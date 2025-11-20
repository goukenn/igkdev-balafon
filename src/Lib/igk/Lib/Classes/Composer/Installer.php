<?php
// @author: C.A.D. BONDJE DOUE
// @file: Installer.php
// @date: 20251120 16:01:35
namespace IGK\Composer;


/**
* 
* @package IGK\Composer
* @author C.A.D. BONDJE DOUE
*/
class Installer{
    public static function PostInstall(){
        echo 'running post install', PHP_EOL;
        echo 'the current cwd: '.getcwd() , PHP_EOL;
    }
    public static function PostUpdate(){
        echo 'running post update', PHP_EOL;
        echo 'the current cwd: '.getcwd() , PHP_EOL;
    }
}