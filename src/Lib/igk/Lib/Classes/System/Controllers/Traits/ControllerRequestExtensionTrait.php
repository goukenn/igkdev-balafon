<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerRequestExtensionTrait.php
// @date: 20251211 07:43:25
namespace IGK\System\Controllers\Traits;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\Helper\ViewHelper;
use IGK\System\Controllers\IControllerRequestInfo;
use IGK\System\Http\Request;
use IGK\System\Http\RequestPreparer;
/**
* auto generate doc.
* @package IGK\System\Controllers\Traits
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Controllers\Traits
*/
trait ControllerRequestExtensionTrait{
    /**
    * auto generate doc.
    * @param string|IControllerRequestInfo $info
    * @return void
    */
    public static function doRequest(BaseController $ctrl, $info){
        $method= 'GET';
        $isajx = false;
        $path = '';
        if (is_string($info))
            $path = $info;
        else 
        {
            $info = Activator::CreateNewInstance(IControllerRequestInfo::class, $info) ?? igk_die("failed");
            if ($info instanceof IControllerRequestInfo){ 
                $method = $info->method ?? $method;
                $path = $info->request;
                $isajx = $info->isAjx;
            }
        } 
        $v_uri = $ctrl::uri($path,false, false, true);                 
        $path = RequestPreparer::PrepareForRequest($v_uri,null, $method);
        list($view, $args) = ViewHelper::PrepareViewArgFromPath($path);  
        if ($args){
            $view .= '/'.implode("/", $args);
            $args = [];
        }
        $ctrl->setCurrentView($view, true, null, $args); 
        RequestPreparer::PopPrepareForRequest();
        $t = $ctrl->getTargetNode(); 
        if ($isajx){
            return igk_ajx_replace_node($t);
        } 
    }
}