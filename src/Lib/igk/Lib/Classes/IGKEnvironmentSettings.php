<?php
namespace IGK;

use IGKEnvironment;

/**
 * manage defaualt environment setting
 * @package IGK
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
    public function no_page_cache(){
        return defined("IGK_NO_PAGE_CACHE") || $this->no_page_cache;
    }
    public function no_view_cache(){
        return defined("IGK_NO_CACHE_VIEW") || $this->no_view_cache;
    }
}