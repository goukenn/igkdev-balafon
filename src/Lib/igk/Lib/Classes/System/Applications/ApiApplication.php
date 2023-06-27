<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApiApplication.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\System\Applications; 
use IGKApp;
use IGKApplicationBase;

/**
 * represent api application entry - 
 * @package 
 */
class ApiApplication extends IGKApplicationBase
{
    /**
     * controller used to initialize the api servivce  
     * @var mixed
     */
    var $controller; 

    public function bootstrap() {       
        $this->library("mysql");
    }
    public function run(string $file, $render=1){   
        $app = IGKApp::RunApiEngine($this, 0);
        // DataAdapter::Register([
        //     "MYSQL"=>\IGK\System\Database\MySQL\DataAdapter::class
        // ]);
        // $this->controller->index();
        $c = igk_do_response($this->controller->index());
        igk_wl($c);
        ob_flush();
        igk_exit();

        // $app = IGKApp::getInstance();
    }
}