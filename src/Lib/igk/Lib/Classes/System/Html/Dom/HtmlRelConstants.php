<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlRelConstants.php
// @date: 20220818 10:58:45
// @desc: 
namespace IGK\System\Html\Dom;

/**
 * store rel value constant 
 * @package 
 */
abstract class HtmlRelConstants {
    /**
    * Constant: no operner.
    * @var mixed
    */
    const NoOperner = "noopener";
    /**
    * Constant: stylesheet.
    * @var mixed
    */
    const Stylesheet = "stylesheet";
    /**
    * Constant: icon.
    * @var mixed
    */
    const Icon = "icon";
    /**
    * .ctr
    * @return
    */
    private function __construct(){
    }
}