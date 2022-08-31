<?php
// @file: IGKCssStyle.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Css;

use IGK\Controllers\BaseController;
use IGKEvents;
use IGKObject;

final class CssStyle extends IGKObject{
    private $m_properties;
    ///<summary></summary>
    public function __construct(){
        $this->m_properties=array();
    }
    ///<summary></summary>
    ///<param name="v"></param>
    ///<param name="level"></param>
    ///<param name="source"></param>
    public function Load($v, $level, $source){
        $doc = igk_app()->getDoc();
        $v=igk_css_treat($v, false, $doc->getTheme(), $doc->getSysTheme());
        $tab=igk_str_explode(array(":", ";"), $v);
        for($i=0; $i < igk_count($tab)-1; $i += 2){
            $this->m_properties[trim($tab[$i])]=trim($tab[$i + 1]);
        }
    }
    ///<summary></summary>
    public function render(){
        $o="";
        foreach($this->m_properties as $k=>$v){
            $o .= $k.":".$v.";";
        }
        return $o;
    }
 
}
