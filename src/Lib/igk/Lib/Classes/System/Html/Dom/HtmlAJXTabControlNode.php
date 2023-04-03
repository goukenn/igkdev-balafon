<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
///<summary>represent a tab control node where tab contains came from ajx query</summary>
/**
* represent a tab control node where tab contains came from ajx query
*/
final class HtmlAJXTabControlNode extends HtmlCtrlComponentNodeItemBase {
    private $m_selected;
    private $m_tabViewListener;
    private $m_tabcontent;
    private $m_tablist;
    private static $demoComponent;
    public const CONTROL = HtmlComponents::AJXTabControl;

    public function getSelectedIndex(){
        return $this->m_selected;
    }

	public function getSettings($key){
		if ($this->m_tabViewListener){
			return $this->m_tabViewListener->getParam($key);
		}
		return "isnull";
	}
    ///<summary></summary>
    ///<param name="opt" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    protected function _acceptRender($options=null):bool{
        if($this->m_tabViewListener !== null){
            $this->m_tabViewListener->TabViewPage($this, $this->m_tablist, $this->m_tabcontent);
        }
        return parent::_acceptRender($options);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("div");
        $this->setClass("igk-tabcontrol");
        $h=$this->div()->setClass("igk-tab-h");
        $ul=$h->add("ul");
        $this->m_tablist=$ul;
        $c=$this->div();
        $this->m_tabcontent=$c;
        $this->m_tabcontent->setClass("igk-tabcontent");
    }
    ///<summary></summary>
    ///<param name="content" default="null"></param>
    ///<param name="uri" default="null"></param>
    ///<param name="active" default="false"></param>
    ///<param name="method" default="GET"></param>
    /**
    * 
    * @param mixed $content the default value is null
    * @param mixed $uri the default value is null
    * @param mixed $active the default value is false
    * @param mixed $method the default value is "GET"
    */
    public function addTabPage($content=null, $uri=null, $active=false, $method="GET"){
        $li=$this->m_tablist->add("li");
        $li->setParam("uri", $uri);
        $li->setParam("method", $method);
        $li->setParam("id", is_string($active)? $active : $content);
        $li->addA($uri)->setAttribute("igk-ajx-tab-lnk", 1)->setContent($content);
        if($active){
            if($this->m_selected){
                $this->m_selected->setClass("-igk-active");
            }
            $li->setClass("igk-active");
            $i=0;
            if($uri){
                $this->replaceContent($this->m_tabcontent, $uri, $method);
            }
            $this->m_selected=$li;
        }
        return $li;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function ClearChilds(){
        $this->m_tablist->clearChilds();
        $this->m_tabcontent->clearChilds();
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
    * 
    * @param mixed $t
    */
    public function initDemo($t){
        // + unregister component
        $s=igk_get_component(__METHOD__);
        if($s){
            $s->Dispose();
        }
        $buri=igk_register_temp_uri(__CLASS__);
        $this->clearChilds();
        $this->addTabPage("page1", $buri."/showpage/1", true);
        $this->addTabPage("page2", $buri."/showpage/2", false);
        $this->addTabPage("page3", $buri."/showpage/4", false);
        $i=$this->m_selected ? $this->m_selected: 1;
        $this->m_tabcontent->Content=igk_ob_get_func(array($this, "showpage"), array($i));
        $t->div()->Content="Code Sample";
        $t->div()->addCode()->setAttribute("igk-code", "php")->Content=<<<EOF
\$this->clearChilds();
\$this->addTabPage("page1", \$this->getComponentUri("showpage/1"), true);
\$this->addTabPage("page2", \$this->getComponentUri("showpage/2"), false);
\$this->addTabPage("page3", \$this->getComponentUri("showpage/4"), false);
EOF;
        // + register component
        igk_reg_component(__METHOD__, $this);
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="uri"></param>
    ///<param name="method" default="'GET'"></param>
    /**
    * 
    * @param mixed $t
    * @param mixed $uri
    * @param mixed $method the default value is 'GET'
    */
    private function replaceContent($t, $uri, $method='GET'){
        $t->addBalafonJS(1)->Content=<<<EOF
(function(q){ igk.winui.controls.tabcontrol.init('$uri', q);})(igk.getParentScript());
EOF;
    }
    ///<summary>force select tag</summary>
    ///<param name="i">identified the selected tab</param>
    /**
    * force select tag
    * @param string|int $i identified the selected tab
    */
    public function select($i){ 
        if($this->m_selected){
            $this->m_selected->setClass("-igk-active");
        }
        $this->m_tabcontent->clearChilds();
        $li = null;
        if (is_int($i)){
            $li=$this->m_tablist->Childs[$i];
        }else{

            foreach($this->m_tablist->Childs->to_array() as $hi){
                if ($hi->getParam("id") == $i){
                    $li = $hi;
                    break;
                }
            } 
        }
        if($li){
            $uri=$li->getParam("uri");
            $method=$li->getParam("method");
            $li->setClass("igk-active");
            if($uri){
                $this->replaceContent($this->m_tabcontent, $uri, $method);
            }
        }
    }
    ///<summary></summary>
    ///<param name="listener"></param>
    ///<param name="param" default="null"></param>
    /**
    * 
    * @param mixed $listener
    * @param mixed $param the default value is null
    */
    public function setComponentListener($listener, $param=null){
        // mark component to listen for parameter
    }
    ///<summary></summary>
    ///<param name="o"></param>
    /**
    * 
    * @param mixed $o
    */
    public function setTabViewListener($o){
        $this->m_tabViewListener=$o;
    }
    ///<summary> , "for demonstration"</summary>
    /**
    *  , "for demonstration"
    */
    public function showpage($index=0){
        if($this->Ctrl){
            $this->Ctrl->showTabPage($index);
        }
        else{
            $d=igk_create_node("div");
            $d->Content="Demo page ".$index;
            $this->m_selected=$index;
            $d->renderAJX();
        }
    }
}