<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlMemoryUsageInfoNode.php
// @date: 20220803 13:48:56
// @desc: 
///<summary>represent language selection options</items>
namespace IGK\System\Html\Dom;
use IGK\Resources\R;
use IGK\System\Number;
use IGK\ValueListener;
use function igk_resources_gets as __;
/**
* represent language selection options
*/
final class HtmlMemoryUsageInfoNode extends HtmlComponentNode {
    /**
    * Returns Setting.
    */
    public function & getSetting(){
        $m = [];
        return $m;
    }
    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct("div");
        $this['class'] = 'memory-view';
        $this->add("div")->Content= new ValueListener($this, "MemoryInUsed");
        $this->add("div")->Content= new ValueListener($this, "MemoryPeekInUsed");
        $this->add("div")->Content= new ValueListener($this, "Components"); 
    }
    /**
    * auto generate doc.
    */
    public function clear_component(){
        igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->DisposeAll();
        igk_ilog( __FILE__.":".__LINE__ , 'destroy session '); session_destroy();
        igk_navtobaseuri();
    }
    /**
    * auto generate doc.
    */
    public function component_info_ajx(){
        $d=igk_create_node();
        $c=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->getComponents();
        $tab=$d->add("table");
        foreach($c as $k=>$v){
            $r=$tab->add("tr");
            $r->add("td")->Content=$k;
            $r->add("td")->Content=get_class($v);
            $id=$v->getParam(IGK_DOC_ID_PARAM) ?? igk_getv($v, 'id');
            $r->add("td")->Content="id: ".$id;
            $r->add("td")->Content=method_exists($v, "getId") ? $v->getId(): "-";
            $r->add("td")->Content=method_exists($v, "getOwner") ? $v->getOwner()->toString(): "-";
        }
        igk_ajx_notify_dialog(R::Gets("title.componentinfo"), $d);
        igk_exit();
    }
    /**
    * get component in use
    */
    public function getComponents(){
        return __("Component: {0}",  igk_count(igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->getComponents()));
    }
    /**
    * get Memeory in use 
    */
    public function getMemoryInUsed(){
        return implode(" ", [sprintf('<pre>%15s</pre>', __('Memory In Used:')),  Number::GetMemorySize(memory_get_usage())]);
    }
    /**
    * get Peek Memory in use
    */
    public function getMemoryPeekInUsed(){
        return implode(" ", [sprintf('<pre>%15s</pre>', __('Memory Peek In Used:')),Number::GetMemorySize(memory_get_peak_usage())]);
    }
    /**
    * auto generate doc.
    */
    public function memoryinfo(){
        $this->renderAJX();
        igk_exit();
    }
}