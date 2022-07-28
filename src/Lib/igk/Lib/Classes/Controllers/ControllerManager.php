<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ControllerManager.php
// @date: 20220425 14:53:16
// @desc: controller manager

namespace IGK\Controllers;

use IGK\Helper\IO;
use IGK\Resources\R;
use IGKControllerManagerObject;
use IGKControllerTypeManager;

///<summary>Controller used to manage controllers</summary>
/**
* Controller used to manage controllers
*/
final class ControllerManager extends NonVisibleControllerBase {
    ///<summary>add a controller from request</summary>
    /**
    * add a controller from request
    */
    public function addControllerRequest($name=null, $webpagecontent=false, $webparent=null){
        $n=str_replace(" ", "_", trim($name ?? igk_getr(IGK_FD_NAME, null)));
        $ctrl_typename=igk_getr("clCtrlType", null);
        $ctrl_ns=igk_getr("clCtrlNameSpace", "igk");
        $response=false;
        $code=0;
        $notf=igk_notifyctrl(igk_getr("notification"));
        $type=null;
        if(empty($ctrl_typename)){
            $code |= 16;
            $notf->addWarningr("msg.typenotdefined_1", $n);
        }
        if(!igk_is_identifier($n)){
            $notf->addWarningr("msg.identifiernotvalid_1", $n);
            $code |= 1;
        }
        if(igk_ctrl_is_reservedname($n)){
            $notf->addWarningr("msg.addctrl.nameisreserverd_1", "[".$n."]");
            $code |= 2;
        }
        if(class_exists($n)){
            $notf->addWarningr("msg.classalreadyexists");
            $code |= 4;
        }
        if(($info=igk_ctrl_get_ctrl_info($ctrl_typename)) && !$info->CanAddNew){
            $notf->addWarningr("msg.ctrl.notallowingchild_1", $info->Created);
            $code |= 8;
        }
        if($code == 0){
            $type=igk_getv(IGKControllerTypeManager::GetControllerTypes(), $ctrl_typename);
            if($type != null){
                $meth="CheckBeforeAddControllerInfo";
                if(method_exists($type, $meth) && !call_user_func_array(array($type, $meth), array($_REQUEST))){
                    $code |= 0x100;
                    $notf->addError(R::ngets("err.addctrl.meth.checkbeforeaddcontrollerinfo.failed_3", $type, $n, $code));
                    igk_wln("ceck befor exists ".$code);
                    return $response;
                }
            }
        }
        if($code != 0){
            $code |= 0x100;
            return $response;
        }
        if($n && ($n != ".") && ($n != "..") && (igk_getctrl($n, false) == null) && ($type != null)){
            $clcontent=self::GetDefaultClassContent($n, $type, $webparent);
            $p="";
            if(($ctrl_ns != "igk") && preg_match(IGK_NAME_SPACE_REGEX, $ctrl_ns)){
                $m=explode(".", $ctrl_ns);
                foreach($m as $k=>$v){
                    if(empty($v))
                        continue;
                    $p .= $v."/";
                }
            }
            $odir=igk_getr("clOutFolder");
            if(!empty($odir)){
                $p="/".$odir."/".$p;
            }
            $folder=igk_io_projectdir()."/".$p.$n;
            $file_name=$folder."/class.".$n.".php";
            if(file_exists($file_name)){
                $code |= 0x200;
                return false;
            }
            $grantaccess="allow from all";
            $denyaccess="deny from all";
            igk_init_controller(new ControllerInitListener($folder, 'system'));
            $t=igk_sys_getdefaultctrlconf();
            $t["clDataAdapterName"]=igk_getr("clDataAdapterName", igk_sys_getconfig("default_dataadapter"));
            $t["clDisplayName"]=igk_getr("clDisplayName", null);
            $t["clRegisterName"]=igk_getr("clRegisterName", igk_web_prefix().".".$n);
            $t["clParentCtrl"]=$webparent == null ? $webparent: igk_getr("clParentCtrl");
            $t["clTargetNodeIndex"]=igk_getr("clTargetNodeIndex");
            $t["clVisiblePages"]=igk_getr("clVisiblePages");
            $t["clDescription"]=igk_getr("clDescription");
            $t["clDataSchema"]=igk_getr("clDataSchema");
            $o=call_user_func_array(array($type, "SetAdditionalConfigInfo"), array(& $t));
            if($type == IGKDefaultPageController::class){
                igk_io_save_file_as_utf8($folder."/".IGK_SCRIPT_FOLDER."/default.js", self::GetDefaultScript($n));
            }
            $file_name=$folder."/class.".$n.".php";
            igk_io_save_file_as_utf8($file_name, $clcontent);
            igk_io_save_file_as_utf8($folder."/".IGK_VIEW_FOLDER."/".IGK_DEFAULT_VIEW_FILE, call_user_func(array($type, "GetAdditionalDefaultViewContent")));
            include($file_name);
            $conf=igk_create_node("config");
            foreach($t as $k=>$v){
                $conf->add($k)->Content=$v;
            }
            $cl=new $n();
            $f=$cl->getConfigFile();
            IO::CreateDir(dirname($f));
            igk_io_w2file($f, $conf->Render());
            $fn=IGK_INITENV_FUNC;
            if(method_exists($cl, $fn)){
                $e=call_user_func_array(array($n, $fn), array($cl));
                if(!$e){
                    igk_ilog("InitEnvironment failed for ".$cl, __METHOD__);
                }
            }
            IGKControllerManagerObject::getInstance()->initCtrl($cl, 1);
            unset($cl);
            $ctrl=igk_getctrl($n);
            $nodefaultarticle=igk_getr("nodefaultarticle", 0);
            if($ctrl && !$nodefaultarticle){
                igk_io_save_file_as_utf8($ctrl->getArticle(IGK_DEFAULT), R::ngets("default.articlev_1", $n)->getValue());
            }
			
			if ($t["clDataSchema"]){
				igk_io_w2file($folder."/".IGK_DATA_FOLDER."/".IGK_SCHEMA_FILENAME, "<".IGK_SCHEMA_TAGNAME." />"); 
			}
            igk_sys_cache_lib_files();
            igk_invoke_session_event("sys://event/controlleradded", array($this, $ctrl));
            $response=true;
        }
        else{
            igk_notifyctrl()->addErrorr("err.cannotaddnewctrl_2", $type, $n);
        }
        return $response;
    }
    ///<summary>Represente GetDefaultClassContent function</summary>
    ///<param name="name"></param>
    ///<param name="extends"></param>
    ///<param name="webparent" default="null"></param>
    /**
    * Represente GetDefaultClassContent function
    * @param  $name
    * @param  $extends
    * @param  $webparent the default value is null
    */
    public static function GetDefaultClassContent($name, $extends, $webparent=null){
        if(igk_ctrl_is_reservedname($name))
            return null;
        $cnf=igk_app()->getConfigs();
        $param=array();
        $param["extend"]=$extends;
        $param["summary"]="";
        $param["desc"]="";
        $param["create"]=igk_date_now();
        $param["copyright"]=igk_getv($cnf, 'copyright', IGK_COPYRIGHT);
        $param["author"]=igk_getv($cnf, 'default_author', IGK_AUTHOR);
        $s=IGK_STR_EMPTY;
        $s .= !$webparent || (igk_getctrl($webparent, false) == null) ? null: <<<EOF
igk_getctrl("{$webparent}")->regChildController(\$this);
EOF;
        $out=<<<EOF
<?php
//***
// @author:{$param["author"]}
// @description:{$param["desc"]}
// @created:{$param["create"]}
// @copyright: {$param["copyright"]}
// @type: controller
//***

///<summary>{$param["summary"]}</summary>
/**
 * {$param["summary"]}
 */
class $name extends {$param["extend"]}{

    /**
     * get controller identification 
     * */
	// public function getName(){return get_class(\$this);}

    /**
     * init countroller
     * */
	// protected function initComplete(\$context=null){
	// 	parent::InitComplete();    
	// }

	///<summary> init target node </summary>
	// protected function initTargetNode(){
	// 	\$node =  parent::initTargetNode();
	// 	return \$node;
	// }


	///<summary>override to handle your custom view mecanism</summary>
    /**
     * override to handle view mecanism
     * */
	//public function View(){
	//	parent::View();
	//}

    //----------------------------------------
	// Please Enter your code declaration here
	//----------------------------------------
}
EOF;
        return $out;
    }
    ///<summary>Represente GetDefaultScript function</summary>
    ///<param name="n"></param>
    /**
    * Represente GetDefaultScript function
    * @param  $n
    */
    private static function GetDefaultScript($n){
        $conf="constant";
        $o=<<<OEF
{$conf('IGK_START_COMMENT')}
default script for {$n}
{$conf('IGK_END_COMMENT')}
OEF;
        return $o;
    }
    ///<summary>Represente getName function</summary>
    /**
    * Represente getName function
    */
    public function getName(){
        return IGK_CTRL_MANAGER;
    }
    ///<summary>Represente IsFunctionExposed function</summary>
    ///<param name="name"></param>
    /**
    * Represente IsFunctionExposed function
    * @param  $name
    */
    public function IsFunctionExposed($name){
        return true;
    }
    ///<summary>Represente removeCtrl function</summary>
    ///<param name="n" default="null"></param>
    /**
    * Represente removeCtrl function
    * @param  $n the default value is null
    */
    public function removeCtrl($n=null){
        $n=($n == null) ? igk_getr("n"): $n;
        $r=false;
        if($n){
            $ctrl=igk_getctrl($n);
            if($ctrl){
                $cl=get_class($ctrl);
                if($cl){
                    IGKControllerManagerObject::ClearCache();
                    $i=IGKControllerManagerObject::getInstance();
                    $r=is_string($n) ? $i->dropControllerByName($n): $i->dropController($n);
                    if($r){
                        $i->reloadModules(array($cl=>$n), false, 0);
                    }
                }
            }
            return $r;
        }
        return false;
    }
}
