<?php

///<summary>Represente class: IGKApiFunctionCtrl</summary>

use IGK\Controllers\ApplicationController;
use IGK\Database\DbSchemas;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;

/**
* Represente IGKApiFunctionCtrl class
*/
final class ApiFunctionController extends ApplicationController {
    const LIBNAME=IGK_API_MYSQLPINC;
    ///<summary></summary>
    /**
    * 
    */
    public function about(){
        igk_wln_e(__FILE__.":".__LINE__, "About");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function beginRequest(){
        $u=igk_getr("u");
        $pwd=igk_getr("pwd");
        if(!$this->ConfigCtrl->IsConnected){
            $this->ConfigCtrl->connect($u, $pwd, false);
        }
        $node=HtmlNode::CreateWebNode("APIResponse");
        if($this->ConfigCtrl->IsConnected){
            $node->add("status")->Content="OK";
            $this->setParam("api:u", $u);
            $this->setParam("api:pwd", $pwd);
            $node->add("SessionId")->Content=session_id();
            igk_show_prev(getallheaders());
        }
        else{
            $node->add("status")->Content="NOK";
            $node->add("message")->Content=$this->message[0];
        }
        $node->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="cmd" default="null"></param>
    /**
    * 
    * @param mixed $cmd the default value is null
    */
    public function ctrl($cmd=null){
        $args=array_slice(func_get_args(), 1);
        $_api=$this;
        $_data=array();
        $n=igk_create_node("div");
        $_data["geninstall"]=function($ctrl) use ($n, $_api){
            $v=igk_getctrl($ctrl, false);
            if(!$v){
                $n->div()->Content="/!\\ Controller [".$ctrl."] not found";
                return false;
            }
            $folder=$v->getDeclaredDir();
            $zip=new ZipArchive();
            $tempdir=$_api->getDeclaredDir()."/temp";
            IO::CreateDir($tempdir);
            $ftempdir=IO::GetDir($tempdir."/".igk_new_id().".iczip");
            if($zip->open($ftempdir, ZIPARCHIVE::CREATE)){
                igk_zip_dir($folder, $zip);
                $inf=igk_create_node("ctrl");
                igk_api_build_ctrl_manifest($ctrl, $inf);
                $opt=HtmlRenderer::CreateRenderOptions();
                $opt->Context="xml";
                $zip->addFromString("ctrl.manifest", $inf->render($opt));
                $zip->close();
                $n->Content=" zip archive created ";
                igk_download_file($v->Name.".iczip", $ftempdir);
                unlink($ftempdir);
                igk_exit();
            }

                $n->Content="not created";
				return false;
        };
        $_data["initDb"]=function($ctrl) use ($n, $_api){
           
            $ctrl=igk_getctrl($ctrl);
            if($ctrl && igk_is_conf_connected()){
                $ctrl->initDb();
                igk_notifyctrl()->addSuccess(__("init db"));
            }
            igk_nav_session(); 
        };
        $_data["resetDb"]=function($ctrl) use ($n, $_api){
            $ctrl=igk_getctrl($ctrl);
            if($ctrl && igk_is_conf_connected() && $_api->datadb("resetctrldb", $ctrl)){                
                igk_notifyctrl()->addSuccess(__("reset db"));
            }else 
                igk_notifyctrl()->addError(__("failed reset db"));
            igk_nav_session();
        };
        $file=IO::GetDir(dirname(__FILE__)."/.ctrl.inc");
        if(file_exists($file)){
            include_once($file);
        }
        if(isset($_data[$cmd])){
            $f=$_data[$cmd];
            return call_user_func_array($f, $args);
        }
        else{
            $fclist=$_data;
            include(IGK_LIB_DIR."/.igk.fc.call.inc");
        }
        $doc=igk_get_document($this, true);
        $doc->body->addBodyBox()->clearChilds()->add($n);
        $doc->renderAJX();
        igk_exit();
        return 1;
    }
    ///<summary>represent a function database function list</summary>
    /**
    * represent a function database function list
    */
    public function datadb($cmd=null){       
        /**
         * @var ?array $_data 
         * @var ?array $_data 
         */

        $file = self::LIBNAME;
        if(file_exists($file)){
            include_once($file);
        }
        $args=array_slice(func_get_args(), 1);
        
        $_api=$this;
        $_data=array(
            "gentoken"=>function($cmd, $args) use ($_api){
                    if(igk_server_request_onlocal_server()){
                        igk_wln("/!\\ Request on local server");
                        return null;
                    }
                    $s=igk_new_id();
                    $_api->setParam("var::OpToken", $s);
                    igk_wl($s);
                    igk_exit();
                },
            "syncfrom"=>function($cmd, $args) use ($_api){
                    $rep=igk_create_node("response");
                    $error=false;
                    $ctrl=igk_getctrl(igk_getv($args, 1));
                    $u=igk_get_user_bylogin(igk_getv($args, 2));
                    $g = "";
                    $srv=($srv=igk_getv($args, 0)) ? $srv: igk_getr("clServer");
                    if(!$ctrl || !$u || empty($srv)){
                        $error=true;
                        $rep->addNode("Status")->Content=-1;
                        $rep->addNode("message")->Content="Ctrl, Server or User is not found";
                    }
                    else{
                        $c=null;
                        $token=igk_curl_post_uri(igk_str_rm_last($srv, '/')."/api/v2/datadb/gentoken");
                        if($token !== false){
                            $c=igk_curl_post_uri(igk_str_rm_last($srv, '/')."/api/v2/datadb/syncdata", array(
                        "clCtrl"=>$ctrl->getName(),
                        "clLogin"=>$u->clLogin,
                        "clClearS"=>1,
                        "clToken"=>$token
                    ));
                        }
                        header("Content-Type: application/xml");
                        if(empty($c)){
                            $rep->addNode("Message")->Content="can't get server response";
                            $v=igk_post_uri_last_error();
                            if($v){
                                $rep->addNode("ErrorCode")->Content=$v["type"];
                                $rep->addNode("ErrorMessage")->Content=$v["message"];
                            }
                        }
                        else{
                            $this->datadb("loadsyncdata", $c, $u->clLogin, $ctrl->getName());
                        }
                        igk_exit();
                        header("Content-Type: application/xml");
                        igk_wl($g);
                        igk_exit();
                    }
                    if(!$error){
                        $rep->addNode("Status")->Content=0;
                    }
                    else{
                        $rep->add(igk_debuggerview());
                    }
                    $rep->RenderXML();
                    return !$error;
                },
            "syncto"=>function($cmd, $args) use ($_api){
                    $rep=igk_create_node("response");
                    $error=false;
                    $ctrl=igk_getctrl(igk_getv($args, 1));
                    $u=igk_get_user_bylogin(igk_getv($args, 2));
                    $srv=($srv=igk_getv($args, 0)) ? $srv: igk_getr("clServer");
                    if(!$ctrl || !$u || empty($srv)){
                        $error=true;
                        $rep->addNode("Status")->Content=-1;
                        $rep->addNode("message")->Content="Ctrl, Server or User is not found";
                    }
                    else{
                        IGKOb::Start();
                        $this->datadb("syncdata", $ctrl->getName(), $u->clLogin);
                        $c=IGKOB::Content();
                        IGKOb::Clear();
                        $g=igk_curl_post_uri($srv."/api/v2/datadb/loadsyncdata", array("data"=>$c, "login"=>$u->clLogin, "ctrl"=>$ctrl->getName()));
                        header("Content-Type: application/xml");
                        igk_wl($g);
                        igk_exit();
                    }
                    if(!$error){
                        $rep->addNode("Status")->Content=0;
                    }
                    else{
                        $rep->add(igk_debuggerview());
                    }
                    igk_wl(igk_xml_header());
                    $rep->renderAJX();
                    return !$error;
                },
            "syncdata"=>function($cmd, $args) use ($_api){
                    if(!igk_server_request_onlocal_server()){
                        $t=igk_getr("clToken");
                        if(empty($t) || ($t != $_api->getParam("var::OpToken"))){
                            igk_wl("<response><erro>1</erro><message>request not form you must have a valid token</message></response>");
                            igk_api_free_session();
                            igk_exit();
                        }
                    }
                    $sync=igk_create_node("igk-sync");
                    $ctrl=igk_getctrl(($c=igk_getv($args, 0)) ? $c: igk_getr("clCtrl"));
                    $uid=igk_get_user_bylogin(($c=igk_getv($args, 1)) ? $c: (($c=igk_getr("clLogin")) ? $c: (($ctrl && ($c=$ctrl->User->clLogin)) ? $c: null)));
                    if($ctrl && $uid){
                        $u=$uid;
                        $tb=igk_db_get_ctrl_tables($ctrl);
                        $apt=igk_get_data_adapter($ctrl->getDataAdapterName());
                        $sync["Controller"]=$ctrl->getName();
                        $sync["namespace"]=$ctrl->Configs->Namespace;
                        $sync["xmlns:igk"]=IGK_WEB_SITE;
                        if($apt->connect()){
                            $tables=(object)array("list"=>array(), "values"=>array());
                            $entries=$sync->addNode("Entries");
                            foreach($tb as $v_tablen){
                                if(!isset($tables->list[$v_tablen]) && $ctrl->Db->getCanSyncDataTable($v_tablen)){
                                    $rep=$sync->addNode(DbSchemas::DATA_DEFINITION)->setAttributes(array("TableName"=>$v_tablen));
                                    $_api->datadb("get_sync_definition", $rep, $v_tablen, $u, $apt, $ctrl->Db, null, $tables);
                                }
                            }
                            foreach($tables->list as $ktb=>$def){
                                $d=(object)$def;
                                if($d->count > 0){
                                    igk_api_sync_def_evaluate_entries($entries, $ktb, $apt, $ctrl->Db, $tables);
                                }
                            }
                            $apt->close();
                            $vd=igk_create_node();
                            igk_hook("system/notify/syncdata/".$ctrl->getName(), $_api, array("node"=>$vd, "user"=>$uid));
                            if($vd->HasChilds){
                                foreach($vd->Childs->to_array() as $l){
                                    switch($l->TagName){
                                        case DbSchemas::DATA_DEFINITION:
                                        $sync->add($l);
                                        break;
                                        case "Entries":
                                        $entries->addRange($l->Childs->to_array());
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    else{
                        igk_wln("/!\\ Args don't match or user not found");
                        igk_exit();
                    }
                    $sync->renderAJX();
                    igk_api_free_session();
                    return;
                },
            "backupdb"=>function($cmd, $args){
                    if(!igk_is_conf_connected()){
                        igk_wln("/!\\ Operation not allowed");
                        igk_exit();
                    }
                    $n= implode("\\", $args);//igk_getv($args, 0);
                    $ctrl=igk_getctrl($n);
                    if($ctrl){
                        $schema=igk_db_backup_ctrl($ctrl, 1);
                        header("Content-Type:application/xml");
                        igk_wl(igk_xml_header()); 
                        igk_wl($schema->render());
                        igk_exit(); 
                    }
                    else{
                        igk_wln("No Ctrl [{$n}] found");
                        igk_wln("usage : /datadb/backupdb/[ctrlName]");
                        $ctrl = igk_get_defaultwebpagectrl();
                        if ($ctrl){
                        igk_wl("<a href=\"".$this->getAppUri("/datadb/backupdb/".get_class($ctrl))."\" >".get_class($ctrl)."</a>");
                        }
                    }
                    igk_exit();
                },
            "loadsyncdata"=>function($cmd, $args) use ($_api){
                    igk_debuggerview()->clearChilds();
                    $rep=igk_create_node("reponse");
                    if(igk_server_request_onlocal_server()){
                        $c=igk_getv($args, 0);
                        $login=igk_getv($args, 1);
                        $u=igk_get_user_bylogin($login);
                        $ctrl=igk_getctrl(igk_getv($args, 2));
                    }
                    else{
                        $c=igk_getr("data");
                        $login=igk_getr("login");
                        $u=igk_get_user_bylogin($login);
                        $ctrl=igk_getctrl(igk_getr("ctrl"));
                    }
                    if(!$u || !$ctrl){
                        $rep->addNode("Status")->Content=-1;
                        $rep->addNode("Error")->Content="user not found or ctrl with that name or namespace not found";
                        $rep->RenderXML();
                        return;
                    }
                    $error=false;
                    $c=preg_replace_callback("#\+@id:/{$u->clLogin}#", function($m) use ($u){
                        return $u->clId;
                    }
                    , $c);
                    $c=preg_replace_callback("#igk-sync#", function($m) use ($u){
                        return IGK_SCHEMA_TAGNAME;
                    }
                    , $c);
                    $n=HtmlReader::LoadXML($c);
                    $p=igk_db_load_data_and_entries_schemas_node($n);
                    if($p == null){
                        $error=true;
                        $rep->addNode("Error")->Content=2;
                        $rep->addNode("Message")->Content="No data entry found";
                        $rep->renderAJX();
                        igk_exit();
                        return;
                    }
                    $p->User=$u;
                    $rowslist=array();
                    foreach($p->Entries as $n=>$e){
                        $rtab=array();
                        foreach($e as $kirow=>$irow){
                            $cirow=& $p->Entries[$n][$kirow];
                            $id=$cirow["igk:id"];
                            unset($cirow["igk:id"]);
                                $rtab[$id ? $id: $ctrl->Db->getSyncIdentificationId($n, $irow, $p)]=array("index"=>$kirow, "row"=>& $cirow);
                        }
                        $rowslist [$n]=$rtab;
                    }
                    $p->Rows=$rowslist;
                    $refs=array();
                    if($ctrl->Db->Connect()){
                        foreach($p->Entries as $k=>$v){
                            if(isset($p->Relations[$k])){
                                $tb=$p->Relations[$k];
                                foreach($v as $krow=>$row){
                                    foreach($tb as $km=>$sm){
                                        $key=strtolower($sm["Table"]."/".$row[$km]);
                                        $i=igk_getv($refs, $key);
                                        if($i == null){
                                            $i=$ctrl->Db->getSyncDataID($sm["Table"], $row[$km], $p);
                                            if(empty($i)){
                                                igk_log_write_i(__FUNCTION__, " data not found for ".$sm["Table"]. ":::".$row[$km]);
                                            }
                                            $refs[$key]=$i;
                                        }
                                        $row[$km]=$i;
                                    }
                                    $v[$krow]=$row;
                                }
                                $p->Entries[$k]=$v;
                            }
                            $ajx=0;
                            foreach($v as  $row){
                                if($ajx)
                                    igk_flush_write_data("insert in $k");
                                $ctrl->Db->insertIfNotExists($k, $row);
                            }
                            if($ajx)
                                igk_flush_data();
                        }
                        $ctrl->Db->close();
                    }
                    if(!$error){
                        $rep->addNode("Status")->Content=0;
                    }
                    $rep->add(igk_debuggerview());
                    $rep->renderAJX();
                    igk_exit();
                },
            "updatedb"=>function($cmd, $args) use ($_api){
                    $n=igk_getv($args, 0);
                    $ctrl=igk_getctrl($n);
                    if($ctrl){
                        $schema=igk_db_backup_ctrl($ctrl);
                        $tables = igk_getv($ctrl->loadDataFromSchemas(),"tables"); 
                        $ctrl::initDb(false);
                        igk_db_restore_backup_data($ctrl, $schema);
                    }
                    else{
                        $d=igk_create_node("div");
                        $d->addObData(function(){?> Usage : update controller db<?php });
                        $d->renderAJX();
                    }
                },
            "help"=>function() use ( & $_data, $_api){
              
                    $doc=igk_get_document($_api);
                    $doc->Title="Api - MYSQL ";
                    igk_google_addfont($doc, "Roboto");
                    $bbox=$doc->body->addBodyBox()->clearChilds();
                    $bbox["class"]="google-Roboto";
                    
                    $b=$bbox->div();
                    $b->container()->addSingleRowCol()->addSectionTitle(4)->Content=__("API DataDB Command list");
                    $b=$bbox->div()->container()->addRow();
                    $buri=$this->getAppUri();
                    foreach(array_keys($_data) as $k){
                        $b->addCol()->div()->addA($buri.'/datadb/'.$k)->Content=$k;
                    }
                    $hdiv=null;
                    $row=null;
                    $fcs=get_defined_functions();
                    $gtab=$fcs["user"];
                    sort($gtab);
                    foreach($gtab as $b=>$m){
                        if(preg_match("/^igk_api_mysql_(?P<name>(.)+)$/i", $m, $tab)){
                            if($hdiv == null){
                                $hdiv=$bbox->div();
                                $hdiv->container()->addRow()->addCol()->addSectionTitle(4)->setContent(__("MySQL DataBase Command Line"));
                                $row=$hdiv->div()->container()->addRow();
                            }
                            $b=$row->addCol()->div();
                            $b->addA($buri.'/datadb/'.$tab["name"])->setContent($tab["name"]);
                            $b->div()->Content=__("help.api.mysql.".$tab["name"]);
                        }
                    }
                    $doc->renderAJX();
                    igk_exit();
                }
        );
        if(isset($_data[$cmd])){
            $f=$_data[$cmd];
            return call_user_func_array($f, array($cmd, $args));
        }
        else{
            if(empty($cmd)){
                $help=$_data["help"];
                return call_user_func_array($help, array());
            }
            else{
                $f="igk_api_mysql_".str_replace("-", "_", $cmd);
          
                if(!function_exists($f)){
                    // igk_ilog(__FUNCTION__."::", "function {$f} not exists");
                    igk_wln_e("function [$f] not exists in ".$file);
                }
                else{
                    $tab=array();
                    $tab[]=$this;
                    $tab=array_merge($tab, $args);
                    $g=new ReflectionFunction($f);
                    if($g->getNumberOfRequiredParameters() > count($tab)){
                        igk_wln_e($f, "Require more parameters ");
                    }
                    return call_user_func_array($f, $tab);
                }
            }
        }
        igk_exit();
        return 1;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function endRequest(){
        $node=HtmlNode::CreateWebNode("APIResponse");
        if($this->ConfigCtrl->IsConnected){
            $this->ConfigCtrl->logout(false, true);
            $node->Content="OK";
        }
        $node->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getBasicUriPattern(){
        return IGK_API_URI;
    }
   
    protected function getEntryNameSpace(){
        return "IGKApi";
    }
    ///<summary></summary>
    /**
    * 
    */
    
    ///<summary></summary>
    /**
    * 
    */
    public function getIsVisible():bool{
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getName():string{
        return IGK_API_CTRL;
    }
    public function getAppName(){
        return IGK_API_CTRL;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getRegUriAction(){
        return IGK_API_URI.IGK_REG_ACTION_METH_OPTIONS;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getVersion(){
        return IGK_API_VERSION;
    }
    ///<summary></summary>
    ///<param name="function"></param>
    /**
    * 
    * @param mixed $function
    */
    public function IsFunctionExposed($function){
        return true;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function request(){
        $u=igk_getr("u");
        $pwd=igk_getr("pwd");
        $node = igk_create_xmlnode("response");
        $this->ConfigCtrl->logout(false, true);
        if(!$this->ConfigCtrl->IsConnected){
            $this->ConfigCtrl->connect($u, $pwd, false);
        }
        if($this->ConfigCtrl->IsConnected){
            session_start();
            $q=base64_decode(igk_getr("q"));
            igk_resetr();
            igk_loadr($q);
            $node->add("ExecutionResponse")->Content=$this->App->ControllerManager->InvokeFunctionUri($q);
            $this->ConfigCtrl->logout(false, true);
        }

        igk_exit();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function sendRequest(){
        $node=HtmlNode::CreateWebNode("APIResponse");
        $q=base64_decode(igk_getr("q"));
        $node->add("Connected")->Content=igk_parsebool($this->ConfigCtrl->IsConnected);
        $node->add("Request")->Content=$q;
        if($q){
            igk_resetr();
            igk_loadr($q);
            $node->add("ExecutionResponse")->Content=$this->App->ControllerManager->InvokeFunctionUri($q);
        }
        $node->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="cmd" default="null"></param>
    /**
    * 
    * @param mixed $cmd the default value is null
    */
    public function setup($cmd=null){
        igk_wln(__FUNCTION__." command");
        igk_exit();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function sysversion(){
        ob_clean();
        igk_wl(IGK_VERSION);
        igk_exit();
    }
}