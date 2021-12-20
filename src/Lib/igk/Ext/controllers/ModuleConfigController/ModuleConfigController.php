<?php

use IGK\System\Configuration\Controllers\ConfigControllerBase;

use function igk_resources_gets as __;

///<summary>Module configuration controller</summary>
class ModuleConfigController extends ConfigControllerBase{
    
    
    public function getConfigPage(){
        return "Module";
    }
    public function getConfigGroup(){
        return "controller";
    }    
    public function View(){
        if (!$this->getIsVisible())
		{
			igk_html_rm($this->TargetNode);
			return;
		}
        $t = $this->getTargetNode()->clearChilds();
        $t->h1()->Content = __("Modules");

        if ($tab = igk_get_modules()){
            // $t->div()->Content = count($tab); 
            $pan = $t->panelbox();
            $pan->actionbar(function($a){
                $group = $a->actiongroup()->setClass("floatr");
                $group->form()->addInput("search_box","text", $this->getParam("module:search"))
                ->setClass("collapse dispib");
 
            });
            $pan->host(function($t, $tab){
                $table = $t->table();
                $table->header("Name", "Author", "Description");
                $search = igk_getr("search_box");
                $table->loop($tab)->host(function($n, $p)use($search){
                    if ($search && !preg_match("/".$search."/i", $p->name)){
                        return;
                    }
                    $tr = $n->tr();
                    $tr->td()->Content = $p->name;                    
                    $tr->td()->Content = $p->author;                    
                    $tr->td()->Content = igk_getv($p, "desc");
                });
            }, $tab);
        }
        else {
            $t->pane()->Content = __("No modules founds");
        }

    }
}

// igk_reg_configuration(ModuleConfigController::class);