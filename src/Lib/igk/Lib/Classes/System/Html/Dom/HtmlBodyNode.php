<?php
namespace IGK\System\Html\Dom;
use IGK\System\Html\RenderOptionProperties;
use IGKEvents;
require_once( IGK_LIB_CLASSES_DIR. "/System/Html/Dom/HtmlDefaultMainPage.php");
require_once( IGK_LIB_CLASSES_DIR. "/System/Html/Dom/HtmlPoweredByNode.php");
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
/**
* Html body node.
* @package IGK\System\Html\Dom
*/
class HtmlBodyNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "body";
    /**
    * Property: bodybox.
    * @var mixed
    */
    private $m_bodybox;
    /**
    * Property: body main script.
    * @var mixed
    */
    private $m_bodyMainScript;
    /**
    * Property: inline theme.
    * @var mixed
    */
    private $m_inlineTheme;
    /**
     * html node 
     * @var mixed
     */
    private $m_appendContent;
    /**
    * .ctr
    */
    public function __construct()
    {
        parent::__construct();
        $this->m_bodyMainScript = new HtmlBodyMainScript();
    }
    // // // // public function addScriptNode($id, $n){
    //     return $this->m_bodyMainScript->addScriptNode($id, $n);
    // }
    /**
    * Removes Script.
    * @param mixed $scriptFile
    */
    public function removeScript($scriptFile){
        return $this->m_bodyMainScript->removeScript($scriptFile);
    }
    /**
     * append main body script
     * @param mixed $id 
     * @param mixed $scriptFile 
     * @return mixed 
     */
    public function appendScript($id, $scriptFile){ 
        return $this->m_bodyMainScript->addScript($id, $scriptFile);
    }
    /**
    * Returns Append Content.
    */
    public function getAppendContent(){
        if($this->m_appendContent === null){
            $this->m_appendContent = new HtmlNoTagNode();
        }
        return $this->m_appendContent;
    }
    ///load addition script content when page request loaded.
    /**
    * auto generate doc.
    */
    public function addScriptContent($key, $script){       
        return $this->m_bodyMainScript->addScript($key, $script);
    }
    /**
     * retrieve the body box 
     * @return mixed 
     */
    public function getBodyBox(){
        if ($this->m_bodybox ===null){
            $this->m_bodybox = new HtmlBodyBoxNode($this);
        }
        return $this->m_bodybox;
    }
    /**
    * Adds Body Box.
    */
    public function addBodyBox(){
        return $this->getBodyBox();
    }
    /**
    * Get rendering children.
    * @param null|mixed $options
    */
    protected function _getRenderingChildren($options = null)
    { 
        $doc = igk_getv($options, RenderOptionProperties::DOCUMENT);
        $c = [];
        if ($this->getBodyBox()->getHasChilds()){
            $c[] = $this->m_bodybox;
        }        
        $c = array_merge($c,  parent::_getRenderingChildren($options));
        $tr = $doc->getDefaultMainPage(); 
        if ($tr->getIsVisible()){
            $c[] = $tr;
        }
        $c[] = $this->m_bodyMainScript;   
        $c[] = $this->getAppendContent(); 
        if ($doc){
            $c[] = new HtmlDocumentCssHostNode($doc);
        }
        $c[] = HtmlPoweredByNode::getItem(); 
        $c[] = new HtmlHookNode(IGKEvents::HOOK_HTML_BODY, "body");
        return $c;
    }
}