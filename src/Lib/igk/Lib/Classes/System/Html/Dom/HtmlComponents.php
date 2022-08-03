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
	const Component = "Component";
	const AJXTabControl = "AJXTabControl";

	private function __construct(){
	}

	/**
	 * get paramater attached to controller name
	 */
	public static function GetParam(BaseController $controller, $controllerName, $default =null){

		return $default;
	}
}
