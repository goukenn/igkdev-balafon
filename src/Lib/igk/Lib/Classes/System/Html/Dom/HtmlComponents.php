<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlComponents.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Controllers\BaseController;

/**
 * represent exportable component
 * @package 
 */
abstract class HtmlComponents {
    /**
    * Constant: component.
    * @var mixed
    */
    const Component = "Component";
    /**
    * Constant: ajxtab control.
    * @var mixed
    */
    const AJXTabControl = "AJXTabControl";
    /**
    * .ctr
    * @return mixed
    */
    private function __construct(){
	}
    /**
    * get paramater attached to controller name
    * @param BaseController $controller
    * @param mixed $controllerName
    * @param mixed $default
    */
    public static function GetParam(BaseController $controller, $controllerName, $default =null){
		return $default;
	}
}