<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApiController.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Http\WebResponse;
use IGKException;
use ReflectionException;

///<summary> api controller</summary>
/**
 * represent api controller
 * @package IGK\Controllers
 */
abstract class ApiController extends ControllerTypeBase{

    /**
     * default index controller 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function index(){
        //        
        $doc = new HtmlDocumentNode();
        $dv = $doc->getBody()->addBodyBox()->div();
        $dv->h1()->Content = "Api Acontroller";
        $dv->div()->Content = "loaded : ". igk_app()->session->api_count++;
        $response = (new WebResponse($doc));
        $response->cache = igk_app()->getApplication()->options("allow_cache_page");
        $response->output(); 
    }
    public function view(){
        return $this->index(...func_get_args());
    }
}