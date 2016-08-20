<?php

namespace Avro\Generator;

class ProtocolGenerator {
  
    private $protocol_tpl =<<< PT
<?php

namespace PT_NAMESPACE;

class PT_CLASSNAME extends \Requestor {
  
  private static \$json_protocol =
PT_JSON
  
  public function __construct(\$host, \$port) {
    PT_JAVA_STRING
    
    set_error_handler(function() use (\$host, \$port) {
      throw new \Exception("Unable to connect to RPC server \$host:\$port");
    });
    \$client = \NettyFramedSocketTransceiver::create(\$host, \$port);
    restore_error_handler();
    
    PT_APCU_START
      \$protocol = \AvroProtocol::parse(self::\$json_protocol);
PT_APCU_END
    
    parent::__construct(\$protocol, \$client);
    \$protocol = unserialize(\$this->serialized_protocol);
    \$protocol->md5string = \$this->md5string;
    parent::__construct(\$protocol, \$client);
  }
  
  public static function getJsonProtocol() { return self::\$json_protocol; }
  
  public function close() { return \$this->transceiver->close(); }
  
PT_CLIENT_FUNCTIONS

  private \$serialized_protocol = 'PT_PROTOCOL_SERIALIZED';
  private \$md5string = 'PT_MD5_STRING';
}
PT;

    private $client_function_tpl = <<<CFT
    
  public function CFT_NAME(CFT_PARAMS) {
    return \$this->request('CFT_NAME', array(CFT_ASSOC_PARAMS));
  }
    
CFT;

    private $java_string_tpl = <<<JST

    global \$JAVA_STRING_TYPE;
    \$JAVA_STRING_TYPE = \AvroSchema::JAVA_STRING_TYPE;
    
JST;
  
    private $apcu_start_tpl = <<<AST

    \$protocol = apcu_fetch("AVRO_PROTOCOL");
    if (\$protocol == false) {
AST;
  
    private $apcu_end_tpl = <<<AET
      apcu_store("AVRO_PROTOCOL", \$protocol);
    }
AET;
  
  
  
  public function generates($input_folder, $output_folder, $namespace_prefix = null, $java_string = false, $apcu = false) {
    if (file_exists($input_folder)) {
      $path = realpath($input_folder);
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $filename) {
        if (!is_dir($filename) && (($temp = strlen($filename) - strlen("avpr")) >= 0 && strpos($filename, "avpr", $temp) !== FALSE)) {
          try {
            $this->write($filename, $output_folder, $namespace_prefix, $java_string, $apcu);
          } catch (Exception $e) {
            echo "Failed to generate $filename: \n".$e->getMessage()."\n";
          }
        }
      }
    }
  }
  
  public function write($input_filename, $output_folder, $namespace_prefix, $java_string, $apcu) {
    $protocol_json = file_get_contents($input_filename);
    $protocol = \AvroProtocol::parse($protocol_json);
    
    $working_tpl = $this->protocol_tpl;
    $filename = $this->getFilename($protocol);
    $subdirectory = $this->getSubdirectory($protocol);
    $working_tpl = $this->generate($protocol, $protocol_json, $namespace_prefix, $working_tpl, $java_string, $apcu);
    
    if (!file_exists($output_folder.$subdirectory))
      mkdir($output_folder.$subdirectory, 0755, true);
    file_put_contents($output_folder.$subdirectory."/".$filename, $working_tpl);
  }
  
  public function generate($protocol, $protocol_json, $namespace_prefix, $working_tpl, $java_string, $apcu) {
    global $JAVA_STRING_TYPE;
    if ($java_string)
      $JAVA_STRING_TYPE = \AvroSchema::JAVA_STRING_TYPE;
    
    $working_tpl = str_replace("PT_PROTOCOL_SERIALIZED", serialize($protocol), $working_tpl);
    $working_tpl = str_replace("PT_MD5_STRING", md5($protocol->__toString()), $working_tpl);

    $namespace = $this->getNamespace($protocol, $namespace_prefix);
    $working_tpl = str_replace("PT_NAMESPACE", $namespace, $working_tpl);
    
    $classname = $this->getClassname($protocol);
    $working_tpl = str_replace("PT_CLASSNAME", $classname, $working_tpl);
    
    $json = $this->getJson($protocol_json);
    $working_tpl = str_replace("PT_JSON", $json, $working_tpl);

    $working_tpl = str_replace("PT_JAVA_STRING", ($java_string) ? $this->java_string_tpl : "", $working_tpl);
    
    $working_tpl = str_replace("PT_APCU_START", ($apcu) ? $this->apcu_start_tpl : "", $working_tpl);
    $working_tpl = str_replace("PT_APCU_END", ($apcu) ? $this->apcu_end_tpl : "", $working_tpl);
  
    if ($protocol == false) {
      apcu_store("AVRO_PROTOCOL", $protocol);
    }
    
    $client_functions = $this->getClientFunctions($protocol);
    $working_tpl= str_replace("PT_CLIENT_FUNCTIONS", $client_functions, $working_tpl);
    
    return $working_tpl;
  }
  
  public function getNamespace($protocol, $namespace_prefix = null) {
    $namespace_token = explode(".", $protocol->namespace);
    array_walk($namespace_token, function(&$token) { $token = ucfirst($token); });
    return (!is_null($namespace_prefix)) ? $namespace_prefix."\\". implode("\\", $namespace_token) : implode("\\", $namespace_token);
  }
  
  public function getClassname($protocol) {
    return $protocol->name."Requestor";
  }
  
  public function getFilename($protocol) {
    return $this->getClassname($protocol).".php";
  }
  
  public function getSubdirectory($protocol) {
    return "/".str_replace("\\", "/", $this->getNamespace($protocol));
  }
  
  
  public function getJson($protocol_json) {
    return "<<<PROTO\n".json_encode(json_decode($protocol_json))."\nPROTO;\n";
  }
  
  public function getClientFunctions($protocol) {
    $client_functions = array();
    
    
    $messages = $protocol->messages;
    foreach ($messages as $msg_name => $msg_def) {
      $working_tpl = $this->client_function_tpl;
    
      $client_functions[$msg_name] = array();
      $client_functions_assoc[$msg_name] = array();
      foreach ($msg_def->request->fields() as $field) {
        //echo $field->name()."/".$field->type(). "\n";
        $client_functions[$msg_name][] = "$".$field->name();
        $client_functions_assoc[$msg_name][] = "'".$field->name()."'" . " => $" . $field->name();
      }
      $client_functions[$msg_name] = str_replace("CFT_PARAMS", implode(", ", $client_functions[$msg_name]), $working_tpl);
      $client_functions[$msg_name] = str_replace("CFT_ASSOC_PARAMS", implode(", ", $client_functions_assoc[$msg_name]), $client_functions[$msg_name]);
      $client_functions[$msg_name] = str_replace("CFT_NAME", $msg_name, $client_functions[$msg_name]);
      //echo $msg_def->response->fullname()."/".$msg_def->response->qualified_name()."/".$msg_def->response->type()."\n";
    }
    return implode("\n", $client_functions);
  }
}
/*

<?php

namespace Avro\Examples\Protocol\Fr\V3d\Avro;

use Avro\RPC\RpcProtocol;

class AsvProtocol extends RpcProtocol {
  
  private $jsonProtocol =
  <<<PROTO
{"protocol": "ASV",
 "namespace": "fr.v3d.avro",

 "types": [
     {"type": "record", "name": "Asv",
      "fields": [
          {"name": "a",   "type": "int"},
          {"name": "s", "type": "string"},
          {"name": "v", "type": "string"}
      ]
     },
     {"type": "record", "name": "Attention",
      "fields": [
          {"name": "status",   "type": "string"}
      ]
     }
     
 ],

 "messages": {
     "send": {
         "request": [{"name": "message", "type": "Asv"}],
         "response": "Attention"
     }
 }
}
PROTO;

  public function getJsonProtocol() {
    return $this->jsonProtocol;
  }
  
  public function send($message) {
    return $this->genericRequest(array($message));
  }
  
  public function sendImpl($callback) {
    $this->genericResponse($callback);
  }
  
}
*/