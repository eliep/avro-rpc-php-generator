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

use Example\Protocol\TestProtocolRequestor;

try {
  $requestor = new TestProtocolRequestor('127.0.0.1', 1412);
} catch (Exception $e) {
  echo $e->getMessage()."\n";
  die();
}

$response = $requestor->testSimpleRequestResponse(array("subject" => "ping"));
echo json_encode($response)."\n";
$response = $requestor->testNotification(array("subject" => "ping"));
echo json_encode($response)."\n";

try {
  $response = $requestor->testRequestResponseException(array("cause" => "ping"));
} catch (AvroRemoteException $e) {
  echo json_encode($e->getDatum())."\n";
}
$requestor->close();

