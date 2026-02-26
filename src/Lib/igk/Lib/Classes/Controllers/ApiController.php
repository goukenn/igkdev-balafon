<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApiController.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
use IGK\System\Applications\WebApplication;
use IGK\System\Exceptions\ArgumentTypeNotValidException; 
use IGKException;
use ReflectionException;
/**
 * represent api controller
 * @package IGK\Controllers
 */
abstract class ApiController extends ControllerTypeBase{
    // use ApplicationUserProfileTrait;

    /**
    * Bootstrap.
    * @param mixed $app
    */
    protected function bootstrap($app){
        WebApplication::InitWebAppLibrary($app); 
    }
    /**
     * default index controller 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    // public function index(){
    //     //        
    //      $doc = IGKHtmlDoc::CreateDoc();new HtmlDocumentNode();
    //     // $dv = $doc->getBody()->addBodyBox()->div();
    //     // $dv->h1()->Content = "Api Acontroller";
    //     // $dv->div()->Content = "loaded : ". igk_app()->session->api_count++;
    //     $response = new WebResponse($doc);
    //     $response->cache = igk_app()->getApplication()->options("allow_cache_page");
    //     $response->output(); 
    // }
    /**
     * invoke base controller
     */
    // public function View():BaseController{
    //     $this->index(...func_get_args());
    //     return $this;
    // }
}