<?php
namespace IGK\System\Html;

///<summary>Represente interface: IIGKHtmlGetValue</summary>
/**
* Represente IIGKHtmlGetValue interface
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