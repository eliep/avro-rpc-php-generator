<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once __DIR__ . "/../vendor/autoload.php";

$protocol = file_get_contents(__DIR__."/avro/test_protocol.avpr");

class TestProtocolResponder extends Responder {
  public function invoke( $local_message, $request) {
    echo $local_message->name.":".json_encode($request)."\n";
    switch ($local_message->name) {
      
      case "testSimpleRequestResponse":
        if ($request["message"]["subject"] == "ping")
          return array("response" => "pong");
        else if ($request["message"]["subject"] == "pong")
          return array("response" => "ping");
        break;
      
      case "testNotNamedResponse":
        return array("one" => "1", "two" => "2");
        break;
      
      case "testNotification":
        break;
      
      case "testRequestResponseException":
        if ($request["exception"]["cause"] == "callback")
          throw new AvroRemoteException(array("exception" => "raised on callback cause"));
        else
          throw new AvroRemoteException("System exception");
        break;
      
      default:
        throw new AvroRemoteException("Method unknown");
    }
  }
}

$server = new SocketServer('127.0.0.1', 1412, new TestProtocolResponder(AvroProtocol::parse($protocol)), true);
$server->start();

