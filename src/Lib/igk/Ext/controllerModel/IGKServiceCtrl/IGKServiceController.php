<?php
// @file: IGKServiceController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Controllers\ControllerExtension;
use IGK\Controllers\ILibaryController;
use IGK\Helper\IO;
use IGK\Helper\ViewHelper;
use IGK\Resources\R;
use IGK\System\Database\IDatabaseHost;
use IGK\System\IO\File\WsdlFile;
use function igk_resources_gets as __;


!defined("IGK_SERVICE_BASE_URI") && define("IGK_SERVICE_BASE_URI", "services");
///<summary>represent a wsdl service controller type </summary>
/** @package  */
abstract class IGKServiceController 
    extends \IGK\Controllers\ControllerTypeBase 
    implements ILibaryController, IDatabaseHost
{
    const DOC_ID = "sys://documents/services";
    private static $sm_services=[];
   

    ///<summary>Represente __getMethodParameter function</summary>
    ///<param name="method"></param>
    private function __getMethodParameter($method){
        if(empty($method) || !method_exists($this, $method))
            return null;
        $rf=new ReflectionMethod($this, $method);
        return $rf->getParameters();
    }
    ///<summary>get available function lists</summary>
    private function _getAvailableFuncs($new=false, $funcrequest=null){ 
        return $this->getExposedServiceFunction(); 
    }
    ///<summary>Represente _initCssStyle function</summary>
    protected function initCssStyle(){                    
        igk_ctrl_bind_css_file($this,ViewHelper::CurrentDocument()->getTheme(), dirname(__FILE__)."/Styles/default.pcss", 1);  
        ControllerExtension::bindCssStyle($this);
    }
    protected function _configureDocument($doc){
        $doc->setHeaderColor("#af104f");
    }
    
   
    ///<summary>Represente _viewDoc function</summary>
    private function _viewDoc(){ 
        $doc=igk_get_document(self::DOC_ID, true);
        igk_set_env("sys://designMode/off", 10);
        $doc->setParam("sys://designMode/off", 1);
        $doc->Title=R::ngets("title.app_2", $this->ServiceName, $this->App->Configs->website_title);
        // $doc->Favicon=new IGKHtmlRelativeUriValueAttribute(igk_io_baseRelativePath($this->getDataDir()."/R/Img/favicon.ico"));
        if (file_exists($fav = $this->getDataDir()."/R/Img/favicon.ico"))
            igk_doc_set_favicon($doc, $fav); 
        igk_html_rm($this->TargetNode); 
        $this->_configureDocument($doc);   
        $div=$doc->Body->getBodyBox()->clearChilds();
        $this->setCurrentView("default", true, null, array("doc"=>$doc));
        $div->add($this->TargetNode);
        $c=$doc->Body->addNodeCallback("debug-z", function($t){
            return $t->div()->setId("debug-z");
        });
        $doc->Body->addScriptContent("main-ss", "ns_igk.configure({nosymbol:1});");
        $doc->setParam("no-script", 0);
        $doc->renderAJX();
    }
    
    ///<summary>Represente baseEvaluateUri function</summary>
    public final function baseEvaluateUri(){
        $dir = dirname(__FILE__);
	 	$f=$dir."/".IGK_VIEW_FOLDER."/default.phtml";
        $doc= igk_get_document(self::DOC_ID);
        $doc->setParam("sys://designMode/off", 1);
        $doc->title = "Services";
        $t=igk_create_node("div");
        igk_doc_set_favicon($doc, $dir."/Data/R/favicon.ico");
 

        $this->_configureDocument($doc); 
        $doc->addTempScript($dir."/Scripts/services.js");
        $t->clearChilds();
        $this->regSystemVars(null);
        $this->regSystemVars(array("services"=>$this->getServices()));
        $bck=$this->TargetNode;
        $this->setTargetNode($t);
        $this->_include_view($f);
        $this->setTargetNode($bck);
        $doc->body->getBodyBox()->add($t);
        $doc->renderAJX();
        $u=igk_io_fullBaseRequestUri();
        igk_set_session_redirection($u);
        igk_exit();
    }
    ///<summary>Represente bindNodeClass function</summary>
    ///<param name="t"></param>
    ///<param name="fname"></param>
    ///<param name="css_def" default="null"></param>
    protected function bindNodeClass($t, $fname, $css_def=null){
        $m=igk_getv(igk_get_env(IGK_ENV_INVOKE_ARGS), "m");
        if($m == "global"){
            $this->_initCssStyle();
        }
        else
            parent::bindNodeClass($t, $fname, $css_def);
    }
    ///<summary>Represente cachewsl function</summary>
    public function cachewsl(){
        $c = igk_getbool($this->Configs->get("clServiceDisableWSDLCache"));
        $this->Configs->clServiceDisableWSDLCache =!$c;
        $this->storeConfigSettings(); 
        igk_navto($this->getServiceUri());
    }
    ///<summary>Represente clearwsdl_cache function</summary>
    public function clearwsdl_cache(){
        $tab=array();
        $d=ini_get("soap.wsdl_cache_dir");
        $s=igk_io_getfiles($d, "/(.)+wsdl(.)+/i", 1);
        foreach($s as $f){
            @unlink($f);
        }
        if ($ref = igk_server()->HTTP_REFERER){
            igk_navto($ref);
        }else {
            igk_nav_session();
        }
    }
    ///<summary>Represente controllerLoaded function</summary>
    public static function controllerLoaded(){
        igk_wln_e(__FILE__.':'.__LINE__, "getConrollerLoaded", "services", igk_count(self::$sm_services));
    }
    ///<summary>Represente evaluateUri function</summary>
    public final function evaluateUri(){
    

        ($inf=igk_sys_ac_getpatterninfo()) || die("uri evaluation setting action not resolved");
        $p=$inf->getQueryParams();
        $c=igk_getv($p, "function");
        $p=igk_getv($p, "params");
        header("content-type: text/html");
        igk_set_session_redirection($this->getServiceUri());
        $u=igk_io_request_uri();
        if(preg_match('#\$metadata$#i', trim($u))){
            igk_set_header('404 not found');
            igk_exit();
        }

        if(isset($_SERVER["HTTP_SOAPACTION"]) || !strstr(igk_server()->HTTP_USER_AGENT, "MS Web Services Client Protocol")){
            if(empty($c)){
                ob_clean();
                $this->renderDefaultDoc();
                igk_exit();
            }
            else{
               
                if(method_exists($this, $c)){
                    ini_set('default_charset', "utf-8");
                    if(is_array($p) == false)
                        $p=array($p);
                    call_user_func_array(array($this, $c), $p);
                }
                else{
                    $this->renderError($c);
                    igk_exit();
                } 
            }
        }
        
        ob_clean();
        igk_set_header('200', "Content-Type: application/xml");
        $this->wsdl();
        igk_exit();
    }
    ///<summary></summary>
    private function generate_wsdl(){
        $b=$this->getWsdlFile();
        $n=$this->getServiceName();
        $g=new WsdlFile($n, igk_io_baseUri()."/".IGK_SERVICE_BASE_URI."/".$n, array("nsprefix"=>"igkns", "nsuri"=>"http://www.igkdev.com"));
        $g->initService($n, array("doc"=>$this->getServiceDescription()));
        $fc=$this->getDataDir()."/.funclist.xml";
        if(file_exists($fc))
            @unlink($fc);
        $this->init_wsdl($g);
        $g->Save($b);
    }
    ///<summary>Represente GetAdditionalConfigInfo function</summary>
    public static function GetAdditionalConfigInfo(){
        return array(
            "clServiceName"=>(object)array("clType"=>"text", "clRequire"=>1, "default"=>function($o){
				return igk_getv($o, "Name");
			}),
            "clServiceDescription"=>(object)["clDescription"=>"service custom description"],
            "clServiceDisableWSDLCache"=>(object)["clType"=>"bool"]
        );
    }
    ///<summary>get de default string content</summary>
    public static function GetAdditionalDefaultViewContent(){
        return <<<EOF
<?php
\$f = \$this->getParentView("viewfuncs.phtml");
include(\$f); 
EOF;
    }
    ///SERVICE FUNC
    public function getDesc($method){
        $t=igk_create_node("div");
        $t->div()->article($this, $method.".desc");
        $c=$this->__getMethodParameter($method);
        if(igk_count($c) > 0){
            $dv=$t->div()->setClass("args")->setStyle("background-color:#efefef");
            $table=$dv->addTable();
            $r=$table->addTr();
            $r->addTh()->Content=__("Name");
            $r->addTh()->Content=__("Type");
            $r->addTh()->Content=__("Description");
            $cf=$this->getArticlesDir()."/".$method.".json";
            $store=0;
            if(!file_exists($cf)){
                $store=1;
                $jdata=igk_createObj();
            }
            else{
                $s=igk_io_read_allfile($cf);
                $jdata=igk_json_parse($s);
            }
            $lg=R::GetCurrentLang();
            foreach($c as $v){
                $nn=$v->name;
                if ($v->isPassedByReference()){
                    $nn = " & ".$nn;
                }
               
                if ($v->isOptional()){
                    $nn = $nn."?";
                }
                if ($v->isVariadic()){
                    $nn = "...".$nn;
                }
                if ($v->isDefaultValueAvailable()){
                    if ($v->isDefaultValueConstant()){
                        $nn = $nn ." = ".$v->getDefaultValueConstantName();
                    }else 
                         $nn = $nn ." = ".$v->getDefaultValue();
                }
                $r=$table->addTr();
                $r->addTd()->Content=$nn;
                $r->addTd()->Content= ($type = $v->getType()) ? IGKType::GetName($type) : "mixed";
                $v=igk_getv($jdata, $nn);               
                $r->addTd()->Content=($v) ? igk_getv($v, $lg): IGK_HTML_SPACE;
                if($store){
                    $jdata->$nn=(object)array($lg=>"");
                }
            }
            if($store){
                igk_io_save_file_as_utf8_wbom($cf, json_encode($jdata));
            }
        }
        return $t->render();
    }
    ///<summary>Represente getExposedServiceFunction function</summary>
    public function getExposedServiceFunction(){
        $funclist= [];
        $cl = get_class($this); 
        foreach((igk_sys_reflect_class(get_class($this)))->getMethods(
            ReflectionMethod::IS_PUBLIC
        ) as $md){
            $n = $md->getName(); 
            if ( (strpos($n, "__")===0) || ($md->getDeclaringClass()->name!=$cl)){
                continue;
            } 
            $funclist[] = $md->getName();
        }  
        return $funclist;
    }
    ///<summary>Represente getExtra function</summary>
    ///<param name="m"></param>
    public function getExtra($m){
        $n=igk_create_node("div");
        $f=$this->getArticlesDir()."/".$m.".json";
        if(file_exists($f)){
            $n->addJSAExtern("openFile", igk_io_to_uri($f))->setClass("igk-btn igk-btn-default igk-active")->Content=R::ngets("btn.Edit");
        }
        $s=$n->render();
        return $s;
    }
    ///<summary>Represente getParentArticle function</summary>
    ///<param name="n"></param>
    public final function getParentArticle($n){
        if(($s=realpath($n)) == $n)
            return $n;
        $g=IO::GetDir(dirname(__FILE__)."/".IGK_ARTICLES_FOLDER. "/".$n);
        if(file_exists($g))
            return $g;
        return null;
    }
    ///<summary>Represente getParentView function</summary>
    ///<param name="n"></param>
    public final function getParentView($n){
        if(realpath($n) == $n)
            return $n;
        $g=IO::GetDir(dirname(__FILE__)."/".IGK_VIEW_FOLDER. "/".$n);
        if(file_exists($g))
            return $g;
        return null;
    }
    ///<summary>Represente getRootUri function</summary>
    public function getRootUri(){
        return igk_io_baseUri()."/".IGK_SERVICE_BASE_URI;
    }
    ///<summary>Represente getServiceDescription function</summary>
    public function getServiceDescription(){
        return $this->getConfigs()->get( "clServiceDescription");
    }
    ///<summary>Represente getServiceName function</summary>
    public function getServiceName(){
        return \IGK\System\Configuration\CacheConfigs::GetCachedOption($this, "clServiceName"); // strtolower($this->getConfigs()->get( "clServiceName"));
    }
    ///<summary>Represente getServices function</summary>
    public final function getServices(){
        return self::$sm_services;
    }
    ///<summary>Represente getServiceUri function</summary>
    ///<param name="d" default="null"></param>
    public function getServiceUri($d=null){
        return $this->getRootUri()."/".$this->getServiceName(). (($d) ? "/".$d: null);
    }
    ///<summary>Represente getServiceViewTitle function</summary>
    ///<param name="m"></param>
    public function getServiceViewTitle($m){
        $n=igk_create_node("div")->setClass("title");
        $info = new ReflectionMethod($this, $m);
        $ctype = "";
        if ($rtype = $info->getReturnType()){
            $ctype = ": <span class=\"rtype\">".IGKType::GetName($rtype)."<span>";
        }
        $content = $m.$ctype;
        if(igk_is_conf_connected()){
            $c=$this->__getMethodParameter($m);
            $ct=igk_count($c);
            $n->addA("#")->setAttribute("onclick", "javascript: ns_igk.services.invoke('{$m}',{$ct}); return false;")->Content=  $content;
        }
        else{
            $n->Content= $content ;
        }
        return $n->render();
    }
    ///<summary>Represente getWsdlFile function</summary>
    protected function getWsdlFile(){
        return igk_dir($this->getDataDir()."/service.wsdl");
    }
    ///<summary>start server function</summary>
    protected function init_server(){
        ini_set("soap.wsdl_cache_enabled", $this->Configs->clServiceDisableWSDLCache ? "0": "1");
        $header=igk_get_allheaders();
        $b=$this->getWsdlFile();
        if(!file_exists($b)){
            $this->generate_wsdl(); 
        }
        $this::register_autoload();
       // igk_wln_e("init server".get_class($this). " ", "disable : ".$this->Configs->clServiceDisableWSDLCache);
        $srvu=$this->getServiceUri();
        $srv=new SoapServer($b, array("uri"=>$srvu));
        $srv->setClass(get_class($this));
        $srv->handle();
        if(!isset($header ["SOAPACTION"]))
            return;
        $sc=ob_get_contents();
        if(!strstr($sc, 'SOAP-ENV:Envelope')){
            $r=igk_create_xmlnode("response");
            $r->addXmlNode("error")->Content="Failed to execute ".$_SERVER["HTTP_SOAPACTION"];
            $t=$r->addXmlNode("info");
            $t->addXmlNode("post_max_size")->Content=ini_get("post_max_size");
            $t->addXmlNode("post_length")->Content=igk_get_sizev(igk_getv($_SERVER, "CONTENT_LENGTH"));
            $r->RenderXML();
        }
        igk_exit();
    }
    ///<summary>Represente init_wsdl function</summary>
    ///<param name="wsdl"></param>
    protected function init_wsdl($wsdl){
        $cl=get_class($this);
        $funclist=$this->getExposedServiceFunction();
        $wsdl->registerMethod($cl, $this->getServiceName(), $funclist);
    }
    ///<summary>Represente InitComplete function</summary>
    protected function initComplete($context=null){
        parent::initComplete();
        $this->register_service();       
    }
    ///<summary>init service environment</summary>
    public static function InitEnvironment($ctrl){
        if(!is_object($ctrl) || !igk_reflection_class_extends(get_class($ctrl), __CLASS__))
            return 0;
        $ctrl->generate_wsdl();
        return 1;
    }
    ///<summary>Represente initRowView function</summary>
    ///<param name="m"></param>
    public function initRowView($m){
        $this->getExtra($m);
    }
    ///<summary>Represente IsExposedServiceFunction function</summary>
    ///<param name="fn"></param>
    public final function IsExposedServiceFunction($fn){
        $tab=igk_get_env("sys://services/".get_class($this)."/notexposed");
        if(isset($tab[strtolower($fn)])){
            return 0;
        }
        return preg_match("/^(view|getname|getdb|clearwsdl_cache|getusedataschema)$/i", $fn) ? 0: 1;
    }
    ///<summary>Represente pageFolderChanged function</summary>
    protected function pageFolderChanged(){    }
    ///<summary>Represente register_service function</summary>
    private function register_service(){
        $c="^/".IGK_SERVICE_BASE_URI."/".$this->getServiceName();
        // $k=$this->getEnvParam("appkeys");
        // if(!empty($k)){
        //     igk_sys_ac_unregister($k);
        // }
        // if($c){
        //     $k="".$c.IGK_REG_ACTION_METH;
        //     igk_sys_ac_register($k, $this->getUri("evaluateUri"));
        //     $this->setEnvParam("appkeys", $k);
        // }
        // $t=& self::$sm_services;
        // if($t == null){
        //     $t=array();
        //     $c="^/".IGK_SERVICE_BASE_URI."(/){0,1}$";
        //     $f=$this->getUri("baseEvaluateUri&m=global");
        //     igk_sys_ac_register($c, $f);
        //     self::$sm_services=& $t;
        // }
        // $t[]=$this;
    }
    ///<summary>Represente renderDefaultDoc function</summary>
    protected function renderDefaultDoc(){
        $this->init_server();
        $this->_viewDoc();;
        igk_exit();
    }
    ///<summary>Represente renderError function</summary>
    ///<param name="c"></param>
    public function renderError($c){
        $doc=igk_get_document("sys:://document/services");
        $doc->Title="!Error - ".$this->getServiceName();
        $bbox=$doc->body->getBodyBox();
        $bbox->clearChilds();
        $doc->renderAJX();
    }
    ///<summary>Represente SetAdditionalConfigInfo function</summary>
    ///<param name="t" ref="true"></param>
    public static function SetAdditionalConfigInfo(& $t){
        $t["clServiceName"]=igk_getr("clServiceName");
        $t["clServiceDescription"]=igk_getr("clServiceDescription");
        $t["clServiceDisableWSDLCache"]=igk_getr("clServiceDisableWSDLCache");
        if(empty($t["clServiceName"])){
            return false;
        }
        return 1;
    }
    ///<summary>call this function to generate a wsdl file</summary>
    public function wsdl($a=null, $appxml=1){
        // igk_wln_e("l:::DATA");
        $b=$this->getWsdlFile();
        if(($a && igk_is_conf_connected()) || !file_exists($b)){
            $this->generate_wsdl($b);
            if(igk_getr("r") == 1){
                igk_navto($this->getServiceUri());
            }
        }
        $s=IO::ReadAllText($b);
        if($appxml)
            header("Content-Type: application/xml");
        igk_wl($s);
        igk_exit();
    }
    public function refresh_wsdl(){
        $this->generate_wsdl();
        igk_navto($this->getServiceUri()); 
    }
    public function present_title(){
        return "SAMMMM";
    }
}
igk_set_env("sys://controller/loaded", array("IGKServiceCtrl", "controllerLoaded"));