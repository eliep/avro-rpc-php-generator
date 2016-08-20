<?php

namespace Avro\Generator;


class ProtocolHelper {
  
  /**
   * @var \AvroProtocol
   */
  private $protocol;
  
  public function __construct(\AvroProtocol $protocol) {
    $this->protocol = $protocol;
  }
  
  public function extractNamespace($namespace_prefix = null) {
    $namespace_token = explode(".", $this->protocol->namespace);
    array_walk($namespace_token, function(&$token) { $token = ucfirst($token); });
    return (!is_null($namespace_prefix)) ? $namespace_prefix."\\". implode("\\", $namespace_token) : implode("\\", $namespace_token);
  }
  
  
  public function extractClassname() {
    return $this->protocol->name."Requestor";
  }
  
  
  public function extractFilename() {
    return $this->extractClassname().".php";
  }
  
  
  public function extractSubdirectory() {
    return "/".str_replace("\\", "/", $this->extractNamespace());
  }
}