<?php
// @author: C.A.D. BONDJE DOUE
// @file: Application.php
// @date: 20230626 14:41:48
namespace IGK\Framework;

use IGK\ApplicationLoader;
use IGKApplicationBase;

///<summary></summary>
/**
* help as entry point to live with other framework
* @package IGK\Framework
*/
class Application extends IGKApplicationBase{
    private function __construct(){
    }
    public function bootstrap() { 
    }

    public function run(string $entryfile, $render = 1) { 
    }

    /**
     * bool framework application 
     * @return static
     */
    public static function Boot(?string $file=null){        
        return ApplicationLoader::Boot('framework')->run();
    }

}