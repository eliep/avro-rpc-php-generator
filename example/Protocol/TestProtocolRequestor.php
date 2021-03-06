<?php
namespace Example\Protocol;

/**
 */
class TestProtocolRequestor extends \Requestor {

	/**
	 * @var string
	 */
	private static $json_protocol = <<<PROTO
	{"namespace":"protocol","protocol":"TestProtocol","types":[{"type":"record","name":"SimpleRequest","fields":[{"name":"subject","type":"string"}]},{"type":"record","name":"SimpleResponse","fields":[{"name":"response","type":"string"}]},{"type":"record","name":"Notification","fields":[{"name":"subject","type":"string"}]},{"type":"record","name":"RaiseException","fields":[{"name":"cause","type":"string"}]},{"type":"record","name":"NeverSend","fields":[{"name":"never","type":"string"}]},{"type":"record","name":"AlwaysRaised","fields":[{"name":"exception","type":"string"}]}],"messages":{"testSimpleRequestResponse":{"doc":"Simple Request Response","request":[{"name":"message","type":"SimpleRequest"}],"response":"SimpleResponse"},"testNotNamedResponse":{"doc":"Simple Request Response","request":[{"name":"message","type":"SimpleRequest"}],"response":{"type":"map","values":"string"}},"testNotification":{"doc":"Notification : one-way message","request":[{"name":"notification","type":"Notification"}],"one-way":true},"testRequestResponseException":{"doc":"Request Response with Exception","request":[{"name":"exception","type":"RaiseException"}],"response":"NeverSend","errors":["AlwaysRaised"]}}}
PROTO;

	/**
	 * @var string
	 */
	private $md5string = '8202b17d7047af3c81dc29ea4ae58cc2';

	/**
	 * @var string
	 */
	private $serialized_protocol = 'O:12:"AvroProtocol":7:{s:4:"name";s:12:"TestProtocol";s:9:"namespace";s:8:"protocol";s:3:"doc";N;s:8:"schemata";O:17:"AvroNamedSchemata":1:{s:27:" AvroNamedSchemata schemata";a:6:{s:22:"protocol.SimpleRequest";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:7:"subject";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:13:"SimpleRequest";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:22:"protocol.SimpleRequest";s:24:" AvroName qualified_name";s:13:"SimpleRequest";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}s:23:"protocol.SimpleResponse";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:8:"response";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:14:"SimpleResponse";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:23:"protocol.SimpleResponse";s:24:" AvroName qualified_name";s:14:"SimpleResponse";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}s:21:"protocol.Notification";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:7:"subject";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:12:"Notification";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:21:"protocol.Notification";s:24:" AvroName qualified_name";s:12:"Notification";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}s:23:"protocol.RaiseException";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:5:"cause";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:14:"RaiseException";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:23:"protocol.RaiseException";s:24:" AvroName qualified_name";s:14:"RaiseException";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}s:18:"protocol.NeverSend";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:5:"never";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:9:"NeverSend";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:18:"protocol.NeverSend";s:24:" AvroName qualified_name";s:9:"NeverSend";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}s:21:"protocol.AlwaysRaised";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:9:"exception";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:0;s:4:"type";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:12:"AlwaysRaised";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:21:"protocol.AlwaysRaised";s:24:" AvroName qualified_name";s:12:"AlwaysRaised";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:6:"record";}}}s:8:"messages";a:4:{s:25:"testSimpleRequestResponse";O:19:"AvroProtocolMessage":6:{s:3:"doc";s:23:"Simple Request Response";s:4:"name";s:25:"testSimpleRequestResponse";s:7:"request";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:7:"message";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:1;s:4:"type";r:7;}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:25:"testSimpleRequestResponse";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:34:"protocol.testSimpleRequestResponse";s:24:" AvroName qualified_name";s:25:"testSimpleRequestResponse";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:7:"request";}s:8:"response";r:25;s:6:"errors";O:15:"AvroUnionSchema":3:{s:24:" AvroUnionSchema schemas";a:1:{i:0;O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}s:28:"schema_from_schemata_indices";a:0:{}s:4:"type";s:5:"union";}s:31:" AvroProtocolMessage is_one_way";b:0;}s:20:"testNotNamedResponse";O:19:"AvroProtocolMessage":6:{s:3:"doc";s:23:"Simple Request Response";s:4:"name";s:20:"testNotNamedResponse";s:7:"request";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:7:"message";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:1;s:4:"type";r:7;}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:20:"testNotNamedResponse";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:29:"protocol.testNotNamedResponse";s:24:" AvroName qualified_name";s:20:"testNotNamedResponse";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:7:"request";}s:8:"response";O:13:"AvroMapSchema":3:{s:21:" AvroMapSchema values";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}s:45:" AvroMapSchema is_values_schema_from_schemata";b:0;s:4:"type";s:3:"map";}s:6:"errors";O:15:"AvroUnionSchema":3:{s:24:" AvroUnionSchema schemas";a:1:{i:0;O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}}s:28:"schema_from_schemata_indices";a:0:{}s:4:"type";s:5:"union";}s:31:" AvroProtocolMessage is_one_way";b:0;}s:16:"testNotification";O:19:"AvroProtocolMessage":6:{s:3:"doc";s:30:"Notification : one-way message";s:4:"name";s:16:"testNotification";s:7:"request";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:12:"notification";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:1;s:4:"type";r:43;}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:16:"testNotification";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:25:"protocol.testNotification";s:24:" AvroName qualified_name";s:16:"testNotification";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:7:"request";}s:8:"response";O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:4:"null";}s:6:"errors";N;s:31:" AvroProtocolMessage is_one_way";b:1;}s:28:"testRequestResponseException";O:19:"AvroProtocolMessage":6:{s:3:"doc";s:31:"Request Response with Exception";s:4:"name";s:28:"testRequestResponseException";s:7:"request";O:16:"AvroRecordSchema":5:{s:24:" AvroRecordSchema fields";a:1:{i:0;O:9:"AvroField":6:{s:15:" AvroField name";s:9:"exception";s:22:" AvroField has_default";b:0;s:18:" AvroField default";N;s:16:" AvroField order";N;s:32:" AvroField is_type_from_schemata";b:1;s:4:"type";r:61;}}s:29:" AvroRecordSchema fields_hash";N;s:21:" AvroNamedSchema name";O:8:"AvroName":4:{s:14:" AvroName name";s:28:"testRequestResponseException";s:19:" AvroName namespace";s:8:"protocol";s:18:" AvroName fullname";s:37:"protocol.testRequestResponseException";s:24:" AvroName qualified_name";s:28:"testRequestResponseException";}s:20:" AvroNamedSchema doc";N;s:4:"type";s:7:"request";}s:8:"response";r:79;s:6:"errors";O:15:"AvroUnionSchema":3:{s:24:" AvroUnionSchema schemas";a:2:{i:0;O:19:"AvroPrimitiveSchema":1:{s:4:"type";s:6:"string";}i:1;r:97;}s:28:"schema_from_schemata_indices";a:1:{i:0;i:1;}s:4:"type";s:5:"union";}s:31:" AvroProtocolMessage is_one_way";b:0;}}s:9:"md5string";N;s:8:"protocol";s:12:"TestProtocol";}';

	/**
	 */
	public static function getJsonProtocol() {
		return self::$json_protocol;
	}

	/**
	 * @param string $host Avro RPC server host
	 * @param string $port Avro RPC server port
	 */
	public function __construct($host, $port) {
		global $JAVA_STRING_TYPE; $JAVA_STRING_TYPE = \AvroSchema::JAVA_STRING_TYPE;
		    set_error_handler(function() use ($host, $port) {
		      throw new \Exception("Unable to connect to RPC server $host:$port");
		    });
		    $client = \NettyFramedSocketTransceiver::create($host, $port);
		    restore_error_handler();
		    $protocol = apcu_fetch("AVRO_PROTOCOL"); if ($protocol === false) {$protocol = unserialize($this->serialized_protocol); apcu_store("AVRO_PROTOCOL", $protocol); }
		    $protocol->md5string = $this->md5string;
		    parent::__construct($protocol, $client);
	}

	/**
	 */
	public function close() {
		return $this->transceiver->close();
	}

	/**
	 * @param mixed $notification
	 * @return mixed
	 */
	public function testNotification($notification) {
		return $this->request("testNotification", array('notification' => $notification));
	}

	/**
	 * @param mixed $message
	 * @return mixed
	 */
	public function testNotNamedResponse($message) {
		return $this->request("testNotNamedResponse", array('message' => $message));
	}

	/**
	 * @param mixed $exception
	 * @return mixed
	 */
	public function testRequestResponseException($exception) {
		return $this->request("testRequestResponseException", array('exception' => $exception));
	}

	/**
	 * @param mixed $message
	 * @return mixed
	 */
	public function testSimpleRequestResponse($message) {
		return $this->request("testSimpleRequestResponse", array('message' => $message));
	}
}
