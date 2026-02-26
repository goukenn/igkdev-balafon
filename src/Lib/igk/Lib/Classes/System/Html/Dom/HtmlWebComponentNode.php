<?php
namespace IGK\System\Html\Dom;
use IGKEvents;
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlWebComponentNode extends HtmlNode{    
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }

    /**
    * auto generate doc.
    * @param mixed $listener
    * @param null|mixed $param
    */
    public function setComponentListener($listener, $param=null){
    }

    /**
    * auto generate doc.
    * @param mixed $name
    */
    public static function CreateComponent($name){
        $c = self::CreateWebNode($name);
        if ($c instanceof self){
            return $c;
        } 
        return null;
    }
} 