<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleConfigurationController.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\WinUI\Menus\MenuItem;
use IGK\System\WinUI\Paginator;

use function igk_resources_gets as __;

///<summary>Module configuration controller</summary>
class ModuleConfigurationController extends ConfigControllerBase{
    public function getName(){
        return MODULE_CNF_CTRL;
    }
    public function initConfigMenu()
	{
		return [
			new MenuItem(
				$this->getConfigPage(),
				__("Modules"),
				$this->getUri("showConfig"),
				30, null, $this->getConfigGroup()
			)
		];
	}
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
        $box = $t->panelbox();
        $box->h1()->Content = __("Modules");
        $box->div()->ajxuriloader($this->getUri("module_view"));
    }
    public function module_view(){
        
        $t = igk_create_node("notagnode");
        if ($tab = igk_get_modules()){
            // $t->div()->Content = count($tab); 
            $pan = $t->div();
            $pan->form()->actionbar(function($a){
                $group = $a->actiongroup()->setClass("floatr");
                $group->form()->addInput("search_box","text", $this->getParam("module:search"))
                ->setClass("collapse dispib"); 
            });

            $pagination = new Paginator(count($tab));
            $pan->host(function($t, $tab){
                $table = $t->table();
                $table->header("", "Name","Version", "Author", "Description","","");
                $search = igk_getr("search_box");
                $table->loop($tab)->host(function($n, $p)use($search){
                    if ($search && !preg_match("/".$search."/i", $p->name)){
                        return;
                    }
                    $tr = $n->tr();
                    $tr->td()->nbsp();
                    $tr->td()->Content = $p->name;                    
                    $tr->td()->Content = $p->version;                    
                    $tr->td()->Content = $p->author;                    
                    $tr->td()->Content = igk_getv($p, "desc");
                    // TODO : render extra information 
                    $extra = null;
                    if ($extra){
                        $n->tr()->td()->setAttribute("colspan", 7)
                            ->Content = "Extra info";
                    }
                });
            }, $tab);
        }
        else {
            $t->panelbox()->Content = __("No modules founds");
        }
        return $t;
    }
}

// igk_reg_configuration(ModuleConfigController::class);