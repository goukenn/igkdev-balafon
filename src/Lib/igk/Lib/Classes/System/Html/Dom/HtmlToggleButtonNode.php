<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlToggleButtonNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlToggleButtonNode extends HtmlNode{
    protected $tagname ="button";
    /**
     * Initializes the toggle button with default CSS classes and data attributes.
     */
    protected function initialize()
    {
        $this["class"]="igk-toggle-button";
        $this["igk-toggle-button"]=true;
        $this["igk-toggle-state"]="collapse";
    }
    /**
     * Clears children and adds the given number of icon bar spans to the button.
     *
     * @param int $c Number of icon bars to add.
     * @return static
     */
    public function addBar($c=1){
        $this->clearChilds();
        for($i=0; $i < $c; $i++)
            $this->add("span")->setClass("igk-iconbar dispb");
        return $this;
    }
}