<?php
// @author: C.A.D. BONDJE DOUE
// @file: ImageLoader.php
// @date: 20230306 16:20:41
namespace IGK\System\Html\IO;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGK\System\Uri;

/**
* auto generate doc.
* @package IGK\System\Html\IO
*/
class ImageLoader{
    /**
    * Path to outdir.
    * @var mixed
    */
    var $outdir;
    /**
    * Flag: hash.
    * @var mixed
    */
    var $hash = "crc32b";
    /**
    * Property: loaded.
    * @var mixed
    */
    private $m_loaded = [];
    /**
    * Called when an object is used as a function.
    * @param mixed $uri
    */
    public function __invoke($uri){ 
        if (isset($this->m_loaded[$uri])){
            Logger::warn("already loaded : ".$uri);
            return;
        }
        Logger::info("load : ".$uri);
        $this->m_loaded[$uri] = 1;
        $match_ext = "#(.)(?P<ext>\.(jpg|jpeg|png))/#";
        if (preg_match($match_ext, $uri, $tab, PREG_OFFSET_CAPTURE)){
            $uri = substr($uri,0,  $tab[0][1] + strlen($tab[0][0]) -1);
        }
        if ($uri && ($content = igk_curl_post_uri($uri))){
            if ( ($c = igk_curl_status())==200){
            $g = new Uri($uri);
            $path = hash($this->hash,  $g->getPath());
            $info = igk_curl_info();
            $mimetype = igk_getv($info, 'Content-Type'); 
            $ext = igk_curl_get_extension($mimetype);
            if (!preg_match("#(\.".ltrim($ext,'.').")$#", $path)){
                $path.= $ext;
            }
                $s = Path::Combine($this->outdir, $path);
                igk_io_w2file($s, $content);
                $this->m_loaded[$uri] = [$s, $mimetype];
                Logger::success($s);
            }else{
                Logger::danger("status : ".$c);
            }
        }else{
            Logger::danger("failed : ");
        }
    }
    /**
    * Loads Content.
    * @param mixed $src
    */
    public function loadContent($src){
        $dv = igk_create_notagnode();
        $dv->load($src);
        array_map($this, array_map(function($n){
            return $n['src'];
        }, $dv->getElementsByTagName("img")));
        array_map($this, array_filter(array_map(function($n){
            if ($src = $n['href']){
                if ($n['as'] == 'image'){
                    return $src;
                }
            }}
        , $dv->getElementsByTagName("link"))));
        return $this->m_loaded;
    }
}