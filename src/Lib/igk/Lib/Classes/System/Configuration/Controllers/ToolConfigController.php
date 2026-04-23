<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ToolConfigController.php
// @date: 20220803 13:48:57
// @desc: 
namespace  IGK\System\Configuration\Controllers;
use IGK\Controllers\BaseController;
use IGK\Resources\R;
use IGKFv;
use \IGK\System\Configuration\Controllers\ToolHost;
use function igk_resources_gets as __; 

/**
* Represent IGKToolsCtrl class
*/
final class ToolConfigController extends ConfigControllerBase {
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return IGK_TOOLS_CTRL;
    }
    /**
    * Represent getConfigPage function
    */
    public function getConfigPage(){
        return "toolctrl";
    }
    /**
    * Returns Config Group.
    */
    public function getConfigGroup(){
        return "administration";
    }
    /**
    * Returns Config Index.
    */
    protected function getConfigIndex(){ 
        return 1000;
    }
    /**
    * Returns Is Config Page Available.
    */
    public function getIsConfigPageAvailable()
    {
        return true;
    }
    /**
    * Represent getm_tools function
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
    /**
    * Represent RegisterTool function
    * @param  $ctrl
    */
    public function RegisterTool($ctrl){ 
        $tools=  $this->getm_tools();
        $tools->register($ctrl);				
        $this->regChildController($ctrl);
    }
    /**
    * Represent View function
    */
    public function View():BaseController{ 
        $t=$this->getTargetNode();
        if(!$this->getIsVisible()){
            $t->remove();
            return $this;
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
        return $this;
    }
    /**
    * Represent view_tools_ajx function
    */
    public function view_tools_ajx(){
        $this->View();
        igk_wl($this->TargetNode->getinnerHtml());
    }
}