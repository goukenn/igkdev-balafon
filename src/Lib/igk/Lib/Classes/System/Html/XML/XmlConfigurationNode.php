<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XmlConfigurationNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\XML;

use IGK\System\Html\HtmlUtils;

class XmlConfigurationNode extends XmlNode{
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }
    /**
     * override from copy
     * @param mixed $n 
     * @param mixed $attributes 
     * @param mixed $indexOrargs 
     * @return $this 
     */
    public function add($n, $attributes = null, $indexOrargs = null){
        if (!($n instanceof self))
        {
            if (is_string($n)){
                $n = self::CreateWebNode($n, $attributes, $indexOrargs);
                parent::add($n);
                return $n;
            }
            else {
                if (!empty($t = $n->getTagName()))
                {
                    $g = new self($t);
                    $g->setAttributes($n->getAttributes()->to_array());
                    $childs = $n->childs->to_array();
                    parent::add($g);
                    // copy children
                    HtmlUtils::CopyNode($g, $childs, function($n){
                        return new self($n);
                    });                  
                    return $g;
                }                
            }

            /** */
        }
        return parent::add($n, $attributes, $indexOrargs);
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
        //  
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
        }
        return $s;

    }
}