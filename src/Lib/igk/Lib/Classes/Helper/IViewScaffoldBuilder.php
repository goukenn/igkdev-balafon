<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewScaffoldBuilder.php
// @date: 20231215 16:34:05
namespace IGK\Helper;


///<summary></summary>
/**
* 
* @package IGK\Helper
*/
interface IViewScaffoldBuilder{
    function initView(string $viewname) : string;
}