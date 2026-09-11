<?php
// @author: C.A.D. BONDJE DOUE
// @file: ResourceLoader.php
// @date: 20260907 12:24:21
namespace IGK\Core;
/**
* auto generate doc.
* @package IGK\Core
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Core
*/
class ResourceLoader{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const PATH = 'load-resources.php';
    /**
    * auto generate doc.
    * @return void
    */
    static function Access(){
        return self::PATH;
    } 
    /**
     * vhvk og path
     * @return void 
     */
    public static function Check(){
        if (!igk_io_file_exists($h = igk_io_basedir(self::PATH), true)){
            igk_io_w2file($h, file_get_contents(IGK_LIB_DIR.'/Default/load-resources.php'));
        }
    }
}