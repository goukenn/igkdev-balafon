<?php
// @file: IGKCssStyle.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Css;
use IGK\Controllers\BaseController;
use IGKEvents;
use IGKObject;

/**
* Css style.
* @package IGK\System\Html\Css
*/
final class CssStyle extends IGKObject{
    /**
    * Property: properties.
    * @var mixed
    */
    private $m_properties;
    /**
     * Constructor.
     */
    public function __construct(){
        $this->m_properties=array();
    }
    /**
     * Load and parse CSS properties from a raw CSS string.
     * @param string $v The raw CSS string to parse.
     * @param mixed $level The CSS specificity level.
     * @param mixed $source The source context for theme resolution.
     */
    public function load($v, $level, $source){
        $doc = igk_app()->getDoc();
        $v=igk_css_treat($v, false, $doc->getTheme(), $doc->getSysTheme());
        $tab=igk_str_explode(array(":", ";"), $v);
        for($i=0; $i < igk_count($tab)-1; $i += 2){
            $this->m_properties[trim($tab[$i])]=trim($tab[$i + 1]);
        }
    }
    /**
     * Render the CSS properties as an inline style string.
     * @return string
     */
    public function render(){
        $o="";
        foreach($this->m_properties as $k=>$v){
            $o .= $k.":".$v.";";
        }
        return $o;
    }
}