<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlHostContextContainer.php
// @date: 20240118 22:06:51
namespace IGK\System\Html;


///<summary></summary>
/**
* element that will host context in rendering children
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlHostContextContainer{
    function getRenderingContextData();
}