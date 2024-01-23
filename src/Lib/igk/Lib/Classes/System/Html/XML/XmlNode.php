<?php

// @author: C.A.D. BONDJE DOUE
// @filename: XmlNode.php
// @date: 20210101 05:24:43
// @desc: represent xmld node base

namespace IGK\System\Html\XML;

use IGK\System\Html\Dom\HtmlItemBase; 

/**
 * represent xml node base
 * @package IGK\System\Html\XML
 */
class XmlNode extends HtmlItemBase{
    public function __construct($tagname=null)
    {
        parent::__construct();
        if ($tagname)
            $this->tagname = $tagname;
    }
    public function comment(string $text){
        $c = new XmlComment();
        $c->text($text);
        return $this;
    }
    public function closeTag():bool{
        return true;
    }
    public static function CreateWebNode($n, $attributes = null, $indexOrargs = null)
    {
        $g = new self($n);
        if ($attributes){
            $g->setAttributes($attributes);
        }
        return $g;
    }
}