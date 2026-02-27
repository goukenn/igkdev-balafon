<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenSelfSignedCertCommand.php
// @date: 20230403 14:52:31
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class GenSelfSignedCertCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--genself-signed-cert';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='create a self signed certificate'; 
	/* var $options=[]; */

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'tools';

    /**
    * Exec.
    * @param mixed $command
    * @param string $public
    * @param string $private
    */
    public function exec($command, string $public='public.crt', string $private='server_key.crt'){ 
		Logger::info('generate private key');
		$o = `openssl genrsa -aes256 -out keyfile.crt 2048 2>&2 1>&2`;
		Logger::print($o);
		Logger::info('generate public key');
		$o = `openssl rsa -in keyfile.crt -out {$public} 2>&2 1>&2`;
		Logger::print($o);
		Logger::info('generate server key');
		$o = `openssl req -new -x509 -nodes -sha256 -key {$public}  -out {$private} 2>&2 1>&2`;
		Logger::print($o);
		Logger::info('done');
	}
}