<?php
// @author: C.A.D. BONDJE DOUE
// @file: Application.php
// @date: 20230626 14:41:48
namespace IGK\Framework;
use IGK\ApplicationLoader;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKApp;
use IGKApplicationBase;
use IGKException;
use ReflectionException;
/**
* help as entry point to live with other framework
* @package IGK\Framework
*/
class Application extends IGKApplicationBase{

    /**
    * Runs.
    * @param string $entryfile
    * @param mixed $render
    */
    public function run(string $entryfile, $render = 1) { 
        // do nothing - to integrate with other framework
    }    
    /**
     * bootstrap application 
     * @param mixed $bootoptions 
     * @param null|callable $loader 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public function bootstrap($bootoptions=null, ?callable $loader=null) {    
        IGKApp::Init();
        if($loader){
            $loader();
        }
    }  
}