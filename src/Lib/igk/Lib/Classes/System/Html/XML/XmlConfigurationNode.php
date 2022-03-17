<?php

namespace IGK\System\Html\XML;


class XmlConfigurationNode extends XmlNode{
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }
    public static function CreateWebNode($name, $attributes = null, $indexOrargs = null)
    {
        $g = new self($name);
        if (is_array($attributes)){
            $g->setAttributes($attributes);
        }
        return $g; 
    }
   
    public function getInnerHtml()
    {
        $s = trim(parent::getInnerHtml());     
        $gps = \IGK\System\Configuration\SysConfigExpressionFactory::GetRegisterRegex();
        // if (!empty($s) && preg_match("/\{\{(?P<exp>.+)\}\}/i", $s, $tab)){
        if (!empty($s) && preg_match("/\{\{(?P<exp>\s*((?P<name>$gps)\.)?.+)\}\}/i", $s, $tab)){
            $m = trim($tab["exp"]);
            switch($tab["name"]){
                case "app":
                    return new \IGK\System\Configuration\SysAppConfigExpression(substr($m , 4));
                    break;
                case "sys":
                    return new \IGK\System\Configuration\SysConfigExpression(substr($m , 4));
                default:
                    if (empty($tab["name"])){
                        return null;
                    }
                    if ($c = \IGK\System\Configuration\SysConfigExpressionFactory::Create($tab["name"], $m)){
                        return $c;
                    }
                    break;
            }
            // // if (strpos($m, "sys.") === 0){                
            // //     return new \IGK\System\Configuration\SysConfigExpression(substr($m , 4));
            // // } 
            // igk_trace();
            // var_dump($tab);
            // igk_wln_e("Configuration : express not resoled", $m);
        }
        return $s;

    }
}