<?php


namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlUtils;
use IGKValueListener;
use IGKViewMode;

///<summary>Represente class: IGKHtmlArticleConfigNode</summary>
/**
* Represente IGKHtmlArticleConfigNode class
*/
final class HtmlArticleConfigNode extends HtmlNode{
    private $m_ctrl;
    private $m_dropfileUri;
    private $m_filename;
    private $m_forceview;
    private $m_target;
    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    ///<param name="target" default="null"></param>
    ///<param name="filename" default="null"></param>
    ///<param name="forceview"></param>
    /**
    * 
    * @param mixed $ctrl the default value is null
    * @param mixed $target the default value is null
    * @param mixed $filename the default value is null
    * @param mixed $forceview the default value is 0
    */
    public function __construct($ctrl=null, $target=null, $filename=null, $forceview=0){
        parent::__construct("div");
        $this->m_filename=$filename;
        $this->m_target=$target;
        $this->m_ctrl=$ctrl;
        $f=$filename;
        $this->m_forceview=$forceview;
        $this["class"]="igk-article-options";
        $this["igk-article-options"]="true";
        $this->Index=-9999;
        $config=igk_getctrl(IGK_CA_CTRL);
        $n=($ctrl) ? $ctrl->Name: "";
        if($config){
            HtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_edit_article_ajx&navigate=1&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "edit_16x16");
            HtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_add_article_frame_ajx&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "add_16x16");
            if(file_exists($f)){
                $this->m_dropfileUri=$config->getUri("ca_drop_article_ajx&navigate=1&ctrlid=".$n."&n=".base64_encode($f));
                HtmlUtils::AddImgLnk($this, igk_js_post_frame(new IGKValueListener($this, "dropFileUri"), $ctrl), "drop_16x16")->setAlt("droparticle");
            }
        }
        else{
            $this->Content="no config article found";
        }
        $target->add($this);
        $this->setIndex(-1000);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getdropFileUri(){
        return $this->m_dropfileUri;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsVisible(){
        return $this->m_forceview || (parent::getIsVisible() && IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER));
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setdropFileUri($v){
        $this->m_dropfileUri=$v;
        return $v;
    }
    public function getCtrl(){
        return $this->m_ctrl;
    }
    public function getFileName(){
        return $this->m_filename;
    } 
}