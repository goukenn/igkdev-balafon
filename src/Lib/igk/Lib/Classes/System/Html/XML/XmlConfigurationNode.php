<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XmlConfigurationNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\XML;
use IGK\System\Configuration\SysAppConfigExpression;
use IGK\System\Configuration\SysConfigExpression;
use IGK\System\Html\HtmlUtils;

/**
 * configuration node
 * @package IGK\System\Html\XML
 */
class XmlConfigurationNode extends XmlNode{
    /**
    * Constant: sys config.
    * @var mixed
    */
    const SYS_CONFIG = 'sys';
    /**
    * Constant: app config.
    * @var mixed
    */
    const APP_CONFIG = 'app';
    /**
    * .ctr
    * @param mixed $tagname
    */
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
                    HtmlUtils::CopyNode($g, $childs, function($n){
                        return new self($n);
                    });                  
                    return $g;
                }                
            }
            /**
            * auto generate doc.
            */
        }
        return parent::add($n, $attributes, $indexOrargs);
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @param null|mixed $attributes
    * @param null|mixed $indexOrargs
    * @return
    */
    public static function CreateWebNode($name, $attributes = null, $indexOrargs = null)
    {
        $g = new self($name);
        if (is_array($attributes)){
            $g->setAttributes($attributes);
        }
        return $g; 
    }
    /**
    * auto generate doc.
    * @return SysAppConfigExpression|SysConfigExpression|null|object|string
    */
    public function getInnerHtml()
    {
        $s = trim(parent::getInnerHtml());     
        $gps = \IGK\System\Configuration\SysConfigExpressionFactory::GetRegisterRegex();
        if (!empty($s) && preg_match("/\{\{(?P<exp>\s*((?P<name>$gps)\.)?.+)\}\}/i", $s, $tab)){
            $m = trim($tab["exp"]);
            $n = igk_getv($tab, 'name');
            switch($n){
                case self::APP_CONFIG:
                    return new \IGK\System\Configuration\SysAppConfigExpression(substr($m , 4));
                    break;
                case self::SYS_CONFIG:
                    return new \IGK\System\Configuration\SysConfigExpression(substr($m , 4));
                default:
                    if (empty($n)){
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