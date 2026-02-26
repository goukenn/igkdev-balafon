<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestCurlCommand.php
// @date: 20241019 21:55:01
namespace IGK\System\Console\Commands\Http;
use IGK\System\Console\AppExecCommand;
/**
* 
* @package IGK\System\Console\Commands\Http
* @author C.A.D. BONDJE DOUE
*/
class RequestCurlCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--request:curl';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='invoque with curl'; 
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'request';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'url';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $url
    */
    public function exec($command, ?string $url=null) { 
		empty($url) && igk_die('missing curl'); 
		if ($g = igk_curl_post_uri($url)){
			$error = igk_curl_lasterror();
			if ($error){
				return -2;
			}
			echo $g;
		} else{
			return -1;
		}
	}
}