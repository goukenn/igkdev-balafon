<?php
// @author: C.A.D. BONDJE DOUE
// @file: HttpUtility.php
// @date: 20230914 09:48:49
namespace IGK\System\Http;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
abstract class HttpUtility{
    const EXT_MIME_TYPES = [
        "css"=>"text/css",
        "txt"=>"text/plain",
        "html"=>"text/html",
        "js"=>"application/javascript",
        "json"=>"application/json",
        "svg"=>"image/svg+xml",
        "png"=>"image/png",
        "jpg"=>"image/jpg",
        "jpeg"=>"image/jpg",
    ];
    /**
     * resolv string and return mime type
     * @param string $ext 
     * @param string $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetContentTypeFromExtension(string $ext, $default = "text/plain"){
        return igk_getv(self::EXT_MIME_TYPES, $ext, $default);
    }
    /**
     * get extension from mime type
     * @param string $mimetype 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetExtensionFromContentType(string $mimetype){
        return igk_getv([
            'image/png'=>'png',
            'image/jpeg'=>'jpg',
            'image/jpg'=>'jpg',
            'application/json'=>'json',
            'image/svg+xml'=>'svg',            
            'text/css'=>'css',
            'text/javascript'=>'js',
            'text/plain'=>'txt',
            'text/html'=>'html'

        ],$mimetype,'.html');
    }

    /**
     * retrieve base host
     * @param null|string $uri 
     * @return string 
     * @throws IGKException 
     */
    public static function GetBaseHost(?string $uri):string{
        $q = parse_url($uri ?? '/');
        $host = igk_getv($q, 'host');
        if ($host && ($port = igk_getv($q, 'port'))){
            $host.=':'.$port;
        }
        return $host ?? '/';
    }
    public static function GetBaseUri(string $uri){
        $q = parse_url($uri ?? '/');
        $scheme = igk_getv($q, 'scheme', 'http');
        $host = igk_getv($q, 'host');
        if ($host && ($port = igk_getv($q, 'port'))){
            $host.=':'.$port;
        }
        return $scheme."://".$host;
    }
}