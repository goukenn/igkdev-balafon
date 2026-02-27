<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlArticleConfigNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlUtils;
use IGK\ValueListener;
use IGKViewMode;
/**
* Represent IGKHtmlArticleConfigNode class
*/
final class HtmlArticleConfigNode extends HtmlNode{

    /**
    * Property: ctrl.
    * @var mixed
    */
    private $m_ctrl;

    /**
    * Property: dropfile uri.
    * @var mixed
    */
    private $m_dropfileUri;

    /**
    * Name of filename.
    * @var mixed
    */
    private $m_filename;

    /**
    * Property: forceview.
    * @var mixed
    */
    private $m_forceview;

    /**
    * Property: target.
    * @var mixed
    */
    private $m_target;

    /**
    * auto generate doc.
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
        $n=($ctrl) ? $ctrl->getName(): "";
        if($config){
            HtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_edit_article_ajx&navigate=1&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "edit_16x16");
            HtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_add_article_frame_ajx&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "add_16x16");
            if(igk_io_file_exists($f)){
                $this->m_dropfileUri=$config->getUri("ca_drop_article_ajx&navigate=1&ctrlid=".$n."&n=".base64_encode($f));
                HtmlUtils::AddImgLnk($this, igk_js_post_frame(new ValueListener($this, "dropFileUri"), $ctrl), "drop_16x16")->setAlt("droparticle");
            }
        }
        else{
            $this->Content="no config article found";
        }
        $target->add($this);
        $this->setIndex(-1000);
    }

    /**
    * auto generate doc.
    */

    public function getdropFileUri(){
        return $this->m_dropfileUri;
    }

    /**
    * auto generate doc.
    */

    public function getIsVisible(){
        return $this->m_forceview || (parent::getIsVisible() && IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER));
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setdropFileUri($v){
        $this->m_dropfileUri=$v;
        return $v;
    }

    /**
    * Returns Ctrl.
    */
    public function getCtrl(){
        return $this->m_ctrl;
    }

    /**
    * Returns File Name.
    */
    public function getFileName(){
        return $this->m_filename;
    } 
}