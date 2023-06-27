<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlDomActiveAttribute.php
// @date: 20230417 16:32:04
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
final class HtmlDomActiveAttribute{
    private static $sm_instance;
    public static function getInstance(){
        return self::$sm_instance ?? self::$sm_instance = new static;
    }
}