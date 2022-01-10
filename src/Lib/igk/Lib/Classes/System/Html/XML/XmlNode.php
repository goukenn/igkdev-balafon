<?php

namespace IGK\System\Html\XML;

use IGK\System\Html\Dom\HtmlItemBase; 

class XmlNode extends HtmlItemBase{
    public function __construct($tagname=null)
    {
        parent::__construct();
        if ($tagname)
            $this->tagname = $tagname;
    }
    public function closeTag()
    {
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