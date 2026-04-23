<?php
namespace IGK\System\Html\Dom;
use IGKEvents;
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

/**
* Html web component node.
* @package IGK\System\Html\Dom
*/
class HtmlWebComponentNode extends HtmlNode{
    /**
    * .ctr
    * @param mixed $tagname
    */
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }
    /**
    * Sets Component Listener.
    * @param mixed $listener
    * @param null|mixed $param
    */
    public function setComponentListener($listener, $param=null){
    }
    /**
    * Creates Component.
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