<?php

/**
 * reprensent core application
 * @package 
 */
abstract class IGKApplication extends IGKApplicationBase{
    public static function Boot($type="web"){
        $app = IGKApplicationLoader::Boot($type);
 
        return $app;
    }
}