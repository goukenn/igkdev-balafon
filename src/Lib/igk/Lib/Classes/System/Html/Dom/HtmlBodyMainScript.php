<?php

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlUtils;
use IGK\System\Html\IHtmlGetValue;

class HtmlBodyMainScript extends HtmlScriptNode{
    static $item;
    private $m_scripts = [];
///<summary>add inline script to bodymain  script</summary>
    ///<return>index of this script</return>
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
     ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="n"></param>
    public function addScriptNode($id, $n){
        return $this->m_bodyMainScript->addScriptNode($id, $n);
    }
 
    public function appendScript($scriptFile){
        return $this->appendScript($scriptFile);
    }
    ///<summary></summary>
    ///<param name="index"></param>
    public function removeScript($index){
        $str=igk_getv($this->m_scripts, $index);
        if($str){
            unset($this->m_scripts[$index]);
            $this->_initValue();
        }
    }
///<summary></summary>
    ///<param name="index"></param>
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
    }
     
    protected function __getRenderingChildren($options = null)
    {
        return [ 
            new HtmlBodyInitDocumentNode(),
            new SourceScriptRenderer($this->m_scripts)
        ];
    }
}  
class SourceScriptRenderer extends HtmlNode{
    private $m_scripts;

    public function __construct($scripts)
    {
        $this->m_scripts = $scripts;
    }
    public function render($options = null) { 
        return $this->m_scripts ? implode("\n", array_values($this->m_scripts )) : null;
    }

} 