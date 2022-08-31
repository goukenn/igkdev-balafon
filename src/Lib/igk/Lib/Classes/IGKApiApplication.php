<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApiApplication.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\System\Http\RequestHandler;
use IGK\Helper\StringUtility;
use IGK\System\Database\MySQL\DataAdapter;

/**
 * represent api application entry - 
 * @package 
 */
class IGKApiApplication extends IGKApplicationBase
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
        //     "MYSQL"=>\IGK\System\DataBase\MySQL\DataAdapter::class
        // ]);
        // $this->controller->index();
        $c = igk_do_response($this->controller->index());
        igk_wl($c);
        ob_flush();
        igk_exit();

        // $app = IGKApp::getInstance();
    }
}