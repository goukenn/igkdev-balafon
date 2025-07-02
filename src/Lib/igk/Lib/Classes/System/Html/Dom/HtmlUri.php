<?php
// @file: IGKHtmlUri.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGK\System\Html\IHtmlGetValue;
use IGKObject;
use IGKValidator;

final class HtmlUri extends IGKObject implements IHtmlGetValue{
    private $m_v;
    public function __construct(){    }
    public function getValue($options=null){
        $bck=$this->m_v;
        if($options && igk_xml_is_mailoptions($options)){
            if(!IGKValidator::IsUri($bck)){
                $tab=explode('?', $bck);
                $cf=igk_getv($tab, 0);
                if(!empty($cf) && igk_io_file_exists($cf)){
                    $f=igk_io_baseuri(igk_realpath($cf));
                    $t=array_slice($tab, 1);
                    if(igk_count($t) > 0)
                        $f .= "?".igk_str_join_tab($t, '?', false);
                    return $f;
                }
            }
            else{
                if(strpos($bck, "?")===0){
                    return igk_io_baseuri().$bck;
                }
            }
        }
        return $this->m_v;
    }
    public function setValue($v){
        $this->m_v=$v;
    }
}
