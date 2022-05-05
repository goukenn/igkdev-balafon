<?php
namespace IGK;

use IGKEnvironment;

/**
 * manage defaualt environment setting
 * @package IGK
 * @property bool $no_init_controller in bootstrap disable the init controller behaviour
 */
class IGKEnvironmentSettings{
    private static $sm_instance;

    public static function getInstance(){
        if (self::$sm_instance === null){
            self::$sm_instance = new self();
        }
        return self::$sm_instance; 
    }
    private function __function(){        
    }
    public function __get($name){
        return null;
    }

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