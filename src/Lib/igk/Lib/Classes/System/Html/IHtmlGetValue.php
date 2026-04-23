<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlGetValue.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Html;

/**
* Represent IHtmlGetValue interface
*/
interface IHtmlGetValue {
    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    function getValue($options=null);
}