<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlMemoryUsageInfoNode.php
// @date: 20220803 13:48:56
// @desc: 



///<summary>represent language selection options</items>

namespace IGK\System\Html\Dom;

use IGK\Resources\R;
use IGK\System\Number;
use IGKValueListener;
use function igk_resources_gets as __;


/**
* represent language selection options
*/
final class HtmlMemoryUsageInfoNode extends HtmlComponentNode {
    public function & getSetting(){
        $m = [];
        return $m;
    }
    ///<summary></summary>
    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct("div");
        $this->add("div")->Content=new IGKValueListener($this, "MemoryInUsed");
        $this->add("div")->Content=new IGKValueListener($this, "MemoryPeekInUsed");
        $this->add("div")->Content=new IGKValueListener($this, "Components"); 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function clear_component(){
        igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->DisposeAll();
        session_destroy();
        igk_navtobaseuri();
    }
    ///<summary></summary>
    /**
    * 
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
    ///<summary>Get componsent strings </summary>
    /**
    * 
    */
    public function getComponents(){
        return __("Component : {0}",  igk_count(igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->getComponents()));
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getMemoryInUsed(){
        return Number::GetMemorySize(memory_get_usage());
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getMemoryPeekInUsed(){
        return Number::GetMemorySize(memory_get_peak_usage());
    }
    ///<summary></summary>
    /**
    * 
    */
    public function memoryinfo(){
        $this->renderAJX();
        igk_exit();
    }
}