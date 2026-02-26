<?php
// @file: HtmlNoChildBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
namespace IGK\System\Html\Dom;

/**
* Html no child base.
* @package IGK\System\Html\Dom
*/
abstract class HtmlNoChildBase extends HtmlItemBase{
   public final

    /**
    * Returns Can Add Childs.
    */
    function getCanAddChilds(){
       return false;
   }
}