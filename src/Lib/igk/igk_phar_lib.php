<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_phar_lib.php
// @date: 20220803 13:34:58
// @desc: for phar file

$web = "/index.php";
/**
* Igkphar.
*/
final class IGKPhar
{
	/**
	 * Send HTTP cache headers to the client.
	 *
	 * @param int $second Number of seconds the response should be cached.
	 * @return void
	 */
	public static function Cacheout($second=3600){
		$ts = gmdate("D, d M Y H:i:s", time() + $second) . " GMT";
		header("Expires: {$ts}");
		header("Pragma: cache");
		header("Cache-Control: max-age={$second}, public");
	}
	/**
	 * Check whether a file exists.
	 *
	 * @param string $file Path to the file to check.
	 * @return bool True if the file exists, false otherwise.
	 */
	public static function fileExists($file){
		return igk_io_file_exists($file);
	}
	/**
	 * Return the directory of the currently running Phar archive.
	 *
	 * @return string Directory path of the running Phar.
	 */
	public static function runningDir(){
		return dirname(Phar::running());
	}
}
define("IGK_PHAR_CONTEXT",1);
define("IGK_INDEX_FILE", __FILE__);
define('IGK_APP_DIR', $dir);
define('IGK_NO_TRACELOG',1);
include_once('Lib/igk/igk_framework.php');
define("IGK_MAIN_FILE", igk_uri(PHar::running(false)));
$key = 'phar://handlerequest';
$uri = igk_io_request_uri();
if (!empty($uri) && ($uri!= $web) && ($uri !='/') && !igk_get_env($key)){
    igk_set_env($key, $uri);
    igk_sys_handle_request($uri);
    igk_set_env($key, null);
}
IGKApplication::Boot("phar")->run(__FILE__);