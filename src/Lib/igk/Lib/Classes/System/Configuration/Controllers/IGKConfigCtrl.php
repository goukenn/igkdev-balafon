<?php
// author: C.A.D. BONDJE DOUE 

namespace IGK\System\Configuration\Controllers;
use IGK\Controllers\BaseController;
use function igk_resources_gets as __;


///<summary>used to manage config manager</summary>
/**
 *  Configuration Controller
 */
final class IGKConfigCtrl extends BaseController implements IConfigController
{
    const CONNEXION_FRAME = IGK_CONNEXION_FRAME;
    const CFG_USER = IGK_CFG_USER;
    ///<summary>.ctr conig controller</summary>
    /**
     * .ctr conig controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->m_confctrls = array();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
     * 
     * @param mixed $n
     */
    public function __get($n)
    {
        igk_trace();
        igk_wln_e("CallDirect Magic Property  : " . __CLASS__ . " try get {$n} : ");
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t
     */
    private function __init_cache_tools($t)
    {
        $t->addDiv()->addSectionTitle(4)->Content = __("title.cacheTools");
        $frm = $t->addForm();
        $frm["action"] = $this->getUri("updatecacheConfig_ajx");
        $u = $this->getUri("activehtmlCache-ajx");
        $div = $frm->addDiv();
        $div->addLabel("a_html_cache")->setClass("dispib")->Content = __("Html Cache");
        $div->addToggleStateButton("a_html_cache", "on", igk_sys_is_htmlcaching())->setClass("dispib")->setAttribute("onchange", "ns_igk.ajx.get('{$u}&cache='+ns_igk.geti(event.target.checked),null,ns_igk.ajx.fn.no); return false;");
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t
     */
    private function __init_log_tools($t)
    {
        $t->addDiv()->addSectionTitle(4)->Content = __("Logs");
        $frm = $t->addForm()->setId("config-log-form");
        $frm["action"] = $this->getUri("update");
        $bar = $frm->addActionBar();
        $bar->addAJXAButton($this->getUri("viewLogs"))->setClass("igk-btn clsubmit igk-btn-default")->Content = __("View global log");
        $bar->addAJXAButton($this->getUri("clearLogs"))->setClass("igk-btn clsubmit igk-btn-default")->Content = __("Clear Log");
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function __initPageConfig()
    {
        $app = igk_app();

        $this->setEnvParam("conf://initPageConfig", 1);
        $bbox = $app->Doc->body->getBodyBox();
        $bbox->ClearChilds();
        switch ($app->CurrentPageFolder) {
            case IGK_CONFIG_PAGEFOLDER:
                $app->Doc->body["class"] = "-igk-client-page +igk-cnf-body +google-Roboto";
                break;
            default:
                $app->Doc->body["class"] = "+igk-client-page -igk-cnf-body -google-Roboto";
                break;
        }
        $this->setEnvParam("conf://initPageConfig", null);
    }
    ///<summary></summary>
    /**
     * 
     */
    private function __NoIE6supportView()
    {
        igk_wln("no ie 6 supported");
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="name"></param>
    ///<param name="param"></param>
    /**
     * 
     * @param mixed $target
     * @param mixed $name
     * @param mixed $param
     */
    function _checkedItemConfig($target, $name, $param)
    {
        $target->add("label", array("for" => $name))->Content = __("lb." . $param);
        $chb = $target->addInput($name, "checkbox", null);
        $chb["value"] = "1";
        if (igk_app()->Configs->$param)
            $chb["checked"] = "true";
    }
    ///<summary></summary>
    ///<param name="o"></param>
    ///<param name="e"></param>
    /**
     * 
     * @param mixed $o
     * @param mixed $e
     */
    private function _cnfPageFolderChanged($o, $e)
    {
        $app = igk_app();
        $bbox = $app->Doc->body->getBodyBox();
        $bbox->clearChilds();
        // $t= $this->getTargetNode();
        // $t->clearChilds();
        $app->Doc->body["class"] = "+igk-client-page -igk-cnf-body";

        switch ($app->CurrentPageFolder) {
            case "home":
                $defctrl = igk_get_defaultwebpagectrl();
                if ($defctrl != null) {
                    $defctrl->View();
                } else {
                    igk_set_env(IGK_ENV_PAGEFOLDER_CHANGED_KEY, null);
                }
                break;
            case IGK_CONFIG_PAGEFOLDER:
                $this->__initPageConfig();
                break;
        }
    }
    ///<summary> load view configuration file </summary>
    /**
     *  load view configuration file
     */
    private function _loadSystemConfig()
    {
        $file = IGK_CONF_DATA;
        $e = array();
        $fullpath = igk_io_syspath($file);
        $e = $this->getConfigSettings()->configEntries;
        IGK\System\Configuration\ConfigUtils::LoadData($fullpath, $e);
    }
    ///<summary></summary>
    ///<param name="page"></param>
    ///<param name="context" default="null"></param>
    /**
     * 
     * @param mixed $page
     * @param mixed $context the default value is null
     */
    protected function _selectMenu($page, $context = null)
    {
        igk_getctrl(IGK_MENU_CTRL)->selectConfigMenu($page, IGKConfigCtrl::class);
        $this->m_menuName = $page;
    }
    ///<summary>send mail notification</summary>
    /**
     * send mail notification
     */
    private function _send_notification_mail()
    {
        $ctrl = $this;
        $app = igk_app();
        if ($app->Configs->informAccessConnection) {
            $to = $app->Configs->website_adminmail;
            if ($to) {
                $message = igk_createnode("div");
                $message->article($ctrl, "mail.notify.template", (object)array(
                    "clDate" => igk_mysql_datetime_now(),
                    "clDomain" => $app->Configs->website_domain
                ));
                $d = igk_get_document('sys://mail/notification');
                if ($d === igk_app()->getDoc()) {
                    igk_die("notification is equal to global document ");
                }
                $opt = igk_xml_create_render_option();
                $opt->Context = "mail";
                $opt->NoStoreRendering = 1;
                $d->body->ClearChilds()->add($message);
                if (!igk_mail_sendmail($to, "no-reply@" . igk_app()->Configs->website_domain, __("title.mail.adminnotifyconnexion_1", $app->Configs->website_domain), $d->render($opt), null)) {
                    igk_ilog(implode(" - ", [__FILE__ . ":" . __LINE__, "message notification failed"]));
                }
            } else {
                igk_ilog(implode(" - ", [__FILE__ . ":" . __LINE__, "/!\\ Can't send mail notification"]));
            }
        }
    }
    ///<summary></summary>
    ///<param name="node"></param>
    /**
     * 
     * @param mixed $node
     */
    private function _view_ConfigMenuSetting($node)
    {
        $c = $node->addPanelBox();
        igk_html_add_title($c, "title.ConfigurationMenuSetting");
        $c->addHSep();
        $f = $this->getDataDir() . "/config.menu.xml";
        igk_wln_e("config menu :::: " . $f);


        $txt = IO::ReadAllText($f);
        $dummy = igk_createnode("dummy");
        $dummy->Load($txt);
        $b = igk_getv($dummy->getElementsByTagName("configmenu"), 0);
        $v_subdiv = $c->addDiv();
        foreach ($b->Childs as  $v) {
            $v_d = $v_subdiv->addDiv();
            $t = $v_d->addDiv(array("class" => "table table-stripped config-menu-title"));
            $t->Content = $v->TagName;
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function activehtmlCache_ajx()
    {
        if (igk_getr("cache")) {
            igk_sys_enable_html_caching();
        } else {
            igk_sys_disable_html_caching();
        }
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    /**
     * 
     * @param mixed $obj
     * @param mixed $method
     */
    public function addConfigSettingChangedEvent($obj, $method)
    {
        igk_die(__METHOD__ . " Obselete");
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    /**
     * 
     * @param mixed $obj
     * @param mixed $method
     */
    public function addConfigUserChangedEvent($obj, $method)
    {
        igk_die(__METHOD__ . " Obselete");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function back()
    {
        $rf = $this->getParam("referer");
        if ($rf) {
            $this->setParam("referer", null);
            igk_navto($rf);
        } else
            igk_navto_home(null);
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function cc_controllerschema()
    {
        igk_app()->getControllerManager()->cm_controllerschema();
    }
    ///<summary>view current controller hierachi</summary>
    ///<obselete />
    /**
     * view current controller hierachi
     * @deprecated removed 
     */
    public function cc_view_controllerschema_ajx()
    {
        igk_wln_e(__METHOD__, __FILE__ . ":" . __LINE__, "obselete");
    }
    ///<summary></summary>
    ///<param name="user"></param>
    /**
     * 
     * @param mixed $user
     */
    private function check_connect($user)
    {
        $adm = strtolower(igk_app()->Configs->admin_login);
        $adm_pwd = strtolower(igk_app()->Configs->admin_pwd);
        return (($adm == $user->clLogin) && ($adm_pwd == $user->clPwd));
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
     * 
     * @param mixed $ctrl
     */
    public function checkConfigDataChanged($ctrl)
    {
        if (IGKAppConfig::getInstance()->checkConfigDataChanged($ctrl)) {
            $this->onConfigSettingChanged();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function checkForUpdate()
    {
        $r = igk_createnode("response");
        $v = igk_getr("v", '0.0.0.0');
        $buri = IGK_WEB_SITE;
        $uri = $buri . "/api/v2/sysversion";
        $sys = 0;
        $r->addXmlNode("status")->Content = 0;
        $r->addXmlNode("request")->Content = "{$sys}";
        $r->addXmlNode("uri")->Content = $uri;
        $r->renderAJX();
        igk_exit();
        if (!empty($sys) && preg_match("/^([0-9]+)\.([0-9]+)(\.([0-9]+)\.([0-9]+))?$/", $sys)) {
            $p = igk_cmp_version($v, $sys);
            if ($p == 0) {
                $m = $r->add("Message");
                $m->addDiv()->Content = __("msg.youareuptodate");
                $r->add("Status")->Content = 0;
            } else if ($p == -1) {
                $r->add("Status")->Content = 1;
                $d = $r->add("Message")->addDiv();
                $d->setStyle("padding-top:10px; padding-bottom:10px");
                $u = igk_io_fullbaserequesturi() . "/" . $this->getUri('conf_install_update');
                $d->addDiv()->Content = __("msg.uptodaterequired");
                $d->addDiv()->Content = __("app.new.version_1", $sys);
                $d->addInput("btn.update", "submit", __("Update"))->setAttribute("onclick", "javascript: ns_igk.os.update('$u'); return false;");
                $dv = $d->addDiv();
                $dv->setId("dialog");
                $dv->setClass("igk-dialog-temp wait");
                $dv->addDiv()->setClass("title")->Content = __("title.pleasewait");
                $m = $dv->addDiv()->setClass("msg")->addDiv();
                $m->addDiv()->Content = __("msg.updatingpleasewait");
                $m->addDiv()->addLineWaiter();
                $dv = $d->addDiv();
                $dv->setId("dialog");
                $dv->setClass("igk-dialog-temp os-complete");
                $dv->addDiv()->setClass("title")->Content = __("title.OS");
                $m = $dv->addDiv()->setClass("msg")->addDiv();
                $m->addDiv()->Content = __("msg.loadingcomplete");
            } else {
                $m = $r->add("Message");
                $m->addDiv()->Content = __("msg.yourversionishighter");
                $r->addNode("Status")->Content = "0";
            }
        } else {
            $r->add("Message")->Content = "";
            $r->add("Status")->Content = -1890;
        }
        $r["xmlns:igk"] = IGK_SCHEMA_NS;
        $r->RenderXML();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function clearcache()
    {
        if (IGKServer::IsLocal() || igk_is_conf_connected() || !igk_sys_env_production()) {
            IGKControllerManagerObject::ClearCache();
        }
        if (igk_is_ajx_demand()) {
            igk_ajx_toast(__("Clear cache success"), "igk-success");
            igk_exit();
        }
        igk_navto_referer();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function clearLogs()
    {
        if (!igk_is_conf_connected() && !igk_server_is_local()) {
            return false;
        }
        $log = dirname(igk_ilog_file());
        $tab = igk_io_getfiles($log);
        foreach ($tab as $f) {
            @unlink($f);
        }
        if (igk_is_ajx_demand()) {
            igk_ajx_toast(__("Clear caches log"), "igk-success");
            igk_exit();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function Clearsession()
    {
        $this->SelectedConfigCtrl = null;
        igk_getctrl(IGK_SESSION_CTRL)->ClearS();
    }
    ///<summary></summary>
    ///<param name="navigate" default="true"></param>
    /**
     * 
     * @param mixed $navigate the default value is true
     */
    public function ClearSessionAndReconnect($navigate = true)
    {
        if ($this->getIsConnected()) {
            $uri = $this->getReconnectionUri();
            igk_getctrl(IGK_SESSION_CTRL)->ClearS(false);
            igk_navto($uri);
            igk_exit();
        }
        igk_navtocurrent();
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="content"></param>
    /**
     * 
     * @param mixed $content
     */
    private function conf_install_checklib($content)
    {
        return true;
    }
    ///<summary></summary>
    ///<param name="file" default="null"></param>
    ///<param name="outdir" default="null"></param>
    /**
     * 
     * @param mixed $file the default value is null
     * @param mixed $outdir the default value is null
     */
    public function conf_install_platform($file = null, $outdir = null)
    {
        $odir = $outdir == null ? igk_io_basedir() : $outdir;
        $f = $file == null ? igk_getv(igk_getv($_FILES, "clFile"), "tmp_name") : $file;
        $r = false;
        $bckdir = igk_io_applicationdatadir() . "/Backup";

        if (!empty($f)) {
            $i = IO::CreateDir($odir);
            $c = igk_zip_unzip_filecontent($f, "__lib.def");
            if (!empty($c)) {
                if ($this->conf_install_checklib($c)) {
                    $bDomain = IGKSubDomainManager::GetBaseDomain();
                    IO::CreateDir($bckdir);
                    igk_zip_folder($bckdir . "/Lib.zip", igk_io_basedir() . "/Lib", "Lib");
                    igk_zip_unzip($f, $odir);
                    $cf = igk_io_basedir("__lib.def");
                    if (file_exists($cf))
                        unlink($cf);
                    IGKControllerManagerObject::ClearCache();
                    IGKSubDomainManager::StoreBaseDomain($this, $bDomain);
                    $r = true;
                }
            } else {
                igk_debug_wln("lib file definition not found !!!!");
            }
        } else {
            igk_log_write_i("config_install", "/!\ not installed");
        }
        if (($file == null) && ($outdir == null)) {
            igk_getconfigwebpagectrl()->reconnect();
            igk_exit();
        }
        return $r;
    }
    ///<summary></summary>
    ///<param name="ruri" default="null"></param>
    /**
     * 
     * @param mixed $ruri the default value is null
     */
    public function conf_install_update($ruri = null)
    {
        if (!igk_is_conf_connected()) {
            return false;
        }
        $u = IGK_WEB_SITE . "/balafon/download/0";
        $f = igk_get_web_content($u);
        $rep = igk_createnode("response");
        if (!empty($f)) {
            $dir = igk_io_dir(IGK_LIB_DIR . "/tmp");
            igk_io_createdir($dir);
            $fn = tempnam($dir, "cnf");
            $k = $fn . ".zip";
            rename($fn, $k);
            $fn = $k;
            $rep->loadArray(array("uri" => $u, "datalength" => strlen($f), "tmp_name" => $fn));
            igk_io_save_file_as_utf8_wbom($fn, $f, true);
            $c = false;
            if (file_exists($fn)) {
                $c = $this->conf_install_platform($fn, null);
                if (file_exists($fn))
                    unlink($fn);
                $rep->loadArray(array("status" => $c));
            } else {
                $rep->loadArray(array("status" => $c));
            }
        }
        if ($c) {
            igk_getctrl(IGK_SESSION_CTRL)->forceview();
            $rep->addNode("ruri")->Content = $ruri ? $ruri : igk_io_fullbaserequesturi() . "/";
        }
        $rep->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function conf_runCtrlConfig()
    {
        $tctrl = igk_sys_getall_ctrl();
        if ($tctrl) {
            $param = array();
            foreach ($tctrl as  $v) {
                $v->SetupCtrl($param);
            }
        }
        igk_app()->getDoc()->Theme->save();
        igk_notifyctrl()->addMsgr("msg.runCtrlConfigComplete");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function conf_update_setting()
    {

        $app = igk_app();
        $c = new stdClass();
        $c->allow_debugging = igk_getr("cldebugmode", false) == "on" ? 1 : 0;
        $c->allow_article_config = igk_getr("clarticleconfig", false) == "on" ? 1 : 0;
        $c->allow_auto_cache_page = igk_getr("clautocachepage", false) == "on" ? 1 : 0;
        $c->cache_file_time = igk_getr("clcache_file_time", false) == "on" ? 1 : 0;
        $c->cache_loaded_file = igk_getr("clCacheLoadedFile", false) == "on" ? 1 : 0;
        $c->informAccessConnection =  (igk_getr("clinformAccessConnection", false) == 'on') ? 1 : 0; // (igk_getr("clinformAccessConnection", false)=="on");

        foreach ($c as $k => $v) {
            $app->Configs->{$k} = $v;
        }

        igk_save_config();
        igk_notifyctrl()->setNotifyHost(null);
        $this->View();
        igk_notifyctrl()->addMsgr("msg.configOptionsUpdated");
        igk_resetr();
    }
    ///<summary>general config ajx</summary>
    /**
     * general config ajx 
     */
    public function configure_search_ajx()
    {
        $s = igk_getr("clsearch");
        $n = igk_html_node_notagnode();
        if (!empty($s)) {
            $s = "/(" . $s . ")/i";
        } else {
            $s = "/(.)+/i";
        }
        $this->configure_settings_load_data($n, $s);
        $n->renderAJX();
    }
    ///<summary>global configure setting request</summary>
    /**
     * global configure setting request
     */
    public function configure_settings()
    {
        if (!igk_is_conf_connected()) {
            igk_navto($this->getAppUri());
        }
        igk_header_no_cache();
        igk_set_env("sys://designMode/off", 1);
        igk_set_env("sys://defaultpage/off", 1);
        $doc = igk_get_document($this, 0);
        $t = $doc->body->clearChilds()->getBodyBox()->clearChilds()->addDiv();
        $t->addDiv()->Content = __("Configuration view");
        self::ViewInContext($this, "general.config.view", ["t" => $t, "doc" => $doc, "pagell" => "configure_setting"]);
        $doc->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="h"></param>
    ///<param name="rg" default="'/(.)*/'"></param>
    /**
     * 
     * @param mixed $h
     * @param mixed $rg the default value is '/(.)\*\/'
     */
    private function configure_settings_load_data($h, $rg = '/(.)*/')
    {
        $tab = $h->addTable()->setClass("fitw")->setStyle("font-size:0.86em");

        $mod = igk_get_modules();
        foreach ($mod as $c) {
            if (file_exists($fc = igk_get_module($c->name)->getDeclaredDir() . "/.settings.pinc")) {
                include_once($fc);
            }
        }
        $t = igk_get_env(IGK_ENV_GLOBAL_SETTING) + include(IGK_LIB_DIR. "/.setting.global.pinc");
        ksort($t, SORT_NATURAL | SORT_FLAG_CASE);
        // merge with module settings
        // igk_wln_e($t);

        $r = $tab->add("thead")->setClass("igk-fixed-header")->addTr();
        $r->addTH()->Content = __("Name");
        $r->addTH()->Content = __("Type");
        $r->addTH()->Content = __("Status");
        $r->addTH()->setStyle("width:100%")->Content = __("Value");
        $r->addTH()->Content = __("Description");
        $ti = array("admin_pwd" => 1);
        $p = "";
        $gf = igk_app()->Configs;
        if ($t) { 
            $st = $tab->add("tbody");
            foreach ($t as $vk => $vv) {
                $s = $vv;
                if (!preg_match($rg, $vk))
                    continue;
                $cnf = igk_getv($gf, $vk);
                $r = $st->addTr();
                $r->addTd()->Content = $vk;
                $r->addTd()->Content = 'string';
                $r->addTd()->Content = $vv == $cnf;
                $td = $r->addTd();
                $td->Content = $cnf;
                $td->setClass("e");
                $r->addTd()->Content = $cnf->clDesc;
            }
        }
        $uri = $this->getUri("configure_store_ajx");
        $tab->addScript()->Content = <<<EOF
			var q = \$igk(igk.getParentScriptByTagName('table'));

igk.ready(function(){
	var e_=null;
	var u_='{$uri}';

	function _start(){
		if (e_==null){

		}
	}

	q.select('.e').each(function(){
		// console.debug('reg event');
		this.reg_event("dblclick", function(){
			 //console.debug("on dbl click");
			 if (e_!=null){
				 if (e_.t==this)
					 return;
				 //restore to default
				 \$igk(e_.t).setHtml(e_.c);
				 e_ = null;
			 }
			 var s = this.innerHTML;
			 var i = this.parentNode.childNodes[0].innerHTML;
			 e_ = {
				 c:s,
				 t:this
			 };
			 var n = ns_igk.createNode('form');
			 n.o["action"] = u_;
			 n.add('input').setAttributes({
				 'id':'clName',
				 'type':'hidden',
				 'value':i
			 });
			 var v_s = n.add('input').setAttributes({
				 'id':'clValue',
				 'class':'cltext igk-form-control',
				 'value':s
			 }).reg_event("keypress", function(evt){
				// console.debug(evt.keyCode);
				switch (evt.keyCode){
					case 13:
					 evt.preventDefault();
					 var mq=this.value;
					 igk.ajx.post(u_, "clName="+this.form["clName"].value+"&clValue="+this.value, function(xhr){
						 if (this.isReady()){
							 if (!e_)return;
							 \$igk(e_.t).setHtml(mq);

							 if (e_.c!=mq){
								 e_.t.parentNode.childNodes[2].innerHTML='<b>user define</b>';
							 }
							 e_ = null;
						 }
					 });
					 break;
					 case 27:
							//restore
							\$igk(e_.t).setHtml(e_.c);
							e_ = null;
						break;
				 }
			 });

			 \$igk(this).setHtml('').add(n);
			 v_s.o.focus();

		});
		return 1;
	});

});
EOF;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function configure_store_ajx()
    {
        if (!igk_is_conf_connected()) {
            return;
        }
        $n = igk_getr("clName");
        igk_app()->Configs->$n = igk_getr("clValue");
        igk_app()->Configs->saveData();
    }
    ///<summary></summary>
    ///<param name="u" default="null"></param>
    ///<param name="pwd" default="null"></param>
    ///<param name="redirect" default="true"></param>
    /**
     * 
     * @param mixed $u the default value is null
     * @param mixed $pwd the default value is null
     * @param mixed $redirect the default value is true
     */
    public function connectToConfig($u = null, $pwd = null, $redirect = true)
    {
        $adm = null;
        $adm_pwd = null; 
        $is_connected = $this->getIsConnected();
        if (!$is_connected && igk_server()->method("POST") && igk_valid_cref(1)) {
            if (!igk_sys_env_production()) {
                $u = $u == null ? "admin" : "";
            }
            $app = igk_app();
            $not = igk_notifyctrl("connexion:frame");
            $u = ($u == null) ? strtolower(igk_getr("clAdmLogin", $u)) : $u;
            $pwd = ($pwd == null) ? strtolower(md5(igk_getr("clAdmPwd", $pwd))) : md5($pwd);
            if (empty($u) || empty($pwd)) {
                $not->addError(__("err.login.failed")); 
            } else {
                $adm = strtolower($app->Configs->admin_login);
                $adm_pwd = strtolower($app->Configs->admin_pwd);
 
                if (($adm == $u) && ($adm_pwd == $pwd)) {
                    $us = (object)array(
                        "clLogin" => $u,
                        "clPwd" => $pwd,
                        "csrf" => "igk-" . (rand() + time())
                    );
                    $obj_u = igk_sys_create_user($us);
                    $this->setConfigUser($obj_u);
                    $this->_send_notification_mail(); 
                    $is_connected = 1;
                } else {
                    $not->addError(__("err.login.failed"));
                    igk_ilog("failed connection"); 
                }
            }
        } else { 
            if (!$redirect) {
                igk_set_header(500);
                igk_wln_e(__("Mandatory failed"));
            }
        }
        // igk_wln_e("connected ? " . $this->getIsConnected(), "?? = ".$is_connected , $u, $pwd, $adm, $adm_pwd);
        if ($redirect) {
            igk_navto("./");
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function forceview()
    {
        igk_getctrl(IGK_SESSION_CTRL)->forceview();
    }
    ///<summary></summary>

    ///<summary></summary>
    /**
     * 
     */
    public function getCanConfigure()
    {
        return ($this->getIsConnected());
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigEntries()
    {
        return $this->getParam("configentries");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigFrame()
    {
        return $this->getEnvParam("configframe");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigMenuNode()
    {
        static $configMenu;
        if ($configMenu === null) {
            $configMenu = igk_createnode("div");
            $configMenu->setId("igk-cnf-menu");
            $configMenu["class"] = "igk-cnf-menu";
        }
        return $configMenu;
        //return $this->getConfigSettings()->MenuNode;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigNode()
    {
        static $confNode;
        if ($confNode === null) {
            $confNode = igk_createnode("div");
            $confNode->setId("igk-cnf-content")->setClass("igk-cnf-content");
        }
        return $confNode;

        // return $this->getEnvParam("configNode", function(){
        // $t = igk_createnode("div");
        // });
        // igk_wln_e("config node");

        // return $this->getConfigSettings()->ConfigNode;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigPage()
    {
        return "configs";
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return *
     */
    public function &getConfigSettings()
    {
        if (!($s = $this->getParam($key = "configsettings"))) {
            $s = (object)array(
                "SelectedController" => null,
                "configEntries" => null,
                "ConfigView" => null
            );
            $this->setParam($key, $s);
        }
        return $s;
    }
    public function getArticlesDir()
    {
        return IGK_LIB_DIR . "/" . IGK_ARTICLES_FOLDER;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigUser()
    {
        return $this->getParam(self::CFG_USER); // $this->getConfigSettings()->ConfigUser;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigView()
    {
        return $this->getConfigSettings()->ConfigView;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDbConstantFile()
    {
        return igk_sys_db_constant_cache();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsAvailable()
    {
        return ($this->getCurrentPageFolder() == IGK_CONFIG_PAGEFOLDER);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsConfiguring()
    {
        return ($this->getIsConnected()) && (igk_app()->CurrentPageFolder == IGK_CONFIG_MODE);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsConnected()
    {
        return defined('IGK_CONF_CONNECT') || ($this->getConfigUser() !== null);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsVisible()
    {
        return $this->getIsAvailable() && igk_const_defined("IGK_CONFIG_PAGE", 1);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getName()
    {
        return IGK_CONF_CTRL;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getphpinfo()
    {
        $cnf = $this->getConfigNode()->ClearChilds();
        IGKOb::Start();
        phpinfo();
        $b = IGKOb::Content();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getReconnectionUri()
    {
        $uri = igk_io_baseuri();
        $clct = $this->getSelectedConfigCtrl();
        if ($_cu = $this->getConfigUser()) {
            $q = array(
                "u" => $_cu->clLogin,
                "pwd" => $_cu->clPwd,
                "selectedCtrl" => $clct ? $clct->Name : null,
                "selectPage" => $this->getSelectedMenuName(),
                "baseUri" => igk_getv(
                    explode(
                        '?',
                        igk_io_base_request_uri()
                    ),
                    0
                )
            );
            $uri = $this->getUri("startconfig&q=" . base64_encode('?' . http_build_query($q)));
        }
        return $uri;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getRegisterToViewMecanism()
    {
        return true;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getSelectedConfigCtrl()
    {
        return igk_getctrl($this->getConfigSettings()->SelectedController);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getSelectedMenuName()
    {
        return igk_getv($this->getConfigSettings(), "SelectedMenuName");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function gotoindex()
    {
        $u = igk_io_baseuri();
        igk_environment()->navgoto = 1;
        igk_navto($u);
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="callback"></param>
    /**
     * 
     * @param mixed $name
     * @param mixed $callback
     */
    public function init_param_callback($name, $callback)
    {
        $bar = $this->getParam($name);
        if ($bar == null) {
            $bar = $callback($this);
            $this->setParam($name, $bar);
        }
        return $bar;
    }
    ///register config controlleur
    /**
     */
    protected function InitComplete()
    {
        parent::InitComplete();
        IGKOwnViewCtrl::RegViewCtrl($this);
        $this->m_configuration = false;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function initConfigMenu()
    {
        $t = array(
            new IGKMenuItem(
                IGK_HOME_PAGEFOLDER,
                IGK_DEFAULT_VIEW,
                $this->getUri("setpage"),
                -900
            ),
            new IGKMenuItem(
                "PhpInfo",
                "phpinfo",
                $this->getUri("show_phpinfo"),
                -800
            ),
            new IGKMenuItem(
                "ServerInfo",
                "serverinfo",
                $this->getUri("show_serverinfo"),
                -750
            ),

            new IGKMenuItem(
                "GoToIndex",
                null,
                igk_io_baseuri(),
                10800
            ),
        );

        $t[] = new IGKMenuItem("LogOut", null, $this->getUri("logout"), 20000);
        return $t;
    }
    ///<summary></summary>
    /**
     * 
     */
    private function initConnexionNode()
    {

        $bfrm = igk_createnotagnode();
        $igk_framename = IGK_FRAMEWORK;
        $igk_version = IGK_VERSION;
        $doc = $this->getDoc();

        igk_google_addfont($doc, "Roboto");


        if ($bmc = igk_require_module("igk/BMC", null, 0, 0)) {

            $bmc->initdoc($doc);



            $doc->setHeaderColor("#4588fa");
            $root = $bfrm->addDiv()->setClass("disptable fit")->addDiv();
            $root->setclass("disptabc alignm fitw");

            $root->img(IGK_LIB_DIR . "/Data/R/img/login_bg.jpg")->setClass("posfix loc_t")->setStyle("");
            $dv = $root->addDiv();
            $dv["class"] = "igk-adm-login-form";
            $frm = $dv->addBMCShape()->addDiv()->addForm()->setClass("dispb");
            $frm["action"] = $this->getUri("connectToConfig");



            $frm->addObData(function () {
                igk_html_form_init();
            }, null);

            $frm->addDiv()->setClass("igk-adm-logo")->Content =  igk_svg_use("balafon_logo");
            $frm->addDiv()->addNotifyHost("connexion:frame", 0);

            $frm->addBMCTextfield("clAdmLogin", array(
                "text" => __("Login"),
                "tip" => __("Admin login"),
                "type" => "text",
                "attribs" => [
                    "autofocus" => true
                ]

            ), "", null, 1, 1)->addBMCRipple();
            $frm->addBMCTextfield("clAdmPwd", array(
                "text" => __("Password"),
                "tip" => __("Admin password"),
                "type" => "password"
            ), "", null, 1, 1)->addBMCRipple();
            $frm->addInput("goodUri", "hidden", $this->getAppUri());
            $frm->addInput("badUri", "hidden", $this->getAppUri());
            $bar = $frm->addActionBar()->setStyle("margin: auto;");
            $bar->addButton("connect", 1)->setClass("bmc-raise igk-winui-bmc-button")->Content = __("Connect");
            $bar->addABtn(igk_io_baseuri())->setClass("igk-pull-right")->Content = __("Back to {0}", igk_sys_domain_name());
            $root->addDiv()->setAttribute("style", "font-size:0.8em; text-align:center")->addDiv()->Content = "{$igk_framename} - ( " . IGK_PLATEFORM_NAME . " ) - {$igk_version}<br />Configuration";
            $root->addDiv()->setClass("alignc")->addIGKCopyright();


            return $bfrm;
        }
        // else if(!igk_environment()->is("production")){
        //     $d=$bfrm->addObData(function(){
        //         echo "<div >/!\\bmc not found</div>";
        //     });
        //     $d["class"]="posab";
        // }
        $frm = $bfrm->addForm()->AppendAttributes(array("class" => "connexion_frame"));
        $frm->ClearChilds();
        $frm->addObData(
            function () {
                igk_html_form_init();
            },
            null
        );
        $frm["method"] = "POST";
        $frm["action"] = $this->getUri("connectToConfig");
        $c = null;
        $android = 0;

        if (igk_agent_isandroid()) {
            $android = 1;
            igk_css_regclass(".igk-android-login-form", "[bgcl:igk-android-login-form-bg]");
            igk_css_regcolor("igk-android-login-form-bg", "#eee");
            $frm["class"] = null;
            $frm["class"] = "fit alignc alingm ";
            $frm["style"] = "position:relative; padding-bottom:48px;font-size:1.3em;";
            $frm->Box["style"] = "top:0px; bottom:0px; position:relative; overflow-y:auto; height:100%; background-color:#37C4FF;vertical-align:middle;";
            $dv = $frm->pageCenterBox(function ($dv) use ($frm, $igk_version, $igk_framename) {

                $dv->addDiv()->setClass("dispib")->setAttribute("style", "text-align:center; color:#efefef; font-size:3.4em;vertical-align:middle; margin-bottom:32px;padding-top:32px;")->Content = IGK_PLATEFORM_NAME . "<span class=\"igk-smaller alignt\" style=\"font-size:0.4em\">&copy;</span> Configuration";
                $kdiv = $frm->addDiv()->setClass("no-overflow");
                $kdiv["style"] = "height:auto; vertical-align:bottom; display:inline-block;  vertical-align:middle; ";
                $div = $kdiv->addDiv();
                $row = $kdiv->addContainer()->addDiv()->setClass("dispib")->setStyle("max-width:250px")->addRow();
                $row->addCol("igk-col-3-3")->addDiv()->setClass("alignl")->addNotifyHost("connexion:frame");
                $cdiv = $row->addCol("igk-col-3-3")->addDiv();
                $cdiv->addLabel()->setClass("igk-hbox")->Content = __("lb.clLogin");
                $cdiv->addInput("clAdmLogin", "text")
                    ->setAttribute("placeholder", __("Admin login"))
                    ->setAttribute("autofocus", true)
                    ->setClass("-cltext dispb igk-sm-fitw igk-form-control")->setStyle("border:none; border-bottom: 2px solid black; ");

                $cdiv = $row->addCol("igk-col-3-3")->addDiv();
                $cdiv->addLabel()->setClass("igk-hbox")->Content = __("lb.clPwd");
                $cdiv->addInput("clAdmPwd", "password")->setAttribute("placeholder", __("Admin password"))->setAttribute("autocomplete", "current-password")->setClass("-clpassword dispb igk-sm-fitw igk-form-control")->setStyle("border:none; border-bottom: 2px solid black;");
                $cdiv = $row->addCol("igk-col-3-3")->addDiv()->setClass('alignc');
                $cdiv->addDiv()->addInput("btn_connect", "submit", __("btn.connect"))->setClass("-clsubmit fitw igk-btn igk-btn-connect");
                $cdiv = $frm->addContainer()->setClass("igk-smaller");
                $cdiv->addA(igk_io_baseuri())->Content = "goto index";
                $frm->addDiv()->setClass("dispb posfix fitw no-overflow loc_l loc_b")->setAttribute("style", "font-size:0.8em; position:fixed; height:48px;")->addDiv()->Content = "{$igk_framename} - ( " . IGK_PLATEFORM_NAME . " ) - {$igk_version}<br />Configuration";
            });
        } else {
            $lang = function ($n) {
                return __($n);
            };
            $bfrm->addObData(
                function () {
                    $admin_bg = IGKResourceUriResolver::getInstance()->resolve(IGK_LIB_DIR . "/Data/R/img/login_bg.jpg");
                    if ($admin_bg) {
?><img src="<?= $admin_bg ?>" alt="admin background" class="posfix cnf-bg" style="z-index:-101; top: 0px; " /><?php
                                                                                                            }
                                                                                                        },
                                                                                                        null
                                                                                                    );
                                                                                                    $baseuri = igk_io_baseuri();
                                                                                                    $out = <<<EOF
<script type="text/javascript">if (window.ns_igk) window.ns_igk.winui.fn.close_all_frames();</script>
<div id="connectionTag" class="igk-cnf-connexion-div google-Roboto">
<div  style="max-width:300px;  position:relative; color:white;  display:inline-block;" >
<div id="id_layer" style="width:300px; z-index:0;">
<div id="id_board"  style="width:301px; padding-top: 48px; background-repeat:no-repeat; left:0px; top:0px;">
	<div id="notify-z" class="notify-z" ></div>
	<ul style="padding-bottom:1.5em" >
        <li><label class="cllabel alignl" for="clAdmLogin" >{$lang('Login')}</label>
        <input type="text" name="clAdmLogin" id="clAdmLogin" class="cltext" autocomplete="off" placeholder="{$lang('Admin login')}" /><br /></li>
        <li><label class="cllabel alignl" for="clAdmPwd" >{$lang('Password')}</label>
        <input type="password" name="clAdmPwd" id="clAdmPwd" class="clpassword " autocomplete="current-password" placeholder="{$lang('Admin password')}" /><br /></li>
	</ul>
    <div class="igk-row" >
        <div class="igk-col fitw alignc">
            <input type="submit" class="igk-btn clsubmit dispib" name="connect" value="{$lang('Connexion')}" />
            <a href="{$baseuri}" class="dispb alignc" style="font-size: 10pt; padding-top:2em" >{$lang('home page')}</a>
        </div>
	</div>
</div>
<div id="id_content" class="config-desc">
${igk_framename} - ${igk_version}<br />
{$lang('dashboard')}
</div>
<div id="id_foot" style="width:301px; height:31px; position:absolute;background-repeat:no-repeat; left:0px; top:0px;">
</div>
</div>
</div>
	<div id="igk_cpv"></div>
</div>
EOF;
                $dv = $frm->addDiv();
                $g = $dv->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->targetNode;
                $g->load($out);
                $i = $g->getElementById("id_board");
                $c = $g->getElementById("igk_cpv");
                $notz = $g->getElementById("notify-z");
                if ($notz) {
                    $not = igk_notifyctrl("connexion:frame");
                    $notz->addNotifyHost("connexion:frame");
                }
                if (!is_object($i)) {
                    igk_die("/!\ not an object \$i. getElementById failed to retrieve id_board.");
                }
            }
            if (!$android) {
                $d = $bfrm->addDiv()->setClass("mobilescreen dispn");
                $d->addDiv()->addSectionTitle(4)->Content = __("Login Form");
                $dv = $d->addDiv();
                $form = $dv->addForm();
                $form["action"] = $this->getUri("connectToConfig");
                $form["method"] = "POST";
                $form["class"] = "login-form";
                $form->addObData(function () {
                    igk_html_form_init();
                }, null);
                $form->addFields([
                    "clAdmLogin" => ["type" => "text", "label_text" => __("Login"),  "placeholder" => __('Admin login'), "attribs" => []],
                    "clAdmPwd" => ["type" => "password", "label_text" => __("Password"), "placeholder" => __('Admin password'), "attribs" => []]
                ]);
                $acbar = $form->addActionBar();
                $acbar->addSubmit("btn.submit", __("connect"));
                $d->addDiv()->Content = IGK_COPYRIGHT;
            }
            if ($c)
                $c->Content = IGK_COPYRIGHT;

            return $bfrm;
        }
        ///<summary></summary>
        /**
            * 
            */
        protected function initTargetNode()
        {
            $this->setParam(IGK_KEY_CSS_NOCLEAR, 1);
            $node = igk_createnode("div")->setAttribute("class", "igk-cnf-page fit igk-parentscroll igk-powered-viewer overflow-y-a");
            $v_cnf = igk_createnode("div")->AppendAttributes(array("class" => "igk-cnf-frame"));
            $v_cnf->add($this->getConfigMenuNode());
            $v_cnf->add($this->getConfigNode());
            $this->setConfigFrame($v_cnf);
            return $node;
        }
        ///<summary></summary>
        ///<param name="f"></param>
        /**
            * 
            * @param mixed $f
            */
        public function IsFunctionExposed($f)
        {
            $v = false;
            if (method_exists(get_Class($this), $f)) {
                $b = new ReflectionMethod(get_class($this), $f);
                $v = $b->isPublic();
            }
            if ($v && igk_is_conf_connected()) {
                return $v;
            }
            return $v || (strtolower($f) == "connecttoconfig");
        }

        ///<summary></summary>
        ///<param name="redirect" default="true"></param>
        ///<param name="detroysession" default="true"></param>
        /**
            * 
            * @param mixed $redirect the default value is true
            * @param mixed $detroysession the default value is true
            */
        public function logout($redirect = true, $detroysession = true)
        {
            if ($this->getIsConnected()) {
                $this->setConfigUser(null);
                $this->setSelectedConfigCtrl(null);
            }
            if ($detroysession) {
                igk_session_destroy();
            }
            if ($redirect) {
                igk_navtocurrent();
            }
        }
        ///<summary></summary>
        /**
            * 
            */
        protected function onConfigSettingChanged()
        {
            if ($this->m_configSettingChangedEvent != null)
                $this->m_configSettingChangedEvent->Call($this, null);
        }
        ///<summary></summary>
        /**
            * 
            */
        protected function onConfigUserChanged()
        {
            igk_notification_push_event(IGK_CONF_USER_CHANGE_EVENT, $this);
        }
        ///<summary></summary>
        ///<param name="msg"></param>
        /**
            * 
            * @param mixed $msg
            */
        public function onHandleSessionEvent($msg)
        {
            switch ($msg) {
                case IGK_ENV_SETTING_CHANGED:
                    $this->checkConfigDataChanged(null);
                    break;
            }
        }
        ///<summary>preview referer result</summary>
        /**
            * preview referer result
            */
        public function preview_result_ajx()
        {
            $d = igk_createnode();
            if ($uri = igk_server()->HTTP_REFERER) {
                $s = igk_curl_post_uri($uri);
                if ($s) {
                    $t = HtmlReader::Load($s);
                    $head = igk_getv($t->getElementsByTagName("head"), 0);
                    $body = igk_getv($t->getElementsByTagName("body"), 0);
                    $tl = igk_getv($head->getElementsByTagName("title"), 0);
                    $d->addDiv()->setClass("fcl-blue igk-title-5")->Content = $tl ? $tl->innerHTML : "NoTitle";
                    $dv = $d->addDiv();
                    if ($body) {
                        $dv->Content = igk_html_render_text_node($body);
                    } else {
                        $dv->Content = "body is null";
                    }
                } else {
                    $d->content = "failed to send uri: " . $uri;
                }
                igk_ajx_notify_dialog("Page Result Preview", $d);
            }
        }
        ///<summary></summary>
        ///<param name="navigate" default="true"></param>
        /**
            * 
            * @param mixed $navigate the default value is true
            */
        public function reconnect($navigate = true)
        {
            $this->ClearSessionAndReconnect($navigate);
        }
        ///<summary></summary>
        ///<param name="ctrl"></param>
        /**
            * 
            * @param mixed $ctrl
            */
        public function registerConfig($ctrl)
        {
            $c = $this->getParam("m_confctrls", array());
            $c[$ctrl->Name] = $ctrl;
        }
        ///<summary> override register Hook</summary>
        /**
            *  override register Hook
            */
        protected function registerHook()
        {
            igk_reg_hook(IGKEvents::HOOK_PAGEFOLDER_CHANGED, function () {
                $this->_cnfPageFolderChanged($this, null);
            });
        }
        ///<summary></summary>
        ///<param name="uri"></param>
        /**
            * 
            * @param mixed $uri
            */
        public function reloadConfig($uri)
        {
            $tab = igk_getquery_args($uri);
        }
        ///<summary></summary>
        ///<param name="obj"></param>
        ///<param name="method" default="null"></param>
        /**
            * 
            * @param mixed $obj
            * @param mixed $method the default value is null
            */
        public function removeConfigSettingChangedEventt($obj, $method = null)
        {
            igk_die(__METHOD__ . " Obselete");
        }
        ///<summary></summary>
        ///<param name="obj"></param>
        ///<param name="method" default="null"></param>
        /**
            * 
            * @param mixed $obj
            * @param mixed $method the default value is null
            */
        public function removeConfigUserChangedEvent($obj, $method = null)
        {
            igk_die(__METHOD__ . " Obselete");
        }
        ///<summary></summary>
        /**
            * 
            */
        public function resetconfig()
        {
            if (igk_qr_confirm()) {
                @unlink(igk_io_currentrelativepath("Data/configure"));
                igk_getctrl(IGK_MYSQL_DB_CTRL)->initSDb(false);
                $this->reconnect();
            } else {
                $frame = igk_frame_add_confirm($this, "frame_reset_config", $this->getUri("resetconfig"));
                $frame->Form->Div->Content = __("msg.confirmResetConfig");
            }
        }
        ///<summary></summary>
        ///<param name="value"></param>
        /**
            * 
            * @param mixed $value
            */
        public function setConfigFrame($value)
        {
            $this->setEnvParam("configframe", $value);
            return $this;
        }
        ///<summary></summary>
        ///<param name="n"></param>
        /**
            * 
            * @param mixed $n
            */
        // protected function setConfigMenuNode($n){
        // $this->getConfigSettings()->MenuNode=$n;
        // }
        ///<summary></summary>
        ///<param name="v"></param>
        /**
            * 
            * @param mixed $v
            */
        // protected function setConfigNode($v){
        // this->getConfigSettings()->ConfigNode=$v;
        // }
        ///<summary></summary>
        ///<param name="v"></param>
        /**
            * 
            * @param mixed $v
            */
        private function setConfigUser($v)
        {
            if ($this->getConfigUser() !== $v) {
                // $this->getConfigSettings()->ConfigUser=$v;
                $this->setParam(self::CFG_USER, $v);
                $this->onConfigUserChanged();
            }
        }
        ///<summary></summary>
        ///<param name="v"></param>
        /**
            * 
            * @param mixed $v
            */
        public function setConfigView($v)
        {
            $this->getConfigSettings()->ConfigView = $v;
        }
        ///<summary></summary>
        ///<param name="p" default="null"></param>
        ///<param name="stored"></param>
        /**
            * 
            * @param mixed $p the default value is null
            * @param mixed $stored the default value is 0
            */
        public function setpage($p = null, $stored = 0)
        {


            $key = "cnf://no_reload";
            if ($sp = igk_get_env($key)) {
                return;
            }
            if ($stored) {
                igk_set_env($key, 1);
            }
            $_cv = $this->getConfigView();
            if (!empty($p)) {
                if ($_cv != $p) {
                    $this->setParam("cnf://no_recallview", 1);
                }
                $this->setConfigView($p);
            } else {
                $this->setConfigView(IGK_DEFAULT_VIEW);
            }
            $p = $this->getConfigView();
            $app = igk_app();
            $cnf_n = $this->getConfigNode();
            if ($cnf_n === null) {
                $cnf_n = igk_createnode("div");
                $this->ConfigNode = $cnf_n;
            }
            $cnf_n->clearChilds();
            $v_supported = true;
            igk_notify_sethost($cnf_n->addDiv());
            $ctrl = $this;
            switch ($p) {
                case "configurationmenusetting":
                    $this->SelectedConfigCtrl = null;
                    $this->_selectMenu("ConfigurationMenuSetting", "IGKConfigCtrl::setpage");
                    $div = $cnf_n->addDiv();
                    $this->_view_ConfigMenuSetting($div);
                    break;
                case "phpinfo":
                    $this->_selectMenu("phpinfo", "IGKConfigCtrl::setpage");
                    $iframe = $cnf_n->add("iframe", array("class" => "fitw fith no-border"));
                    $iframe["src"] = $this->getUri("getphpinfo");
                    $iframe["style"] = "min-height:800px; ";
                    break;
                case "serverinfo":
                    $this->_selectMenu("serverinfo", "IGKConfigCtrl::setpage");
                    include($this->getViewFile("config.server_info.phtml"));
                    break;
                case IGK_DEFAULT_VIEW:
                    include($this->getViewFile("config.default_page.phtml"));
                    igk_set_env($key, 1);
                    break;
                default:
                    $v_supported = false;
                    break;
            }
            //$cnf_n->clearChilds();//addDiv()->Content = "OK";
        }
        ///set selected menu config
        ///$ctrl = selected config controller
        ///$menuname = menu name
        ///$context = from context. info
        /**
            */
        public function setSelectedConfigCtrl($ctrl, $fromContext = null)
        {
            $_select = $this->getSelectedConfigCtrl();
            if ($_select !== $ctrl) {

                // igk_wln_e("selected config ...", $ctrl );
                $this->getConfigSettings()->SelectedController = $ctrl ? $ctrl->getName() : null;
                if ($ctrl && ($cp = $ctrl->getConfigPage())) {
                    $this->_loadSystemConfig();
                    $this->_selectMenu($cp, IGKConfigCtrl::class);
                }
            }
        }
        ///<summary></summary>
        /**
            * 
            */
        public function show_configuration_menu_setting()
        {
            $this->SelectedConfigCtrl = null;
            $this->setpage("configurationmenusetting", 1);
        }
        ///<summary></summary>
        /**
            * 
            */
        public function show_phpinfo()
        {
            $this->SelectedConfigCtrl = null;
            $this->setpage("phpinfo", 1);
        }
        ///<summary></summary>
        /**
            * 
            */
        public function show_serverinfo()
        {
            $this->SelectedConfigCtrl = null;
            $this->setpage("serverinfo", 1);
        }
        ///<summary></summary>
        /**
            * 
            */
        public function showConfig()
        {
            $this->View();
        }
        ///<summary></summary>
        /**
            * 
            */
        public function startconfig()
        {

            $q = base64_decode(igk_getr("q"));
            $ajx = igk_getr("ajx");
            $tab = igk_getquery_args($q);
            $u = (object)array("clLogin" => $tab["u"], "clPwd" => $tab["pwd"]);
            $v = $this->check_connect($u);
            if ($v) {
                $this->initTargetNode();
                $this->setConfigUser(igk_sys_create_user($u));
                $ctrl = igk_getctrl(igk_getv($tab, "selectedCtrl", IGK_CONF_CTRL));
                $p = igk_getv($tab, "selectPage", IGK_DEFAULT_VIEW);
                if ($ctrl) {
                    $ctrl->showConfig();
                } else {
                    $this->ShowConfig();
                }
                if (igk_getr("navigate", 1)) {
                    if (!$ajx) {
                        $uri = igk_io_baseuri(igk_getv(explode('?', igk_getv($tab, "baseUri", igk_io_baseuri())), 0));
                        igk_navto($uri);
                        igk_exit();
                    } else {
                        if ($ctrl) {
                            $ctrl->TargetNode->renderAJX();
                        }
                    }
                }
            } else {
                igk_notifyctrl()->addErrorr("err.failedtoconnect");
            }
            igk_navtocurrent();
        }
        ///<summary></summary>
        /**
            * 
            */
        public function test_send_mail()
        {
            $this->_send_notification_mail();
        }
        ///<summary></summary>
        /**
            * 
            */
        public function update_adminpwd()
        {
            $d = igk_getr("passadmin");
            if ($d && (strlen($d) >= IGK_MAX_CONFIG_PWD_LENGHT)) {
                igk_app()->Configs->admin_pwd = md5($d);
                igk_save_config();
                igk_resetr();
                igk_notifyctrl(__FUNCTION__)->addSuccessr("msg.pwdupdated");
            } else {
                igk_notifyctrl(__FUNCTION__)->addErrorr("e.adminpwdnotupdated");
            }
            $this->View();
            igk_navtocurrent("/#adminpwd-form");
        }
        ///<summary></summary>
        /**
            * 
            */
        public function update_default_tagname()
        {
            $s = igk_getr("cldefault_node_tagname", "div");
            if (!empty($s))
                igk_app()->Configs->app_default_controller_tag_name = $s;
            igk_save_config();
            igk_resetr();
            $this->View();
            igk_notifyctrl()->setNotifyHost(null);
            igk_notifyctrl()->addMsgr("msg.ConfigOptionsUpdated");
            igk_navtocurrent();
        }
        ///<summary></summary>
        /**
            * 
            */
        public function update_defaultlang()
        {
            $app = igk_app();
            $app->Configs->default_lang = igk_getr("cldefaultLang", "Fr");
            igk_save_config();
            igk_notifyctrl()->addMsgr("msg.update_defaultlang");
            $this->View();
            igk_navtocurrent('?l=' . $app->Configs->default_lang);
        }

        public function getCtrlFile($path)
        {
            if (igk_realpath($path) == $path)
                return $path;
            return igk_io_dir(IGK_LIB_DIR . DIRECTORY_SEPARATOR . $path);
        }
        public function getStylesDir()
        {
            return igk_io_dir(IGK_LIB_DIR . "/Styles");
        }
        ///<summary></summary>
        /**
            * 
            */
        public function update_domain_setting()
        {
            $d = igk_getr("website_domain", IGK_DOMAIN);
            $title = igk_getr("website_title");
            $prefix = igk_getr("website_prefix");
            $app = igk_app();
            if ($d && strlen($d) && igk_is_domain_name($d)) {
                $app->Configs->website_domain = $d;
                IGKSubDomainManager::StoreBaseDomain($this, $d);
            }
            $app->Configs->website_title = $title;
            $app->Configs->website_prefix = $prefix;
            $app->Configs->website_adminmail = igk_getr("website_adminmail", null);
            $app->Configs->company_name = igk_getr("company_name");
            igk_io_save_file_as_utf8_wbom(igk_io_applicationdatadir() . "/domain.conf", $d, true);
            if (igk_save_config()) {
                igk_notifyctrl()->addSuccessr("msg.settingupdate");
            } else {
                igk_notifyctrl()->addError(__("failed to store configuration"));
            }
            $this->View();
            igk_navtocurrent();
        }
        ///<summary>base configuration view</summary>
        /**
            * base configuration view
            */
        public function View()
        {


            if (!$this->getIsVisible() || igk_get_env(IGK_KEY_VIEW_FORCED)) {
                return;
            }
            $data = $this->getEnvParam("CNFDATA", function () {
                $s = "^/Configs(/:lang)?(" . IGK_REG_ACTION_METH . ")?(;(:options))?";
                $uri = igk_io_request_uri();
                $b = igk_sys_ac_create_pattern(null, $uri, $s);
                if ($b->matche($uri)) {
                    return $b->getQueryParams();
                }
                return [];
            });
            if (isset($data["lang"]) && !empty($data["lang"])) {
                igk_ctrl_change_lang($this, $data);
            }
            $this->setEnvParam("cnf_query_options", $data);
            if (($t = $this->getTargetNode()) == null) {
                igk_die("target node for config not initialized");
            }
            if (is_string($t)) {
                igk_die("bad for " . get_class($this));
            }
            $menuctrl = igk_getctrl(IGK_MENU_CTRL);
            $app = igk_app();
            $bbox = $app->Doc->body->getBodyBox();
            $bbox->ClearChilds();


            switch ($app->CurrentPageFolder) {
                case IGK_CONFIG_MODE:
                    $app->Doc->body["class"] = "-igk-client-page +igk-cnf-body +google-Roboto";
                    igk_html_add($t, $bbox);
                    break;
                default:
                    $app->Doc->body["class"] = "+igk-client-page -igk-cnf-body -google-Roboto";
                    return;
            }

            $t->ClearChilds();
            if ($this->getIsAvailable()) {
                if (igk_agent_isie() && igk_agent_ieversion() < 7) {
                    $this->__NoIE6supportView();
                    return;
                }
                if ($f = igk_realpath($this->getStylesDir() . "/config.pcss")) {
                    $app->Doc->Theme->addTempFile($f);
                }


                if (!$this->getIsConnected()) {
                    igk_io_protect_request(igk_io_baseuri() . "/Configs");
                    $cnode = $this->initConnexionNode();
                    $t->addNotifyHost();
                    $t["class"] = "+con-start";
                    igk_html_add($cnode, $t);
                    $this->setEnvParam(self::CONNEXION_FRAME, $cnode);
                } else {
                    $menuctrl->setConfigParentView($this->getConfigMenuNode());
                    $cnode = $this->getEnvParam(self::CONNEXION_FRAME);
                    if ($cnode) {
                        igk_html_rm($cnode, true);
                        $this->setEnvParam(self::CONNEXION_FRAME, null);
                    }
                    $this->setEnvParam(IGK_KEY_CSS_NOCLEAR, 1);
                    $this->_include_view_file("config.layout");
                    $this->setEnvParam(IGK_KEY_CSS_NOCLEAR, 0);
                    $v_cctrl = $this->getSelectedConfigCtrl();

                    if ($v_cctrl === null) {
                        $this->setpage();
                    } else {
                        if (igk_get_env("sys://config/selectedview") !== $v_cctrl) {
                            $tab = $this->getEnvParam("cnf_query_options");
                            $g = igk_pattern_view_extract($v_cctrl, $tab, 1);
                            $v_cctrl->regSystemVars(array_merge(isset($g["c"]) ? [$g["c"]] : [], is_array($v_t = igk_getv($g, "param")) ? $v_t : []), igk_getv($g, "query_options"));
                            $v_cctrl->showConfig();
                        }
                    }
                }
            }
            $this->_onViewComplete();
        }
        ///<summary></summary>
        /**
            * 
            */
        public function viewLogs()
        {
            $log = igk_ilog_file();
            $d = igk_createxmlnode("div");
            $d["class"] = "logview";
            $d["style"] = "max-height:420px; overflow:auto";
            if (file_exists($log)) {
                $tab = explode(IGK_LF, igk_io_read_allfile($log));
                $dv = $d->add("div");
                foreach ($tab as $line) {
                    $dv->li()->Content = $line;
                }
            } else {
                $d->addPanel()->setClass("igk-danger")->Content = __("No log found");
            }

            if (igk_is_ajx_demand()) {
                igk_ajx_panel_dialog(__("logs"), $d);
                igk_exit();
            }
            return $d;
        }
    }
