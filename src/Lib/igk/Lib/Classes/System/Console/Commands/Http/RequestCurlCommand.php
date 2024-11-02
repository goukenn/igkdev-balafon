<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestCurlCommand.php
// @date: 20241019 21:55:01
namespace IGK\System\Console\Commands\Http;

use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Http
* @author C.A.D. BONDJE DOUE
*/
class RequestCurlCommand extends AppExecCommand{
	var $command='--request:curl';
	/* var $desc='desc'; */
	/* var $options=[]; */
	/* var $category = ''; */
	var $usage = 'url'; 
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