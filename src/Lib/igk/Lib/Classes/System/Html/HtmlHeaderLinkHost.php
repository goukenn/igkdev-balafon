<?php
// @file: IGKHtmlHeaderLinkHost.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

final class HtmlHeaderLinkHost{
    private $m_list;
    var $privateLink, $sharedLink;
    ///<summary></summary>
    public function __construct(){
        $this->m_list=array();
        $this->privateLink=array();
        $this->sharedLink=array();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="node"></param>
    ///<param name="temp"></param>
    public function add($name, $node, $temp){
        if(isset($this->m_list[$name]))
            igk_die("link already referenced");
        $this->m_list[$name]=$node;
        if($temp){
            $this->m_privateLink[$name]=$node;
        }
    }
    ///<summary></summary>
    public function clearChilds(){
        foreach($this->m_list as $v){
            igk_html_rm($v);
        }
        $this->m_list=array();
        $this->privateLink=array();
        $this->sharedLink=array();
    }
    ///<summary></summary>
    ///<param name="o"></param>
    public function getLink($o){
        return igk_getv($this->m_list, $o);
    }
}
