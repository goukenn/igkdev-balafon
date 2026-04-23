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

/**
* Html uri.
* @package IGK\System\Html\Dom
*/
final class HtmlUri extends IGKObject implements IHtmlGetValue{
    /**
    * Property: v.
    * @var mixed
    */
    private $m_v;
    /**
     * Constructor.
     */
    public function __construct(){    }
    /**
     * Returns the URI value, resolving file paths to base URIs when mail options are active.
     *
     * @param mixed $options Optional rendering options.
     * @return mixed The resolved URI string or the raw stored value.
     */
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
    /**
     * Sets the raw URI value.
     *
     * @param mixed $v The URI value to store.
     * @return void
     */
    public function setValue($v){
        $this->m_v=$v;
    }
}