# Avro RPC Generator

See [Avro](http://avro.apache.org/) for a full documentation on Avro and its 
usage in PHP.

This library use [avro-rpc-php](https://github.com/eliep/avro-rpc-php) as an implementation
of the Avro RPC protocol in php.


## Installation
Add the following to your composer.json require section 

```
"eliep/avro-rpc-php": "^1.7.7.0"
```

and run
 
```bash
composer install
```


## Protocol class Generation
The script is located in your vendor/bin folder:
```bash
php vendor/bin/generate.php --help
```

Required arguments:

  * --input  (-i) _dir_ : The folder containing your Avro protocol
  * --output (-o) _dir_ : The folder where the protocol classes will be written
  
Optional argument:

  * --prefix (-p) _namespace_: The namespace prefix of the output directory
  * --stringType (-s) : If the requestor will be used with a java implementation using String instead of CharSequence

## Namespacing
The generate script will respect the namespace defined in your avro protocol. For example,
if you have define "my.avro" as your protocol namespace, the script:
  - create the folder `My\Avro` in the directory specified by the -o option.
  - use My\Avro for the generated php class.
  
Note: If the directory specified by the -o option has a namespace, 
you can use the option -p to specify it so that the namespace
of the generated php class will use it as a prefix

The name of the generate class will be protocol name with the `Requestor` suffix 
(if your protocol's name is `Protocol`, the class name will be `ProtocolRequestor`);

## Protocol class Usage
If your protocol namespace is `My\Avro` and your protocol name is `Protocol`,
you can connect to an Avro RPC server with

```php
use My\Avro\ProtocolRequestor

$serverHost = '127.0.0.1';
$serverPort = 1412;
$requestor = new ProtocolRequestor($serverHost, $serverPort);
```

The `ProtocolRequestor` class contains one function for each message in your protocol.
These functions accept as many argument as defined by the corresponding message.

For example, if your protocol define a message like:
```json
{
 "namespace": "protocol",
 "protocol": "TestProtocol",

 "types": [
     {"type": "record", "name": "SimpleRequest",
      "fields": [{"name": "subject",   "type": "string"}]
     },
     {"type": "record", "name": "SimpleResponse",
      "fields": [{"name": "something",   "type": "string"}]
     }
 ],

 "messages": {
    "requestSomething": {
      "request": [{"name": "message", "type": "SimpleRequest"}],
      "response": "SimpleResponse"
    }
  }
}
```
You can call:

```php
$response = $requestor->requestSomething(array("subject" => "ping"));
```

## Example
An example is available in example/ folder.

  - Start a server
```bash
php bin/generate.php sample_rpc_server.php

```
  - Use the sample client
```bash
php bin/generate.php sample_rpc_client.php
```

  - Regenerate the sample client
```bash
php bin/generate.php --input example/avro/ --output example/ --prefix Example --stringType
```
