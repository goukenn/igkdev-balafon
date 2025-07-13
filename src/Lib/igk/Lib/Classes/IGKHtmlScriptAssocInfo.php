<?php
// @file: IGKHtmlScriptAssocInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\IToArray;
final class IGKHtmlScriptAssocInfo implements ArrayAccess, IToArray{
    use IGK\System\Polyfill\ScriptAssocArrayAccessTrait;
    private $data;
    static $sm_initCache, $sm_store;
    public function __construct(){
        $this->data=array();
    }
    public function __serialize(){
        if(self::$sm_store){
            if(!igk_io_file_exists($cache=self::GetCacheFile())){
                if(!($cdata=igk_get_env("sys://res_files"))){
                    $cdata=$this->data;
                }
                $o="<?php\n";
                $o .= IGK_PROTECT_ACCESS;
                foreach($cdata as $k=>$v){
                    $o .= '$data["'.$k.'"]='.$v.';'."\n";
                }
                igk_io_w2file($cache, $o);
            }
            self::$sm_store=0;
        }
        return [];
    }
    public function __unserialize($s){
        return;    }
    public static function GetCacheFile(){
        return igk_dir(igk_io_cachedir()."/.core.scripts.cache");
    }
    protected function store($d){
        self::$sm_store=$d;
    }
    public function to_array():?array{
        return $this->data;
    }
}