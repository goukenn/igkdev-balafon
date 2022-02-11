<?php

namespace IGK\Cache;

use Exception;
use IGK\Cache\CommonCache;
use Phar;
use function igk_resources_gets as __;


class SystemFileCache extends CommonCache{
    static $LibFiles;
    ///<summary>override lib cache file</summary>
    /**
     * return 
     * @return string 
     */
    public static function CacheFile(){
        return igk_io_syspath(IGK_FILE_LIB_CACHE);
    }
    ///<summary></summary>
    ///<param name="force" default="false"></param>
    public static function CacheLibFiles($force=false){
        $f=self::CacheFile();
        if(empty($f))
            return;
        $t=3600;
        $expire=time() - $t;
        $el=IGK_LF;
        if(!$force && file_exists($f) && (filemtime($f) > $expire)){
            return;        }
        $data=IGK_STR_EMPTY;
        $dir=IGK_LIB_DIR;
        $rdata=[];
        $src="";
        if(!empty(self::$LibFiles)){
            foreach(self::$LibFiles as $v){
                $v=igk_io_collapse_path($v);
                if(strpos($v, "%lib%") === 0){
                    $src .= "require_once(IGK_LIB_DIR.'/".substr($v, 6)."');".$el;
                }
                else{
                    if(strpos($v, "%project%") === 0){
                        $src .= "require_once(IGK_PROJECT_DIR.'/".substr($v, 10)."');".$el;
                    }
                    else{
                        $data .= '\''.$v.'\','.$el;
                        $rdata[]=$v;
                    }
                }
            }
        }
        else{
            igk_die(__("lib caches is empty."));
        }
        $init='';
        $restore='';
        $phar=igk_phar_available() && strstr(IGK_LIB_DIR, Phar::running());
        if(!$phar){
            $init="";
            $restore="";
        }
        $date=igk_date_now();
        $out=implode("\n", ["<?php", "// Balafon lib cache - auto generate", "// author: C.A.D. BONDJE DOUE", "// date : {$date}", "${init}", "${src}", !empty($data) ? 
        implode("\n", ["foreach([{$data}] as \$k){",
            "    if (file_exists(\$c = igk_io_expand_path(\$k))){", 
            "        require_once(\$c);", "    }", 
            "    else die(\"Cache corruption. File not found: \".\$k);", "}"
            ]): "", "${restore}", ]);
        igk_invalidate_opcache($f);
        igk_io_w2file($f, $out);
        igk_io_w2file(dirname($f)."/.lib.version.cache", IGK_VERSION);
        self::Init_CachedHook();
    }
     ///<summary>Represente CheckLibVersion function</summary>
     public static function CheckLibVersion(){
        return (!file_exists($ver_file=igk_io_cachedir()."/.lib.version.cache")) || (IGK_VERSION != trim(file_get_contents($ver_file)));
    }
   ///<summary></summary>
   public static function LoadCacheLibFiles(){
        $f= self::CacheFile();
        $v=false;
        if(!defined("IGK_NO_CACHE_LIB") && file_exists($f)){
            //+ | clear lib cache

            if(self::CheckLibVersion()){
                unlink($f);
                return $v;
            }
            try {
                include_once($f);
                $v=true;
                igk_get_env_lib_loaded(true);
            }
            catch(Exception $ex){
                igk_ilog("[ ".__FUNCTION__." ]- can't load files...[".$f."]".$ex->getMessage());
                igk_show_exception($ex);
                igk_wln_e("LoadLibError", $ex->getMessage());
            }
        }
        return $v;
    }
    ///<summary>init cache folder hook</summary>
    public static function Init_CachedHook($e=null){
        if(!file_exists($f=igk_io_cachedir()."/.htaccess")){
            igk_io_w2file($f, "deny from all");
        }
    }
}