<?php
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
