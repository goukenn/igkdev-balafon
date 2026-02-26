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

/**
* Igkhtml script assoc info.
*/
final class IGKHtmlScriptAssocInfo implements ArrayAccess, IToArray{
    use IGK\System\Polyfill\ScriptAssocArrayAccessTrait;

    /**
    * Property: data.
    * @var mixed
    */
    private $data;

    /**
    * Properties: init cache, store.
    * @var mixed
    */
    static $sm_initCache, $sm_store;
    /**
     * Constructor.
     */

    public function __construct(){
        $this->data=array();
    }
    /**
     * Serializes the instance and optionally writes the script cache file.
     *
     * @return array
     */

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
    /**
     * Restores the instance from serialized data.
     *
     * @param array $s Serialized data array.
     * @return void
     */

    public function __unserialize($s){
        return;    }
    /**
     * Returns the path to the core scripts cache file.
     *
     * @return string
     */

    public static function GetCacheFile(){
        return igk_dir(igk_io_cachedir()."/.core.scripts.cache");
    }
    /**
     * Marks the store flag to trigger cache writing on serialization.
     *
     * @param mixed $d The value to store as the flag.
     * @return void
     */

    protected function store($d){
        self::$sm_store=$d;
    }
    /**
     * Returns the internal data array.
     *
     * @return array|null
     */

    public function to_array():?array{
        return $this->data;
    }
}