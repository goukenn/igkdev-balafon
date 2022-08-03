<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IHtmlGetValue.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Html;

///<summary>Represente interface: IHtmlGetValue</summary>
/**
* Represente IHtmlGetValue interface
*/
interface IHtmlGetValue {
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    function getValue($options=null);
}