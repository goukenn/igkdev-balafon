<?php
// @file: IGKHtmlScriptAssocInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKHtmlScriptAssocInfo implements ArrayAccess{
    use IGK\System\Polyfill\ScriptAssocArrayAccessTrait;
    private $data;
    static $sm_initCache, $sm_store;
    ///<summary></summary>
    public function __construct(){
        $this->data=array();
    }
    ///<summary></summary>
    public function __serialize(){
        if(self::$sm_store){
            if(!file_exists($cache=self::GetCacheFile())){
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
    ///<summary></summary>
    ///<param name="s"></param>
    public function __unserialize($s){
        return;    }
    ///<summary></summary>
    public static function GetCacheFile(){
        return igk_io_dir(igk_io_cachedir()."/.core.scripts.cache");
    }
   
    ///<summary>Represente store function</summary>
    ///<param name="d"></param>
    protected function store($d){
        self::$sm_store=$d;
    }
    ///<summary></summary>
    public function to_array(){
        return $this->data;
    }
}
