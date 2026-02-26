<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlImgLnkNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Resources\R;
/**
* Represent IGKHtmlImgLnkItem class
*/
final class HtmlImgLnkNode extends HtmlANode{

    /**
    * Property: img.
    * @var mixed
    */
    private $m_img;
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
    /**
    * 
    */

    public function getAlt(){
        return $this->m_img["alt"];
    }
    /**
    * 
    * @param mixed $v
    */

    public function setAlt($v){
        $this->m_img["alt"]=$v;
        return $this;
    }
}