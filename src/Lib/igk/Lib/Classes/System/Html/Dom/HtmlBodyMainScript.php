<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBodyMainScript.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;


/**
 * 
 * @package IGK\System\Html\Dom
 */
class HtmlBodyMainScript extends HtmlScriptNode{
    static $item;
    private $m_scripts = [];
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
     public function addScriptNode($id, $n){
        return $this->m_bodyMainScript->addScriptNode($id, $n);
    }
 
    public function appendScript($scriptFile){
        return $this->appendScript($scriptFile);
    }
    public function removeScript($index){
        $str=igk_getv($this->m_scripts, $index);
        if($str){
            unset($this->m_scripts[$index]);
            $this->_initValue();
        }
    }
public function getScriptAt($index){
        return igk_getv($this->m_scripts, $index, null);
    }
    public static function getItem(){
        if (self::$item === null)
            self::$item = new self();
        return self::$item;
    }
    function __construct(){
        parent::__construct();
        $this["class"] = "igk-mbody-script";
        // avoid defering on script
        $this->activate('defer');
    }
     
    protected function _getRenderingChildren($options = null)
    {
        return array_filter([ 
            new HtmlBodyInitDocumentNode(),
            count($this->m_scripts)>0 ? new SourceScriptRenderer($this->m_scripts) : null
        ]);
    }
    protected function _acceptRender($options = null): bool
    {      
        // $r = count($this->m_scripts)>0;
        // igk_wln_e(__FILE__.":".__LINE__ , 'main script ', $r);  
        return true; 
    }
}  
/**
 * 
 * @package IGK\System\Html\Dom
 */
final class SourceScriptRenderer extends HtmlNode{
    private $m_scripts;

    public function __construct($scripts)
    {
        $this->m_scripts = $scripts;
    }
    public function render($options = null) { 
        return $this->m_scripts ? implode("\n", array_values($this->m_scripts )) : null;
    }

} 