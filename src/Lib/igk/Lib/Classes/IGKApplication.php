<?php

/**
 * represent core application
 * @package 
 */
abstract class IGKApplication extends IGKApplicationBase{
    /**
     * 
     * @param string $type 
     * @return IGKApplication a create application
     * @throws TypeError 
     * @throws IGKException 
     */
    public static function Boot($type="web"){        
        $app = IGKApplicationLoader::Boot($type);
        // configure the create application
        return $app;
    }
}