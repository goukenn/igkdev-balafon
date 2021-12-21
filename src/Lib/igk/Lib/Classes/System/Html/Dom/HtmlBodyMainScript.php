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
            if(is_string($script))
                $this->m_v .= $script;
            else{
                $this->m_v .= HtmlUtils::GetValue($script);
            }
            $this->m_scripts[$key]=$script;
        }
        else{
            $this->m_scripts[$key]=$script;
            $this->_initValue();
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
            // new ScriptFileRendererer($this->m_script),
            new HtmlBodyInitDocumentNode()
        ];
    }
}  

/** @package IGK\System\Html\Dom */
class ScriptFileRendererer implements IHtmlGetValue{
    private $tag;
    public function __construct(array $tab){
        $this->tag = $tab;
    }
    public function getValue($option=null){
        $m = "";
        foreach($this->tab as $k=>$v){
            $m.="<script src=\"".$v."\"></script>";
        }
        return $m;
    }
}