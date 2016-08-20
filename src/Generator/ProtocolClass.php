<?php

namespace Avro\Generator;

use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;
use gossi\codegen\generator\CodeFileGenerator;

class ProtocolClass {
  
  /**
   * @var PhpClass
   */
  private $class;
  
  private function __construct(PhpClass $class) {
    $this->class = $class;
  }
  
  public static function build(\AvroProtocol $protocol, $protocol_json, $namespace_prefix, $java_string, $apcu) {
  
    $helper = new ProtocolHelper($protocol);
    
    $full_classname =
      $helper->extractNamespace($namespace_prefix) . '\\' .
      $helper->extractClassname();
    
    $class = new PhpClass($full_classname);
    $class
      ->setParentClassName('\Requestor')
      
      ->setProperty(PhpProperty::create('json_protocol')
        ->setVisibility('private')->setStatic(true)->setType('string')
        ->setExpression("<<<PROTO\n".json_encode(json_decode($protocol_json))."\nPROTO")
      )
      ->setProperty(PhpProperty::create('serialized_protocol')
        ->setVisibility('private')->setType('string')
        ->setExpression("'".serialize($protocol)."'")
      )
      ->setProperty(PhpProperty::create('md5string')
        ->setVisibility('private')->setType('string')
        ->setValue(md5($protocol->__toString()))
      )
      
      ->setMethod( self::buildConstructorMethod($java_string, $apcu))
      
      ->setMethod(PhpMethod::create('getJsonProtocol')
        ->setStatic(true)
        ->setBody('return self::$json_protocol;')
      )
      
      ->setMethod(PhpMethod::create('close')
        ->setStatic(false)
        ->setBody('return $this->transceiver->close();')
      //->setVisibility('public')
      );
    
    $messages = $protocol->messages;
    foreach ($messages as $msg_name => $msg_def) {
      $class->setMethod(self::buildMessageMethod($msg_name, $msg_def));
    }
    
    return new ProtocolClass($class);
  }
  
  /**
   * @param $java_string
   * @param $apcu
   * @return string
   */
  protected function buildConstructorMethod($java_string, $apcu) {
    $string = ($java_string)
      ? 'global $JAVA_STRING_TYPE; $JAVA_STRING_TYPE = \\AvroSchema::JAVA_STRING_TYPE;'
      : '';
    
    $acpu_start = ($apcu)
      ? '$protocol = apcu_fetch("AVRO_PROTOCOL"); if ($protocol === false) {'
      : '';
    
    $acpu_end = ($apcu)
      ? ' apcu_store("AVRO_PROTOCOL", $protocol); }'
      : '';
    
    $body = $string.'
    set_error_handler(function() use ($host, $port) {
      throw new \\Exception("Unable to connect to RPC server $host:$port");
    });
    $client = \\NettyFramedSocketTransceiver::create($host, $port);
    restore_error_handler();
    '.$acpu_start.'$protocol = unserialize($this->serialized_protocol);'.$acpu_end.'
    $protocol->md5string = $this->md5string;
    parent::__construct($protocol, $client);';
    
    return PhpMethod::create('__construct')
      ->addParameter(PhpParameter::create('host')
        ->setType('string')->setDescription('Avro RPC server host'))
      ->addParameter(PhpParameter::create('port')
        ->setType('string')->setDescription('Avro RPC server port'))
      ->setBody($body);
  }
  
  /**
   * @param $msg_name
   * @param $msg_def
   * @return PhpMethod
   */
  protected function buildMessageMethod($msg_name, $msg_def) {
    
    $message_parameters = array();
    $message = PhpMethod::create($msg_name)->setVisibility('public')->setType('mixed');
    foreach ($msg_def->request->fields() as $field) {
      $message_parameters[] = "'".$field->name()."'" . " => $" . $field->name();
      $message->addParameter(PhpParameter::create($field->name()));
    }
    $message->setBody('return $this->request("'.$msg_name.'", array('. implode(', ', $message_parameters).'));');
    
    return $message;
  }
  

  public function write($dir, $filename) {
    echo $dir."\n";
    $generator = new CodeFileGenerator();
    $code = $generator->generate($this->class);
    $code = str_replace("\tPROTO", "PROTO", $code);
  
    if (!file_exists($dir))
      mkdir($dir, 0755, true);
    file_put_contents($dir. "/" . $filename, $code);
  }
}