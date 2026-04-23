<?php
// @file: IGKHtmlCssLink.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

/**
* Html css link node.
* @package IGK\System\Html\Dom
*/
class HtmlCssLinkNode extends HtmlNode{
    /**
     * Determines whether this CSS link node should be included in the render output.
     * @param mixed $options Render options, may contain a Context and Document.
     * @return bool
     */
    protected function _acceptRender($options = null):bool{
        if($this->system && $options && ($options->Context == "mail")){
            return false;
        }
        $uri= null;
        $tr= $uri ? $uri: $this->link;
        $this->ln["href"]= $tr;
        return $tr && $this->IsVisible;
    }
    /**
     * Constructor.
     * @param string $link The stylesheet URL or path.
     * @param bool $system Whether this is a system-level stylesheet.
     * @param int $defer Whether to defer loading of the stylesheet.
     */
    public function __construct($link, $system=false, $defer=0){
        parent::__construct("igk-css-link");
        $ln = self::CreateWebNode("link");
        $ln["type"]="text/css";
        $ln["rel"]="stylesheet";
        if($defer)
            $ln->activate("defer");
        $ln->link=$link;
        $ln->cache=false;
        $ln->system=$system;
        $this->setln($ln);
    }
    /**
     * Indicates that this node does not render its own tag.
     * @return bool
     */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
     * Returns the inner link node as the sole child for rendering.
     * @param mixed $option Render options.
     * @return array
     */
    protected function _getRenderingChildren($option=null){
        return array($this->ln);
    }
    /**
     * Activates a named feature or condition on the inner link node.
     * @param string $name The feature name to activate.
     * @param mixed $condition_key Optional condition key for the activation.
     * @return static
     */
    public function activate($name, $condition_key=null){
        $this->ln->activate($name, $condition_key);
        return $this;
    }
    /**
     * Returns the cache setting of the inner link node.
     * @return mixed
     */
    public function getCache(){
        return $this->ln->cache;
    }
    /**
     * Returns the stylesheet link URL or path.
     * @return string
     */
    public function getlink(){
        return $this->ln->link;
    }
    /**
     * Returns the inner link node stored in the csslink flag.
     * @return mixed
     */
    public function getln(){
        return $this->getFlag("csslink");
    }
    /**
     * Returns the rel attribute value of the inner link node.
     * @return string
     */
    public function getRel(){
        return $this->ln["rel"];
    }
    /**
     * Returns the system flag value of the inner link node.
     * @return bool
     */
    public function getSystem(){
        return $this->ln->system;
    }
    /**
     * Returns the type attribute value of the inner link node.
     * @return string
     */
    public function getType(){
        return $this->ln["type"];
    }
    /**
     * Returns null to suppress inner HTML output for this node.
     * @param mixed $option Render options.
     * @return null
     */
    protected function innerHTML(& $option=null){
        return null;
    }
    /**
     * Sets an attribute on the inner link node.
     * @param string $name The attribute name.
     * @param mixed $value The attribute value.
     * @return static
     */
    public function setAttribute($name, $value){
        $this->ln->setAttribute($name, $value);
        return $this;
    }
    /**
     * Stores the inner link node in the csslink flag.
     * @param mixed $v The link node to store.
     * @return static
     */
    protected function setln($v){
        $this->setFlag("csslink", $v);
        return $this;
    }
    /**
     * Sets the rel attribute on the inner link node.
     * @param string $value The rel attribute value.
     */
    public function setRel($value){
        $this->ln["rel"]=$value;
    }
    /**
     * Sets the type attribute on the inner link node.
     * @param string $value The type attribute value.
     * @return static
     */
    public function setType($value){
        $this->ln["type"]=$value;
        return $this;
    }
}