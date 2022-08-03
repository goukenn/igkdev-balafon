<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlImgLnkNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Resources\R;

/**
* Represente IGKHtmlImgLnkItem class
*/
final class HtmlImgLnkNode extends HtmlANode{
    private $m_img;
    ///<summary></summary>
    ///<param name="uri" default="null"></param>
    ///<param name="img" default="null"></param>
    ///<param name="width" default="16px"></param>
    ///<param name="height" default="16px"></param>
    ///<param name="desc" default="null"></param>
    /**
    * 
    * @param mixed $uri the default value is null
    * @param mixed $img the default value is null
    * @param mixed $width the default value is "16px"
    * @param mixed $height the default value is "16px"
    * @param mixed $desc the default value is null
    */
    public function __construct($uri=null, $img=null, $width="16px", $height="16px", $desc=null){
        parent::__construct();
        $this["href"]=$uri;
        $this->m_img=$this->add("img", array(
            "width"=>$width,
            "height"=>$height,
            "src"=>R::GetImgUri(trim($img)),
            "alt"=>R::ngets($desc)
        ));
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAlt(){
        return $this->m_img["alt"];
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setAlt($v){
        $this->m_img["alt"]=$v;
        return $this;
    }
}