<?php
// @author: C.A.D. BONDJE DOUE
// @file: Application.php
// @date: 20230626 14:41:48
namespace IGK\Framework;

use IGK\ApplicationLoader;
use IGKApp;
use IGKApplicationBase;

///<summary></summary>
/**
* help as entry point to live with other framework
* @package IGK\Framework
*/
class Application extends IGKApplicationBase{

    public function run(string $entryfile, $render = 1) { 
        // do nothing - to integrate with other framework
    }    

    public function bootstrap($bootoptions=null, callable $loader=null) {    
        IGKApp::Init();
        if($loader){
            $loader();
        }
    }  
}