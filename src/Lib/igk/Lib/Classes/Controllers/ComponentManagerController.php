<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ComponentManagerController.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;

use __PHP_Incomplete_Class;
use stdClass;

// // /**
// * component manager controller
// */

/**
* Component manager controller.
* @package IGK\Controllers
*/
final class ComponentManagerController extends NonVisibleControllerBase{
    /**
    *  Dispose all component
    */
    public function DisposeAll(){
        if($ob=$this->getComponents()){
            foreach($ob as  $v){
                $v->Dispose();
            }
        }
        igk_app()->settings->appInfo->components = null;
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    */
    public function Exists($obj){
        $setting=& $this->getSettings();
        return igk_array_value_exist($setting->objs, $obj);
    }

    /**
    * auto generate doc.
    * @param mixed $id
    */
    public function getComponentById($id){
        return igk_getv($this->m_ids, $id);
    }

    /**
    * auto generate doc.
    * @param mixed $host
    */
    public function getComponentId($host){
        return $host->getParam(IGK_COMPONENT_ID_PARAM);
    }
    /**
    *  get components registrated
    */
    public function getComponents(){
        return $this->getm_objs();
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    */
    public function getId($obj){
        if($obj === null)
            return null;
		if (get_class($obj) === stdClass::class){
			igk_die("stdlass resolv ::: -" );
		}
        $r=$obj->getParam(__CLASS__.":id");
        if($r)
            return $r;
        foreach($this->m_ids as $k=>$v){
            if($v === $obj)
                return $k;
        }
        return null;
    }

    /**
    * auto generate doc.
    */
    public function getm_ids(){
        return $this->settings->ids;
    }

    /**
    * auto generate doc.
    */
    public function getm_objs(){
        return $this->settings->objs;
    }

    /**
    * auto generate doc.
    */
    public function getm_srcs(){
        return $this->settings->srcs;
    }

    /**
    * auto generate doc.
    */
    public function getm_uris(){
        return $this->settings->uris;
    }

    /**
    * auto generate doc.
    */
    public function getName(): string{
        return IGK_COMPONENT_MANAGER_CTRL;
    }

    /**
    * auto generate doc.
    * @return mixed|array settings
    */
    public function & getSettings(){
        static $setting;
        if($setting === null){
            $setting = igk_getv(igk_app()->settings->appInfo, 'components');         
            if(!$setting){
                $setting= igk_prepare_components_storage();
                igk_app()->settings->appInfo->components = $setting;
            }
        }
        return $setting;
    }

    /**
    * auto generate doc.
    * @param mixed $obj the default value is null
    */
    public function getUri($f=null, $obj=null){
        if($obj == null)
            return parent::getUri($f);
        $id=$this->getId($obj);
        $u=parent::getUri("inv&q=".base64_encode("f=".$f."&id=".$id));
        $this->getsettings()->uris[$id]=get_class($obj);
        return $u;
    }

    /**
    * auto generate doc.
    */
    protected function initComplete($context=null){
        parent::initComplete();
        igk_hook(IGK_NODE_DISPOSED_EVENT, array($this, "nodeDisposed"));
    }
    /**
    * invoke
    */
    public function inv(){
        $f=base64_decode(igk_getr("q"));
        if(empty($f))
            return;
        $tab=igk_getquery_args($f);
        $k=$tab["id"];
        $obj=igk_getv($this->m_ids, $k);
        if($obj){
            if(get_class($obj) == __PHP_Incomplete_Class::class){
                igk_wln("try to used an incomplete class ");
                igk_exit();
            }
            $m=igk_sys_meth_info(igk_getv(explode("&", $tab["f"]), 0));
            $g=$m->Name;
            if(method_exists(get_class($obj), $g)){
                if(igk_count($m->Args) == 0){
                    $obj->$g();
                }
                else{
                    call_user_func_array(array($obj, $g), $m->Args);
                }
            }
            else{
                call_user_func_array(array($obj, $g), $m->Args);
            }
            igk_exit();
        }
        else{
            igk_header_no_cache();
            igk_set_header(404, "Error Component");
            igk_hook(IGK_COMP_NOT_FOUND_EVENT, $this, $k);
            if(igk_is_ajx_demand()){
                igk_wln("<div style=\"color:#FFDF72\" >/!\\ Component {{$k}} not found  </div>");
            }
            else{
                $doc= igk_get_document($this, true);
                $doc->Title= __("Component Error") . " - ".igk_web_get_config('website_title');
                $dv=$doc->body->addBodyBox()->div();
                $dv->div()->setContent("<div>component object not found.[ $k ]</div><i>/!\\ session destroyed or component cleared</i>");
                $doc->renderAJX();
                $doc->Dispose();
            }
            igk_exit();
        }
    }

    /**
    * auto generate doc.
    * @param mixed $node
    */
    public function nodeDisposed($e){
		$node = $e->args[0];
        $this->Unregister($node);
    }

    /**
    * auto generate doc.
    * @param mixed $componentInterface the default value is true
    */
    public function Register($obj, $componentInterface=true){
		return; 
    }

    /**
    * auto generate doc.
    * @param mixed $obj
    */
    public function Unregister($obj){
		return; 
    }
}