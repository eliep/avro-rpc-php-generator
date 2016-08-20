<?php

namespace Avro\Generator;

class ProtocolGenerator {
  
  public function generate($input_folder, $output_folder, $namespace_prefix = null, $java_string = false, $apcu = false) {
  
    global $JAVA_STRING_TYPE;
    if ($java_string)
      $JAVA_STRING_TYPE = \AvroSchema::JAVA_STRING_TYPE;
    
    if (file_exists($input_folder)) {
      $path = realpath($input_folder);
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $filename) {
        if (!is_dir($filename) && (($temp = strlen($filename) - strlen("avpr")) >= 0 && strpos($filename, "avpr", $temp) !== FALSE)) {
          try {
  
            $protocol_json = file_get_contents($filename);
            $protocol = \AvroProtocol::parse($protocol_json);
            $helper = new ProtocolHelper($protocol);
  
            $filename = $helper->extractFilename();
            $subdirectory = $helper->extractSubdirectory();
  
            $class = ProtocolClass::build($protocol, $protocol_json, $namespace_prefix, $java_string, $apcu);
            $class->write($output_folder.$subdirectory, $filename);
            
          } catch (\Exception $e) {
            echo "Failed to generate $filename: \n".$e->getMessage()."\n";
          }
        }
      }
    }
  
  }
}