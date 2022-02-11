<?php
// @file: IGKHtmlRelativeUriValueAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\Dom\HtmlItemAttribute;

/**
 * represent base dir resolution path resolver. \
 * base_dir/lnk exists resolv it
 * @package 
 */
final class IGKHtmlRelativeUriValueAttribute extends HtmlItemAttribute{
    private $m_lnk;
    ///<summary></summary>
    ///<param name="uri" default="null" type="string"></param>
    public function __construct(string $uri=null){
        $this->m_lnk=$uri;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return $this->getValue(null);
    }
    ///<summary></summary>
    public function getLnk(){
        return $this->m_lnk;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        $lnk=$this->m_lnk;
        if(empty($lnk)){
            $s="";
            if(igk_get_env("sys://error")){
                $s=igk_io_currentbasedomainuri();
            }
            return $s;
        }
        if(defined("IGK_PHAR_CONTEXT")){
            if(IGKPhar::fileExists($lnk)){
                return igk_ajx_link($lnk) ?? igk_html_get_system_uri($lnk);
            }
        }
        if(!($fs=igk_realpath(igk_io_basedir()."/".$this->m_lnk))){
            $fs=$this->m_lnk;
        } 
        return IGKResourceUriResolver::getInstance()->resolve($fs, $options);
    }
}
