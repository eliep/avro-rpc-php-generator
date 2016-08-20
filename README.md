# Avro RPC Generator

## Installation
Add the following to your composer.json require section 

```
"eliep/avro-rpc-php": "^1.7.7.0"
```

and run
 
```bash
composer install
```


## Usage
The script is located in your vendor/bin folder:
```bash
php vendor/bin/generate.php --help
```

Required arguments:

  * -i <dir> --input <dir>: The folder containing your avro protocol
  * -o <dir> --output <dir>: The folder where the protocol classes will be written
  * -p <namespace> --prefix <namespace>: The namespace prefix of the output directory
  
Optionnal argument:

  * -s --stringType: If the requestor will be used with a java implementation using String instead of CharSequence

## Namespacing



## Example
An example is available in example/ folder.

1. Start a server
```bash
php bin/generate.php sample_rpc_server.php
```

2. Use the sample client
```bash
php bin/generate.php sample_rpc_client.php
```

3. Regenerate the sample client

```bash
php bin/generate.php --input example/avro/ --output example/ --prefix Example --stringType
```
