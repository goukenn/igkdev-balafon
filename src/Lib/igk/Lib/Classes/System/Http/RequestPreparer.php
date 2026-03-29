<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestPreparer.php
// @date: 20251211 12:06:41
namespace IGK\System\Http;

use IGK\System\IO\Path;
use IGK\System\Uri;

/**
* 
* @package IGK\System\Http
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Http
*/
class RequestPreparer{

    /**
    * Constant: pkey.
    * @var mixed
    */
    const PKEY = __CLASS__.'//preparefor_request';
    /**
     * prepare request. update $_SERVER and $_REQUEST
     * @param string $path
     * @param ?string $base_uri
     * @return string 
     * 
     */
    public static function PrepareForRequest(string $path, ?string $base_uri = null, ?string $method=null):string{
        $storage =[
            $_SERVER,
            $_REQUEST,
            Request::getInstance()->getQueryInfo()->query_options
        ];
        igk_hook('sys://prepare_for_subrequest', ['storage'=>& $storage]);
        $mkey = self::PKEY;
        igk_push_env($mkey, $storage); 
        $base_uri = $base_uri ?? igk_io_baseuri();
        $g = new Uri(\igk_uri(Path::Combine($base_uri, $path)));
        $path = $g->getPath() ?? '';
        $_SERVER['REQUEST_URI'] = $g->getRequestUri();
        if ($query = $_SERVER['QUERY_STRING'] = $g->getQuery()){
            parse_str($query, $_REQUEST);
        } 
        if ($method){
            $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        }

        igk_server()->prepareServerInfo();
        $uri = igk_getv(parse_url(igk_server()->REQUEST_URI), 'path');
        Request::getInstance()->getQueryInfo()->query_options = (($s = igk_getv(explode(';', $uri, 2), 1)) ? igk_get_query_options($s) : null); 
        return $path;
    }

    /**
    * auto generate doc.
    * @return void
    */

    public static function PopPrepareForRequest(){
        if($storage = igk_pop_env(self::PKEY)){
            list($s,$r,$i) = igk_extract($storage, implode('|', range(0,2)));
            // + | update server info 
            $_SERVER = $s;
            $_REQUEST = $r;
            Request::getInstance()->getQueryInfo()->query_options = $i;
        }

    }
}