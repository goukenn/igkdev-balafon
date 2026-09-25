<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlUniqueIdRefValue.php
// @date: 20260925 19:03:27
namespace IGK\System\Html;


/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
class HtmlUniqueIdRefValue extends HtmlAttributeValue{
    public function getValue($options = null)
    {
        return uniqid();
    }
}