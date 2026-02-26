<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewScaffoldBuilder.php
// @date: 20231215 16:34:05
namespace IGK\Helper;
/**
* 
* @package IGK\Helper
*/
interface IViewScaffoldBuilder{

    /**
    * auto generate doc.
    * @param string $viewname
    * @return string
    */
    function initView(string $viewname) : string;
}