<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ToolConfigController.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Configuration\Controllers;

use IGK\Resources\R;
use IGKFv;
use \IGK\System\Configuration\Controllers\ToolHost;

use function igk_resources_gets as __; 
///<summary>Represente class: IGKToolsCtrl</summary>
/**
* Represente IGKToolsCtrl class
*/
final class ToolConfigController extends ConfigControllerBase {
 
    ///<summary>Represente getConfigPage function</summary>
    /**
    * Represente getConfigPage function
    */
    public function getConfigPage(){
        return "toolctrl";
    }
    public function getConfigGroup(){
        return "administration";
    }
    protected function getConfigIndex(){ 
        return 1000;
    }
    public function getIsConfigPageAvailable()
    {
        return true;
    }
    ///<summary>Represente getm_tools function</summary>
    ///<return refout="true"></return>
    /**
    * Represente getm_tools function
    * @return *
    */
    public function getm_tools(){
        static $_toolhost=null;
        if($_toolhost === null){
			$tab = [];		
			$fv = new IGKFv($tab);
			$_toolhost=new ToolHost($fv);
        }
        return $_toolhost;
    }
    ///<summary>Represente RegisterTool function</summary>
    ///<param name="ctrl"></param>
    /**
    * Represente RegisterTool function
    * @param  $ctrl
    */
    public function RegisterTool($ctrl){ 
        $tools=  $this->getm_tools();
        $tools->register($ctrl);				
        $this->regChildController($ctrl);
    }
    ///<summary>Represente View function</summary>
    /**
    * Represente View function
    */
    public function View(){ 
        $t=$this->TargetNode;
        if(!$this->getIsVisible()){
            $t->remove();
            return;
        }

        $v_ct=$this->getm_tools()->getTools();
        $count = igk_count($v_ct);
        $t->ClearChilds();
        $this->getConfigNode()->add($t);
        $box=$t->addPanelBox();
        igk_html_add_title($box, __("Tools"));
        
        igk_notifyctrl()->setNotifyHost($box->addDiv());
        $s=$box->addSearch()->setClass("fitw");
        $s->Uri=$this->getUri("view_tools_ajx");
        $s->TargetId="#igktoolsctrl";
        $s->loadingComplete();
        $box->addHSep();
        $th=igk_app()->Doc->getSysTheme();
        $th[".igk-tool-option div"]="padding: 4px; background-color:white";
        $d["class"]="igk-tool-option table ";
        $q=strtolower(igk_getr("q"));
        $box->addDiv()->Content=__("Tools : {0} ", $count);
        if ($count>0){
        $d=$box->addDiv();
            foreach($v_ct as $k=>$v){
                if(!$v->getIsAvailable() || ($q && !strstr(strtolower($v->Name), $q) && !strstr(strtolower(R::ngets("tool.".$this->Name)->getValue()), $q)))
                    continue;
                $v->showTool($d->addDiv()->setAttribute("class", "dispib floatl marg4"));
            }
        }
    }
    
    ///<summary>Represente view_tools_ajx function</summary>
    /**
    * Represente view_tools_ajx function
    */
    public function view_tools_ajx(){
        $this->View();
        igk_wl($this->TargetNode->getinnerHtml());
    }
}