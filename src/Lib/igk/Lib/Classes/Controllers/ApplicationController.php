<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApplicationController.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/CacheConfigs.php";
require_once IGK_LIB_CLASSES_DIR . "/System/Database/IDatabaseHost.php";


use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\Models\Groups;
use IGK\Resources\R;
use IGK\System\Database\IDatabaseHost;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\UriActionException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Http\WebResponse;
use IGKDbUtility;
use IGKException;
use IGKGD;
use IGKHtmlDoc;
use ReflectionException;
use ReflectionMethod;
use function igk_resources_gets as __;

///<summary>base application controller</summary>
abstract class ApplicationController extends  PageControllerBase
implements IDatabaseHost
{
    const IGK_CTRL_APPS_KEY = IGK_USER_SETTING + 0xA0;
    const IGK_CTRL_APP_INIT = self::IGK_CTRL_APPS_KEY + 1;
    const IGK_CTRL_APP_TEMPLATE = self::IGK_CTRL_APPS_KEY + 2;
    private static $INIT;
    private static $sm_apps;

    ///<summary></summary>
    ///<param name="news" default="false"></param>
    ///<param name="funcrequest" default="null"></param>
    /**
     * 
     * @param mixed $news the default value is false
     * @param mixed $funcrequest the default value is null
     */
    private function _getfunclist($news = false, $funcrequest = null)
    {
        return igk_sys_getfunclist($this, $news, $funcrequest);
    }
    ///<summary> override this method to handle shortcut evaluationUri according to function and param</summary>
    ///<return> true if handled otherwise false</return>
    /**
     *  override this method to handle shortcut evaluationUri according to function and param
     */
    protected function _handle_uri_param($fc, $param, $options = null)
    {
        return false;
    }
    ///<summary>override to create the application db utility intance </summary>
    /**
     * override to create the application db utility intance
     */
    protected function _createDbUtility()
    {
        return new IGKDbUtility($this);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function administration()
    {
        $doc = $this->getAppDocument();
        $div = $doc->body->clearChilds()->div();
        $div["class"] = "igk-notify igk-notify-warning";
        $div["style"] = "display:block; position:absolute; top:50%; min-height:96px; margin-top:-48px;";
        $div->Content = "No administration page";
        $div = $doc->body->div();
        $div["style"] = "font-size: 3em; ";
        $div->addA($this->getAppUri(""))->setClass("glyphicons no-decoration")->Content = "&#xe021;";
        $doc->renderAJX();
    }
    ///<summary></summary>
    ///<param name="func"></param>
    ///<param name="args"></param>
    /**
     * 
     * @param mixed $func
     * @param mixed $args
     */
    protected function bind_func($func, $args)
    {
        if ($func) {
            $cl = get_class($this);
            if (method_exists($cl, $func)) {
                call_user_func_array(array($this, $func), $args);
                return true;
            }
        }
        return false;
    }
    ///<summary>check before controller add</summary>
    /**
     * check before controller add
     */
    public static function CheckBeforeAddControllerInfo($request)
    {
        $title = igk_getv($request, IGK_CTRL_CNF_TITLE);
        $appname = strtolower(igk_getv($request, IGK_CTRL_CNF_APPNAME));
        $c = self::GetApps();
        if (isset($c->apps[$appname]) && igk_is_class_incomplete($c->apps[$appname])) {
            unset($c->apps[$appname]);
        }
        if (empty($title) || empty($appname) || !preg_match(IGK_IS_FQN_NS_REGEX, $appname) || isset($c->apps[$appname])) {
            return false;
        }
        return true;
    }
    ///<summary></summary>
    ///<param name="funcname"></param>
    /**
     * 
     * @param mixed $funcname
     */
    protected final function checkFunc($funcname)
    {
        if (igk_is_conf_connected() || $this->UserAllowedTo($funcname))
            return true;
        igk_notifyctrl()->addWarning(R::ngets("warning.usernotallowed_1", $funcname));
        igk_navto($this->getAppUri(""));
        igk_exit();
        return false;
    }

    ///<summary></summary>
    ///<param name="node" default="null"></param>
    /**
     * 
     * @param mixed $node the default value is null
     */
    public function conffunctions($node = null)
    {
        if (!igk_is_conf_connected()) {
            igk_navto($this->getAppUri());
            igk_exit();
        }
        if ($node == null) {
            $doc = $this->getAppDocument();
            $doc->Title = __("Configure Functions - [{0}]", igk_configs()->website_domain);
            $bbox = $doc->body->getBodyBox();
            $bbox->addIGKAppHeaderBar($this);
            $bbox->addMenuBar();
            $this->conffunctions($bbox);
            $doc->renderAJX();
            return;
        }
        $d = $node->div();
        $tab = $d->addTable();
        foreach (igk_sys_getall_funclist($this) as  $v) {
            $tr = $tab->add("tr");
            $tr->add("td")->addSpace();
            $tr->add("td")->Content = $v->clName;
            $i = $tr->add("td")->addInput("meth[]", "checkbox");
            $i["value"] = $v->clName;
            if ($v->clAvailable) {
                $i["checked"] = "checked";
            }
        }
    }
    ///<summary></summary>
    ///<param name="clear" default="false"></param>
    /**
     * 
     * @param mixed $clear the default value is false
     */
    public function createNewDoc($clear = false)
    {
        $key = $this::name("app_document");
        $doc = $this->getEnvParam($key);
        if ($doc == null) {
            $doc = IGKHtmlDoc::CreateDocument($key);
            $this->setEnvParam($key, $doc);
        }
        $doc->Title = $this->AppTitle;
        if ($clear)
            $doc->body->clearChilds();
        else
            $doc->body->getBodyBox()->clearChilds();

        return $doc;
    }
    ///<summary></summary>
    /**
     * 
     */
    public final function dbinitentries()
    {
        $s = igk_is_conf_connected() || $this->IsUserAllowedTo(igk_ctrl_auth_key($this, __FUNCTION__));
        if (!$s) {
            igk_notifyctrl()->addErrorr("err.operation.notallowed");
            igk_navto($this->getAppUri());
        }
        if ($this->getUseDataSchema()) {
            $r = $this->loadDataAndNewEntriesFromSchemas();
            $tb = $r->Data;
            $etb = $r->Entries;
            $ee = igk_create_node();
            $db = igk_get_data_adapter($this, true);
            if ($db) {
                if ($db->connect()) {
                    foreach ($tb as $k => $v) {
                        $n = igk_db_get_table_name($k);
                        $data = igk_getv($etb, $k);
                        if (igk_count($data) == 0)
                            continue;
                        if ($db->tableExists($n)) {
                            foreach ($data as $vv) {
                                igk_db_insert_if_not_exists($db, $n, $vv);
                                if ($db->getHasError()) {
                                    $dv = $ee->div();
                                    $dv->addNode("div")->Content = "Code : " . $db->getErrorCode();
                                    $dv->addNode("div")->setClass("error_msg")->Content = $db->getError();
                                }
                            }
                        } else {
                            $r = $db->createTable($n, igk_getv($v, 'ColumnInfo'), $data, igk_getv($v, 'Description'), $db->DbName);
                        }
                    }
                    $db->close();
                }
            }
        }
        if ($ee->HasChilds) {
            igk_notifyctrl()->addError($ee->render());
        } else {
            igk_hook(igk_get_event_key($this, "dbchanged"), $this);
            $this->logout();
        }
        igk_navto($this->getAppUri());
    }
    ///<summary>drop application table from system config</summary>
    /**
     * drop application table from system config
     */
    protected static function dropDb($navigate = true, $force = false)
    {

        if (!($c = igk_getctrl(static::class, false))) {
            return;
        }
        $s = $force || igk_is_conf_connected() || $c->IsUserAllowedTo($c->Name . ":" . __FUNCTION__);

        if ($s) {
            $args = func_get_args();
            $db = [ControllerExtension::class, __FUNCTION__];
            return $db($c, ...$args);
        }
        igk_hook("sys://drop_app_database", [$c]);
        if ($navigate && igk_app_is_uri_demand($c, __FUNCTION__)) {
            igk_navto($c->getAppUri());
        }
    }
    ///<summary>use to handle redirection uri</summary>
    /**
     * use to handle redirection uri
     */
    public final function evaluateUri()
    {
        $inf = igk_sys_ac_getpatterninfo();
        if ($inf === null) {
            return;
        }
        $this->handle_redirection_uri($inf);
        igk_exit();
    }
    ///<summary>List Exposed Functions</summary>
    /**
     * List Exposed Functions
     */
    public function functions($n = false)
    {
        if (!igk_server_is_local() && !igk_is_conf_connected()) {
            igk_notifyctrl()->addWarningr("warn.noaccessto_1", __FUNCTION__);
            igk_navto($this->getAppUri());
            igk_exit();
        }
        $doc = $this->getAppDocument();
        $doc->Title = R::ngets("title.app_2", "Functions " . $this->getConfig(IGK_CTRL_CNF_TITLE), $this->App->Configs->website_title);
        $d = $bodybox = $doc->body->getBodyBox();
        $d->clearChilds();
        $m = $d->div()->div()->container();
        $r = $m->addRow();
        $cl = get_class($this);
        $ref = igk_sys_reflect_class($cl);
        $sf = $this->getDeclaredFileName();
        $r->div()->setClass("fc_h")->setStyle("font-size:1.4em")->Content = "File : " . igk_io_basepath($sf);
        $m = $d->div()->div()->container();
        $r = $m->addRow();
        $func = $this->_getfunclist($n);
        usort($func, function ($a, $b) {
            return strcmp(strtolower($a), strtolower($b));
        });
        foreach ($func as $k) {
            $b = $r->addCol("igk-col-12-2 igk-sm-list-item")->setStyle("padding-top:8px; padding-bottom:8px")->div();
            $b->addA($this->getAppUri($k))->setContent($k);
        }
        $bodybox->setStyle("position:relative; color: #eee; margin-bottom:300px;padding-bottom:0px; overflow-y:auto; color:indigo;");
        $bodybox->div()->setClass("posfix loc_b loc_r loc_l dispb footer-box igk-fixfitw")->setId("fbar")->setAttribute("igk-js-fix-loc-scroll-width", "1")->setStyle("min-height:80px; z-index: 10; width: auto;");
        $bodybox->div()->setClass("no-visibity dispb")->setAttribute("igk-js-fix-height", "#fbar");
        $b = $bodybox->addActionBar();
        $u = $this->getAppUri("functions/1");
        $b->addButton("btn.init")->setAttribute("value", "init function list")->setAttribute("onclick", "javascript: ns_igk.form.posturi('" . $u . "'); return false;");
        $doc->renderAJX();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function get_data_schemas()
    {
        $u = $this->App->Session->User;
        if (!igk_is_conf_connected() && !$this->IsUserAllowedTo("system/:" . __FUNCTION__)) {
            igk_wln("user not allowed to");
            igk_exit();
        }
        $f = $this->getDataSchemaFile();
        if (file_exists($f)) {
            $s = HtmlReader::LoadFile($f);
            $s->RenderXML();
        } else {
            $d = HtmlNode::CreateWebNode(IGK_SCHEMA_TAGNAME);
            $d->RenderXML();
        }
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function GetAdditionalConfigInfo()
    {
        return array(
            IGK_CTRL_CNF_TITLE => igk_createAdditionalConfigInfo(array("clRequire" => 1)),
            IGK_CTRL_CNF_APPNAME => igk_createAdditionalConfigInfo(array("clRequire" => 1)),
            IGK_CTRL_CNF_BASEURIPATTERN => igk_createAdditionalConfigInfo(array("clRequire" => 1)),
            IGK_CTRL_CNF_TABLEPREFIX => igk_createAdditionalConfigInfo(array("clRequire" => 1, "clDefaultValue" => "tbigk_")),
            IGK_CTRL_CNF_APPNOTACTIVE => (object)array("clType" => "bool", "clDefaultValue" => "0")
        );
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function GetAdditionalDefaultViewContent()
    {
        return <<<EOF
<?php
use IGK\\Resources\\R;
\$t->clearChilds();
\$t->div()->addSectionTitle(4)->Content = R::ngets("Title.App_1", \$this->AppTitle);
\$t->inflate(igk_dir(\$dir."/".\$fname));
EOF;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function getAllowViewDirectAccess()
    {
        return 0;
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getAppImgUri()
    {
        return igk_html_resolv_img_uri($this->getDataDir() . IGK_APP_LOGO);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getAppName()
    {
        return $this->getConfig(IGK_CTRL_CNF_APPNAME, static::class);
    }
    ///<summary>get if this application is not active</summary>
    /**
     * get if this application is not active
     */
    public function getAppNotActive()
    {
        return $this->getConfig(IGK_CTRL_CNF_APPNOTACTIVE);
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return *
     */
    public static function &GetApps()
    {
        if (self::$sm_apps === null) {
            // igk_wln_e("application get Apps : call");
            // $m=igk_app()->session->getParam(__METHOD__);
            $m = igk_environment()->get(self::IGK_CTRL_APPS_KEY);
            if ($m === null) {
                // igk_wln("new std array");
                $m = new \stdClass();
                $m->_ = [];
                // $m = (object)array('_'=>array());
                // igk_app()->session->setParam(self::IGK_CTRL_APPS_KEY, $m);
                igk_environment()->set(self::IGK_CTRL_APPS_KEY, $m);
            }
            self::$sm_apps = &$m;
        }
        return self::$sm_apps;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getAppTitle()
    {
        return $this->getConfig(IGK_CTRL_CNF_TITLE);
    }

    ///<summary>Basic uri pattern</summary>
    /**
     * Basic uri pattern
     */
    public function getBasicUriPattern()
    {
        return \IGK\System\Configuration\CacheConfigs::GetCachedOption($this, IGK_CTRL_CNF_BASEURIPATTERN);
    }

    ///<summary>return application uri</summary>
    /**
     * return application uri
     */
    public function getAppUri(?string $function = null): ?string
    {
        if (is_null($function)) {
            $function = "";
        }
        if (!empty($function)) {
            $function = igk_str_rm_start($function, "/");
        }
        if ($function == IGK_DEFAULT_VIEW) {
            $function = "";
        } else {
            if (basename($function) == IGK_DEFAULT_VIEW) {
                $function = dirname($function);
            }
        }

        if ($this::IsEntryController()) {
            if ($subdomain = SysUtils::GetApplicationLibrary("subdomain")) {
                if ($subdomain->subdomain === $this) {
                    $g = $subdomain->subdomainInfo->clView;
                    if (!empty($function) && (stripos($g, $function) === 0)) {
                        $function = substr($function, strlen($g));
                    }
                }
            }
        } else {
            $s = "";
            if ($this->getEnvParam(self::IGK_ENV_PARAM_LANGCHANGE_KEY)) {
                $s .= R::GetCurrentLang() . "/";
            }
            $rt = $this->getRootPattern();
            if ($rt || $s) {
                $function = $s . $rt . (!empty($function) ? "/" . $function : '');
            }
        }
        $buri = igk_io_baseuri() ?? "/";
        if ($function) {
            return igk_str_rm_last($buri, '/') . "/" . $function;
        }
        return $buri;
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getcanAddChild()
    {
        return false;
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getDataTablePrefix()
    {
        return $this->getConfig(IGK_CTRL_CNF_TABLEPREFIX);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDb()
    {
        if (!$db = $this->getEnvParam("dbu")) {
            ($db = $this->_createDbUtility()) || igk_die("failed to create db utility");
            $this->setEnvParam("dbu", $db);
        }
        return $db;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDomainUriAction()
    {
        return "^(/(?P<lang>" . R::GetSupportLangRegex() . "))?" . IGK_REG_ACTION_METH_OPTIONS;
    }
    ///<summary>get exposed functions list</summary>
    /**
     * get exposed functions list
     */
    public function getExposed()
    {
        static $exposed = null;
        if ($exposed === null) {
            $exposed = array("about" => 1, "logout" => 1);
        }
        return $exposed;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsVisible(): bool
    {
        parent::getIsVisible();
        return ControllerExtension::getIsVisible($this); 
        //  parent::__callStatic("invokeMacros", ["getIsVisible", $this]);
        // return $g;
    }
    ///<summary> application by default not allowed global action</summary>
    public function getNoGlobalAction(){
        return true;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getRegInvokeUri()
    {
        return $this->getUri(IGK_EVALUATE_URI_FUNC);
    }

    ///<summary>get sub application app uri </summary>
    /**
     * get sub application app uri
     */
    public function getRegUriAction()
    {
        $primary = $this->getBasicUriPattern();
        if (empty($primary))
            return null;
        $s = "" . $primary . IGK_REG_ACTION_METH;
        if ((strlen($s) > 0) && $s[0] = "^") {
            $s = "^(/:lang)?" . substr($s, 1) . "(;:options)?";
        }
        return $s;
    }
    ///<summary>get base uri pattern configured</summary>
    /**
     * get base uri pattern configured
     */
    protected function getRootPattern()
    {
        $t = array();
        if (empty($broot = $this->getBasicUriPattern())) {
            return null;
        }
        $s = preg_match_all("/(\^\/)?(?P<name>(([^\/]+)(\/([^\/]+)\/?)?))/i", $broot, $t);
        if ($s > 0) {
            $o = $t["name"][0];
            return $o;
        }
        return null;
    }
    ///<summary></summary>
    /**
     * init argument with application's document 
     */
    public function getSystemVars()
    {
        $doc = $this->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY);
        if ($doc === null) {
            if (igk_sys_is_subdomain() && (SysUtils::GetSubDomainCtrl() === $this)) {
                $doc = $this->getAppDocument();
            } else {
                $doc = igk_app()->getDoc();
            }
            $this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
        }
        return parent::getSystemVars();
    }
    ///<summary> base application uri handle</summary>
    /**
     *  base application uri handle
     * @param mixed $forcehandle default is true. will stop the script
     * @param bool $forcehandle default is true. will stop the script
     */
    public function handle_redirection_uri($u, $forcehandle = 1)
    {  
        igk_sys_handle_uri();
        extract(array(
            "page" => 0,
            "k" => 0,
            "pattern" => 0,
            "p" => 0,
            "c" => 0,
            "param" => 0,
            "viewdefault" => 0,
            "query_options" => 0
        ));
        // + | PARSE DATA and extract matching pattern 
        if (is_string($u)) {
            $page = explode("?", $u);
            $k = $this->getDomainUriAction();
            $pattern = igk_pattern_matcher_get_pattern($k);
            $p = igk_pattern_get_matches($pattern, $page[0], array_merge(["lang"], igk_str_get_pattern_keys($k)));
            extract(igk_pattern_view_extract($this, $p, 1));
            igk_ctrl_change_lang($this, $p);
        } else {
            unset($u->ctrl);
            $page = explode("?", $u->uri);
            $pattern = $u->pattern;
            $p = $u->getQueryParams();
            $viewdefault = 1;
            extract(igk_pattern_view_extract($this, $p, 1));
            igk_ctrl_change_lang($this, $p);
        }

        // get request query options
        $query_options = igk_getv($p, 'options');
        //passing ctrl to view for sitepam
        igk_bind_sitemap(["ctrl" => $this, "c" => $c]);

        // include(IGK_LIB_DIR."/Inc/igk_sitemap.pinc");
        $tn = $this->getTargetNode();

        if ($this->_handle_uri_param($c, $param, $query_options)) {
            $forcehandle && igk_exit();
            return;
        }       
        // reset system variable
        $this->regSystemVars(null);
        if (empty($param))
            $param = array();
        else if (!is_array($param)) {
            $param = array($param);
        }
        if (empty($c) &&  $viewdefault) {
            $this->renderDefaultDoc($this->getConfig("/default/document", 'default'));
            igk_exit();
        }
        $doc = $this->getAppDocument();
        $this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
        $this->setEnvParam(IGK_VIEW_OPTIONS, $query_options);
        if (igk_sys_is_subdomain()) {
            //check of uri access ... 
            $actionctrl = igk_getctrl(IGK_SYSACTION_CTRL, true);
            $m = $actionctrl->matche($page[0]);
            $ck = $this->getEnvParam("appkeys");
            if ($m !== null) {
                if ($m->action == $ck) {
                    if ((igk_get_defaultwebpagectrl()) === $this) {
                        $m = "Misconfiguration. Subsequent call of domain controller is not allowed. " . igk_io_request_uri() .
                            "<br />" . $this->getName() .
                            "<br />";
                        throw new UriActionException($m, $u, 0x1a001);
                    } else { 
                        throw new \IGKException("Subdomain request for entry path");
                    }
                } else {
                    $actionctrl->invokeUriPattern($m);
                    $forcehandle && igk_exit();
                    return;
                }
            }
        }
 
        // + | NAVIGATE TO CURRENT VIEW
        $this->setCurrentView($c, true, null, $param, $query_options);         
        $this->resetCurrentView(null);
        if (igk_is_ajx_demand()) {
            igk_ajx_replace_node($tn, "#" . $tn["id"]);
        } else {
            $ctx = $this->getEnvParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY);
            if ($ctx == "docview") {
                igk_app()->getDoc()->renderAJX();
            } else {
                $doc->getBody()->getBodyBox()->clearChilds()->add($tn);
                HtmlRenderer::RenderDocument($doc, 0, $this);
            }
        }
        $forcehandle && igk_exit(); 
    }
    ///<summary></summary>
    ///<param name="code"></param>
    /**
     * 
     * @param mixed $code the default value is 0
     */
    protected function HandleError($code = 0)
    {
        return 0;
    }
    /**
     * init - application macros 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function initMacros()
    {
        if (is_null($cl = $this::resolveClass(\Database\InitMacros::class))){
            return;
        }
        $m = new $cl();
        $m->run(igk_app()->getApplication()->getBuilder());
    }
    /**
     * init macros from file
     * @return void 
     * @throws IGKException 
     */
    protected function _initMacros()
    {  
        if (self::IsSysController(static::class)){
            return;
        }       
        if (\IGK\Models\ModelBase::IsMacrosInitialize()) {
            $this->initMacros();
        } else {
            igk_reg_hook($this::hookName("register_autoload"), function($e){
            // igk_reg_hook(\IGKEvents::HOOK_MODEL_INIT, function () {
                 // $op_start = igk_sys_request_time();
                 // if (\IGK\Models\ModelBase::IsMacrosInitialize()){
                 // + | changed   
                 if (!igk_environment()->NO_PROJECT_AUTOLOAD){
                    $this->initMacros();
                 }
                //}
                // igk_ilog("init macros duration: ". (igk_sys_request_time() - $op_start) . " ".
                // get_class($this));
            });
        }
        
    }
    protected function _registerApp()
    {
        if ($n = get_class($this)) {
            $n = str_replace("\\", ".", $n);
            $c = self::GetApps();

            if (($def = preg_match(IGK_IS_FQN_NS_REGEX, $n)) && !isset($c->_[$n])) {
                $c->_[$n] = $this->getName();
            } else {
                igk_assert_die(
                    !igk_get_env("sys://reloadingCtrl"),
                    "Application identifier is not valid or already register. [{$n}] - " . $def
                );
            }
        }
    }
    ///<summary>init complete</summary>
    /**
     * init application complete    
     * @param mixed $context init object context 
     */
    protected function initComplete($context = null)
    {
        parent::initComplete();
        $this->_initMacros();
        $this->_registerApp();
        $this->_registerAction();
        if (!isset(self::$INIT)) {
            igk_reg_hook(IGK_EVENT_DROP_CTRL, "igk_app_ctrl_dropped_callback");
            self::$INIT = true;
        }
        OwnViewCtrl::RegViewCtrl($this, 0);
    }


    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
     * 
     * @param mixed $ctrl
     */
    public static function InitEnvironment($ctrl)
    {
        IO::CreateDir($ctrl->getDataDir());
        IO::CreateDir($ctrl->getResourcesDir());
        if (igk_app()->application->lib("gd")) {
            $s = IGKGD::Create(256, 128);
            $n = $ctrl->getName();
            if ($s) {
                igk_io_w2file($ctrl->getDataDir() . IGK_APP_LOGO, $s->RenderText(), true);
            }
            igk_io_w2file(
                $ctrl->getDataDir() . IGK_APP_LOGO . ".gkds",
                <<<EOF
<gkds>
  <Project>
    <SurfaceType>IconSurface</SurfaceType>
  </Project>
  <Documents>
    <LayerDocument PixelOffset="None" BackgroundTransparent="True" Width="256" Height="128" Id="{$n}">
      <Layer Id="layer_{$n}">
      </Layer>
    </LayerDocument>
  </Documents>
</gkds>
EOF,
                true
            );
        }
        return true;
    }
    ///<summary>check that if the controller handle base uri</summary>
    /**
     * check that if the controller handle base uri
     */
    public function is_handle_uri($uri = null)
    {
        if (igk_const('IGK_REDIRECTION') == 1) {
            if (preg_match("#^/!@#", igk_io_request_uri()))
                return false;
        }
        return $this->IsActive();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function IsActive()
    {
        $inf = igk_sys_ac_getpatterninfo();
        return (($inf != null) && preg_match(igk_sys_ac_getpattern($this->getBasicUriPattern()), igk_io_rootBaseRequestUri()));
    }
    ///<summary></summary>
    ///<param name="k"></param>
    /**
     * 
     * @param mixed $k
     */
    public function isAuthKeys($k)
    {
        if (preg_match("/^(" . $this->getAuthKey() . ")/", $k))
            return true;
        return false;
    }
    ///<summary>get if function is available</summary>
    /**
     * get if function is available
     */
    protected function IsFuncUriAvailable(&$func)
    {

        $c = new ReflectionMethod($this, $func);
        if (!$c->isPublic()) {
            return false;
        }
        if (igk_is_conf_connected() || isset($this->Exposed[$func]))
            return true;
        $lst = $this->_getfunclist(false, $func);
        if (igk_array_value_exist($lst, $func))
            return true;
        return false;
    }

    ///<summary></summary>
    /**
     * 
     */
    public function load_data()
    {
        $doc = $this->getAppDocument();
        $d = $doc->Body->add("div");
        $frm = $d->addForm();
        $frm["action"] = $this->getAppUri("load_data_files");
        $frm["method"] = "POST";
        $i = $frm->addInput("clFileName", "file");
        $i["class"] = "dispn";
        $i["multiple"] = "false";
        $i["accept"] = "text/xml";
        $i["onchange"] = "this.form.submit(); return false;";
        $frm->addInput("clRuri", "hidden", $this->getAppUri(""));
        $frm->script()->Content = <<<EOF
(function(){var f = \$ns_igk.getParentScriptByTagName('form'); f.clFileName.click();})();
EOF;
        $doc->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function load_data_files()
    {
        if (isset($_FILES["clFileName"])) {
            $f = $this->getDataSchemaFile();
            $dom = HtmlNode::CreateWebNode("dummy");
            $dom->Load(IO::ReadAllText($_FILES["clFileName"]["tmp_name"]));
            $d = $this->getAppDocument();
            $div = $d->Body->add("div");
            if (igk_count($dom->getElementsByTagName(IGK_SCHEMA_TAGNAME)) == 1) {
                igk_io_move_uploaded_file($_FILES["clFileName"]["tmp_name"], $f);
                $div->add("div", array("class" => "igk-title"))->Content = R::ngets("Title.GoodJOB");
                $div->add("div", array("class" => "igk-notify igk-notify-success"))->Content = R::ngets("msg.fileuploaded");
            } else {
                $div->add("div", array("class" => "igk-title"))->Content = R::ngets("Title.Error");
                $div->add("div", array("class" => "igk-notify igk-notify-danger"))->Content = R::ngets("error.msg.filenotvalid");
            }
            $d->renderAJX();
            unset($d);
            unset($dom);
        } else {
            igk_navtobase("/");
        }
        igk_exit();
    }
    ///<summary></summary>
    /**
     * register action bind
     */
    protected final function _registerAction()
    {
        $k = $this->getEnvParam("appkeys");
        if (!empty($k)) {
            igk_sys_ac_unregister($k);
        }
        $k = $this->getRegUriAction();
        if (!empty($k)) {
            igk_sys_ac_register($k, $this->getRegInvokeUri());
            $this->setEnvParam("appkeys", $k);
        }
    }
    ///<summary></summary>
    ///<param name="view" default="'default'"></param>
    ///<param name="doc" default="null"></param>
    ///<param name="render" default="true"></param>
    /**
     * 
     * @param mixed $view the default value is 'default'
     * @param mixed $doc the default value is null
     * @param mixed $render the default value is true
     */
    protected function renderDefaultDoc($view = 'default', $doc = null, $render = true)
    {

        $d = $doc ?? $this->getAppDocument(true);
        // $d= $doc ?? $this->getDoc();// true);
        if ($d === igk_app()->getDoc()) {
            igk_die("/!\\ app document match the global document. That is not allowed");
        }
        $wt = igk_app()->getConfig("website_title", igk_server()->SERVER_NAME);
        $title  = $this->getConfig(IGK_CTRL_CNF_TITLE);
        if (!empty($title))
            $title = __("title.app_2", $title, $wt); // igk_configs()->website_title);
        else {
            $title = __("title.app_1", $wt);
        }
        $d->Title = $title;

        $this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $d);
        $bbox = $d->Body->getBodyBox();
        igk_doc_set_favicon($d, $this->getResourcesDir() . "/Img/favicon.ico");
        $this->setCurrentView($view, true);
        $bbox->add($this->TargetNode);
        if ($render) {
            HtmlRenderer::RenderDocument($d, 0, $this);
        }
    }
    ///<summary></summary>
    ///<param name="c"></param>
    /**
     * 
     * @param mixed $c
     */
    protected function renderError($c)
    {
        igk_dev_wln_e(__FILE__ . "." . __LINE__, "RenderError document");
        $f = igk_io_baseDir("Pages/error_404.html");
        if (file_exists($f)) {
            include($f);
        } else {
            $d = $this->getAppDocument();
            $d->Title = R::ngets("title.app_2", $this->getConfig(IGK_CTRL_CNF_TITLE), $this->App->Configs->website_title);
            $div = $d->Body->add("div");
            $div->add("div", array("class" => "igk-title"))->Content = R::ngets("Title.Error");
            $div->add("div", array("class" => "igk-notify igk-notify-danger"))->Content = "No function $c found";
            $d->renderAJX();
            igk_exit();
        }
    }

    ///<summary> save data schema</summary>
    /**
     *  save data schema
     */
    public function save_data_schemas($exit = 1)
    {
        $this->checkFunc(__FUNCTION__);
        $dom = ControllerExtension::SaveDataSchemas($this);
        if ($exit && $dom) {
            return new WebResponse($dom, 200, ["Content-Type: application/xml"]);
        }
        return $dom;
    }
    ///<summary></summary>
    ///<param name="t" ref="true"></param>
    /**
     * 
     * @param  * $t
     */
    public static function SetAdditionalConfigInfo(&$t)
    {
        $t[IGK_CTRL_CNF_BASEURIPATTERN] = igk_getr(IGK_CTRL_CNF_BASEURIPATTERN);
        $t[IGK_CTRL_CNF_TITLE] = igk_getr(IGK_CTRL_CNF_TITLE);
        $t[IGK_CTRL_CNF_APPNAME] = strtolower(igk_getr(IGK_CTRL_CNF_APPNAME));
        $t[IGK_CTRL_CNF_APPNOTACTIVE] = igk_getr(IGK_CTRL_CNF_APPNOTACTIVE);
        $t[IGK_CTRL_CNF_TABLEPREFIX] = igk_getr(IGK_CTRL_CNF_TABLEPREFIX);
    }
    ///<summary></summary>
    ///<param name="doc"></param>
    /**
     * 
     * @param mixed $doc
     */
    protected function setDefaultFavicon($doc)
    {
        throw new IGKException(__("Not implement : use igk_doc_set_favicon function "));
    }
    ///<summary></summary>
    ///<param name="param"></param>
    /**
     * 
     * @param mixed $param
     */
    public function SetupCtrl($param)
    {
        parent::SetUpCtrl($param);
        $t = strtolower(str_replace(' ', '_', $this->Name));
        if (empty($t))
            throw new IGKException(__("Can't setup controller: {0}", get_class($this)));
        $c = array($t, $t . "_administrator");;
        foreach ($c as $k) {
            Groups::insertIfNotExists(array(IGK_FD_NAME => $k));;
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function storeConfigSettings()
    {
        // + bypass invoke parent class       
        igk_environment()->bypass_method($this, true);
        $cp = call_user_func_array([parent::class, __FUNCTION__], []);
        igk_environment()->bypass_method($this, null);
        if ($cp) {
            $this->_registerAction();
        }
        return $cp;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function sync_from_user_data()
    {
        igk_wln(__FUNCTION__ . " Not implements");
    }
    ///<summary> synchronize the current user data to target server</summary>
    /**
     *  synchronize the current user data to target server
     */
    public function sync_user_data($login = null)
    {
        if (($login == null) && ($this->User != null))
            $login = $this->User->clLogin;
        $c = igk_get_user_bylogin($login);
        $d = igk_create_node("response");
        if ($c == null) {
            $d->addXmlNode("error")->Content = "LoginNotFound";
            $d->addXmlNode("msg")->Content = "login . not present on our database";
            igk_wln($d);
            igk_exit();
        }
        ob_start();
        $this->save_data_schemas(0);
        $s = ob_get_contents();
        ob_end_clean();
        igk_wl($s);
        igk_exit();
    }
}
