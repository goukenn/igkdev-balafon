<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlDomActiveAttribute.php
// @date: 20230417 16:32:04
namespace IGK\System\Html\Dom;
/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlDomActiveAttribute{
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
    * Returns Instance.
    */
    public static function getInstance(){
        return self::$sm_instance ?? self::$sm_instance = new static;
    }
}