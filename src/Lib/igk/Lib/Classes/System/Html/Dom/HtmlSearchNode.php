<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlSearchNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Html\HtmlUtils;
use IGK\ValueListener;
/**
* Represent HtmlSearchNode class
*/
final class HtmlSearchNode extends HtmlNode {
    /**
    * Property: ajx.
    * @var mixed
    */
    private $m_AJX;
    /**
    * Identifier: target id.
    * @var mixed
    */
    private $m_TargetId;
    /**
    * Property: ajxfunc.
    * @var mixed
    */
    private $m_ajxfunc;
    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;
    /**
    * Property: frm.
    * @var mixed
    */
    private $m_frm;
    /**
    * Property: input.
    * @var mixed
    */
    private $m_input;
    /**
    * Property: link.
    * @var mixed
    */
    private $m_link;
    /**
    * Property: method.
    * @var mixed
    */
    private $m_method;
    /**
    * Property: prop.
    * @var mixed
    */
    private $m_prop;
    /**
    * Property: search.
    * @var mixed
    */
    private $m_search;
    /**
    * Property: uri.
    * @var mixed
    */
    private $m_uri;
    /**
    * auto generate doc.
    * @param mixed $target the default value is null
    */
    public function __construct($uri=null, $search=null, $prop="q", $ajx=0, $target=null){
        parent::__construct("div");
        $this["class"]="clsearch search_fcl";
        $this->m_AJX=$ajx;
        $this->m_method="POST";
        $this->m_uri=$uri;
        $this->m_frm=$this->addForm();
        $this->m_prop=$prop;
        $this->m_search=$search;
        $this->m_TargetId=$target;
        $this->initView();
    }
    /**
    * auto generate doc.
    */
    public function getAJX(){
        return $this->m_AJX;
    }
    /**
    * auto generate doc.
    */
    public function getMethod(){
        return $this->m_method;
    }
    /**
    * auto generate doc.
    */
    public function getTargetId(){
        return $this->m_TargetId;
    }
    /**
    * auto generate doc.
    */
    public function getUri(){
        return $this->m_uri;
    }
    /**
    * auto generate doc.
    */
    public function initView(){
        $uri=$this->m_uri;
        $tab=igk_getquery_args($uri);
        if(isset($tab["c"])){
            $this->m_ctrl=igk_getctrl($tab["c"]);
            $f=igk_getv($tab, "f");
            $this->m_ajxfunc=null;
            if($f){
                $f=str_replace("-", "_", $f);
                if(!IGKString::EndWith($f, "_ajx")){
                    $f=$f."_ajx";
                    if(method_exists($this->m_ctrl, $f)){
                        $this->m_ajxfunc=$this->m_ctrl->getUri($f);
                    }
                }
                else{
                    $this->m_ajxfunc=$this->m_ctrl->getUri($f);
                }
            }
        }
        $frm=$this->m_frm;
        $prop=$this->m_prop;
        $search=$this->m_search;
        if(!$frm || empty($uri)){
            return;}
        $frm->clearChilds();
        $frm["action"]=$uri;
        $frm["id"]="search_item";
        $frm["method"]=new ValueListener($this, "Method");
        $frm->div()->setClass("igk-underline-div");
        $frm->NoTitle=true;
        $frm->NoFoot=true;
        $d=$frm->div();
        $d["class"]="disptable fitw";
        $d=$d->div()->setClass("disptabr");
        $this->m_link= HtmlUtils::AddImgLnk($d, $uri, "btn_search_16x16", "24px", "24px");
        $this["class"]="alignm";
        $this->m_input=$d->addInput($prop, "text", igk_getr($prop, $search));
        $this->m_input["class"]="igk-form-control fitw";
        $this->m_input["onkeypress"]="javascript:return ns_igk.form.keypress_validate(this,event);";
        if($this->AJX || $this->m_ajxfunc){
            $frm["igk-ajx-form"]=1;
            $frm["igk-ajx-form-uri"]=$this->m_ajxfunc;
            $frm["igk-ajx-form-target"]=$this->m_TargetId;
        }
        else{
            $frm["igk-ajx-form"]=null;
            $frm["igk-ajx-form-uri"]=null;
            $frm["igk-ajx-form-target"]=null;
        }
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setAJX($v){
        $this->m_AJX=$v;
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setMethod($v){
        $this->m_method=$v;
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setTargetId($v){
        $this->m_TargetId=$v;
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setUri($v){
        $this->m_uri=$v;
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function setValue($v){
        $this->m_search=$v;
        return $this;
    }
}