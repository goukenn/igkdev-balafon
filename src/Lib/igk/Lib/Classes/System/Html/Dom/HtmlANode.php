<?php
// @file: IGKHtmlA.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlUtils;  
 

class HtmlANode extends HtmlNode{
    private $m_href, $m_rdef;
    var $domainLink;
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    protected function __AcceptRender($option=null){
        if(!$this->getIsVisible())
            return false;
        if($this["onclick"] == null){
            $bck=$this["href"]->getUri();
            $kr=is_string($bck) ? $bck: HtmlUtils::GetValue($bck);
            if(strpos(trim($kr), "javascript")===0){
                $this["onclick"]=$kr." return false;";
                $this->m_rdef=1;
            }
        }
        return true;
    }
    ///<summary></summary>
    public function __construct(){
        parent::__construct("a");
        $this->m_href=new HtmlAHref($this);
        $this->domainLink=0;
        parent::offsetSet("href", $this->m_href);
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    protected function __RenderComplete($option=null){
        if($this->m_rdef == 1){
            $this["onclick"]=null;
            $this->m_rdef=0;
        }
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="v"></param>
    public function offsetSet($k, $v): void{
        if($k == "href"){
            if($this->m_href !== $v){
                $this->m_href->setValue($v);
                return;
            }
            else
                igk_die("can't set the href to the same value");
        }
        parent::offsetSet($k, $v);
    }
}
