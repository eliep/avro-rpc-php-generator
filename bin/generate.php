<?php

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

use Avro\Generator\ProtocolGenerator;

$doc = <<<DOC
Generate protocol class from Avro protocol file (.avpr)

Usage:
  generate.php --input <dir> --output <dir> [--prefix <namespace>] [--stringType] [--apcu]
  generate.php (-h | --help)
  generate.php --version

Options:
  -h --help  Show this screen
  -i <dir> --input <dir>               The folder containing your avro protocol
  -o <dir> --output <dir>              The folder where the protocol classes will be written
  -p <namespace> --prefix <namespace>  The namespace prefix of the output directory
  -s --stringType                      If the requestor will be used with a java implementation using String instead of CharSequence
  -a --apcu                            use APCu to cache the parsed protocol.

DOC;

$args = Docopt::handle($doc, array('version' => 'Avro RPC Generator 1.7.7.0'));

$generator  = new ProtocolGenerator();
$generator->generate($args['--input'], $args['--output'], $args['--prefix'], $args['--stringType'], $args['--apcu']);
