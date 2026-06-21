<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKEnvironmentSettings.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK;
use IGKEnvironment;

/**
 * manage defaualt environment setting
 * @package IGK
 * @property bool $no_init_controller in bootstrap disable the init controller behaviour
 */
class EnvironmentSettings{
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
    * Cache: no page cache.
    * @var mixed
    */
    var $no_page_cache;
    /**
    * Returns Instance.
    */
    public static function getInstance(){
        if (self::$sm_instance === null){
            self::$sm_instance = new self();
        }
        return self::$sm_instance; 
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    private function __function(){        
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        return null;
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $args
    */
    public function __call($name, $args){
        return IGKEnvironment::getInstance()->$name;
    }
    /**
     * no use page cache
     * @return bool 
     */
    public function no_page_cache(){
        return defined("IGK_NO_PAGE_CACHE") || $this->no_page_cache;
    }
    /**
     * no use view cache
     * @return bool 
     */
    public function no_view_cache(){
        return defined("IGK_NO_VIEW_CACHE") || $this->no_view_cache;
    }
}