<?php

namespace IGK\System\Html\XML;


class XmlConfigurationNode extends XmlNode{
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }
    public static function CreateWebNode($n, $attributes = null, $indexOrargs = null)
    {
        die(__METHOD__);
    }
    public static function CreateElement($name, $param = null)
    {
        $g = new self($name);
        if (is_array($param)){
            $g->setAttributes($param);
        }
        return $g; 
    }
    public function getInnerHtml()
    {
        $s = trim(parent::getInnerHtml());
        if (!empty($s) && preg_match("/\{\{(?P<exp>.+)\}\}/i", $s, $tab)){
            $m = trim($tab["exp"]);
            if (strpos($m, "sys.") === 0){                
                return new \IGK\System\Configuration\SysConfigExpression(substr($m , 4));
            } 
            igk_wln_e("dkjsf");
        }
        return $s;

    }
}