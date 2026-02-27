<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewScaffoldBuilder.php
// @date: 20231215 16:34:05
namespace IGK\Helper;

/**
* auto generate doc.
* @package IGK\Helper
*/
interface IViewScaffoldBuilder{

    /**
    * Initializes View.
    * @param string $viewname
    * @return string
    */
    function initView(string $viewname) : string;
}