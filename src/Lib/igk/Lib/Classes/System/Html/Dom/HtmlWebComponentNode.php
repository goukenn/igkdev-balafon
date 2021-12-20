<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlWebComponentNode extends HtmlNode{    
  
    public function __construct($tagname)
    {
        parent::__construct($tagname);
        
    }
    public function setComponentListener($listener, $param=null){

    }
    public static function CreateComponent($name){
        $c = self::CreateWebNode($name);
        if ($c instanceof self){
            return $c;
        } 
        return null;
    }
} 