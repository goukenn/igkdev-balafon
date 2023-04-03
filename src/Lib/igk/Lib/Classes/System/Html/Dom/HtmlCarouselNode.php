<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCarouselNode.php
// @date: 20220315 09:21:37
// @desc: represent carousel compoent

namespace IGK\System\Html\Dom;

use IGKException;

/**
 * for carousel component \
 *      activate attribute for setting
 *          - controls to enabled control \
 *          - indicators to enabled indicator bullet \
 *      configuration attribute \
 *          - igk:interval to setup interval default is 6000ms \
 * @package IGK\System\Html\Dom
 */
class HtmlCarouselNode extends HtmlNode{
    protected $tagname = "div";

    protected function initialize()
    {
        parent::initialize();        
    }
    /**
     * add new slide node
     * @return mixed 
     * @throws IGKException 
     */
    public function addSlide(){
        $n = $this->add("div");
        $n["class"] = "igk-winui-carousel-slide";
        return $n;
    }

    protected function _acceptRender($options = null):bool
    {
        if ($doc = $options ? igk_getv($options, "Document") : null){
            $doc->getTheme()->addTempFile(IGK_LIB_DIR."/Styles/winui/carousel.pcss");
        }
        return parent::_acceptRender($options);
    }
}