<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBodyMainScript.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlBodyMainScript extends HtmlScriptNode{
    /**
    * Property: item.
    * @var mixed
    */
    static $item;
    /**
    * Property: scripts.
    * @var mixed
    */
    private $m_scripts = [];
    /**
    * Adds Script.
    * @param mixed $key
    * @param mixed $script
    */
    public function addScript($key, $script){
        if(!isset($this->m_scripts[$key])){            
            if (!empty($script)){
                $this->m_scripts[$key]=$script;
            }  
        }
        else{ 
            if ($script===null)
            {
                unset($this->m_scripts[$key]);
            } else {
                $this->m_scripts[$key]=$script;
            }
        }
        return igk_count($this->m_scripts);
    }
    /**
    * Adds Script Node.
    * @param mixed $id
    * @param mixed $n
    */
    public function addScriptNode($id, $n){
        return $this->m_bodyMainScript->addScriptNode($id, $n);
    }
    /**
    * Append script.
    * @param mixed $scriptFile
    */
    public function appendScript($scriptFile){
        return $this->appendScript($scriptFile);
    }
    /**
    * Removes Script.
    * @param mixed $index
    */
    public function removeScript($index){
        $str=igk_getv($this->m_scripts, $index);
        if($str){
            unset($this->m_scripts[$index]);
            $this->_initValue();
        }
    }
    /**
    * Returns Script At.
    * @param mixed $index
    */
    public function getScriptAt($index){
        return igk_getv($this->m_scripts, $index, null);
    }
    /**
    * Returns Item.
    */
    public static function getItem(){
        if (self::$item === null)
            self::$item = new self();
        return self::$item;
    }
    /**
    * .ctr
    */
    function __construct(){
        parent::__construct();
        $this["class"] = "igk-mbody-script";
        // avoid defering on script
        $this->activate('defer');
    }
    /**
    * Get rendering children.
    * @param null|mixed $options
    */
    protected function _getRenderingChildren($options = null)
    {
        return array_filter([ 
            new HtmlBodyInitDocumentNode(),
            count($this->m_scripts)>0 ? new SourceScriptRenderer($this->m_scripts) : null
        ]);
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null): bool
    {      
        // $r = count($this->m_scripts)>0;
        // igk_wln_e(__FILE__.":".__LINE__ , 'main script ', $r);  
        return true; 
    }
}
/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class SourceScriptRenderer extends HtmlNode{
    /**
    * Property: scripts.
    * @var mixed
    */
    private $m_scripts;
    /**
    * .ctr
    * @param mixed $scripts
    */
    public function __construct($scripts)
    {
        $this->m_scripts = $scripts;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options = null) { 
        return $this->m_scripts ? implode("\n", array_values($this->m_scripts )) : null;
    }
} 