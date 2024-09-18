<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UsersConfigurationController.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Helper\StringUtility;
use IGK\Helper\SysUtils;
use IGK\System\Models\IModelDefinitionInfo;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\System\Database\QueryBuilder;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CrefNotValidException;
use IGK\System\Html\Dom\IGKHtmlMailDoc;
use IGK\System\Html\HtmlUtils;
use IGK\System\Http\Cookies;
use IGK\System\Http\Request;
use IGK\System\WinUI\Views;
use IGKAppType;
use IGKDbModelUtility;
use IGKEvents;
use IGKException;
use IGKHtmlDoc;
use IGKSysUtil;
use IGKValidator;
use ReflectionException;

use function igk_resources_gets as __;

///<summary>class used to register global user in system</summary>
/**
 * class used to register global user in system
 */
class UsersConfigurationController extends ConfigControllerBase
{
    const view_action = self::class . "::ViewAction";
    const NOTIFY_KEY = 'sys://uc/auf';
    ///<summary></summary>
    /**
     * 
     */
    // public function __user_info(){
    //     igk_init_user_info();
    // }
    public static function ViewAction($a)
    {
        $ctrl = self::ctrl();
        $a->searchbox($ctrl->getUri("search"));
    }
    public function search()
    {
        $this->setParam("search", igk_getr("search"));
        $this->View();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function begin_pwd_reset()
    {
        $doc = new IGKHtmlDoc("reset_pwd");
        $domain = igk_configs()->website_domain;
        $doc->Title = __("title.welcome_1", $domain);
        $mbox = $doc->body->getBodyBox()->setClass("igk-register");
        $mbox->addIGKHeaderBar()->Title = __("title.beginpwdreset");
        $p = $mbox->div()->container()->div();
        $p["style"] = "max-width:380px; margin:auto;";
        $p->div()->setClass("igk-title-5")->Content = __("title.findyouraccount_1", $domain);
        $frm = $p->addForm();
        $frm["action"] = $this->getUri("check_email");
        $div = $frm->div();
        $div->addLabel("clEmail")->Content = "lb.youremail";
        $div->addInput("clEmail", "text")->setClass("igk-form-control")->setAttribute("placeholder", "tip.yourmail");
        $div->div()->addInput("btn_search", "submit", "btn.search")->setClass("igk-btn");
        $p->div();
        $p->div()->Content = igk_configs()->copyright;
        $doc->renderAJX();
        igk_exit();
    }
    ///<summary>General connection method</summary>
    ///<param name="log">display login</param>
    ///<param name="pwd">clear pwd</param>
    /**
     * General connection method
     * @param mixed $log display login
     * @param mixed $pwd clear pwd
     */
    public function connect($log = null, $pwd = null)
    {
        $u = igk_app()->session->User;
        if ($u !== null)
            return false;
        $rm_me = null;
        if ($log == null) {
            if (!igk_server()->ispost()) {
                return false;
            }
            $log = igk_getr("clLogin");
            $pwd = igk_getr("clPwd");
            $rm_me = igk_getr("remember_me");
            unset($_REQUEST["clPwd"]);
            if (empty($log) || empty($pwd)) {
                return false;
            }
        } else {
            $rm_me = 1;
        }
        $condition = [];

        if (!IGKValidator::IsEmail($log)) {
            $condition[] = (object)[
                "operand" => "OR",
                "conditions" => [
                    "clLogin" => $log,
                    "clLogin" => $log . "@" . igk_configs()->website_domain
                ]
            ];
        } else {
            $condition['clLogin'] = $log;
        }
        $crypt_pwd = igk_encrypt($pwd);
        $condition["clPwd"] = $crypt_pwd;
        if ($r = Users::select_row($condition)) {
            if ($r->clStatus == 1) {
                igk_app()->session->lastLogin = $r->clLastLogin;
                //+ | update the last login 
                Users::update(
                    ["clLastLogin" =>
                    QueryBuilder::Expression("CURRENT_TIMESTAMP")],
                    $r->clId
                );
                $t = igk_sys_create_user($r->to_array());
                $this->setGlobalUser($t);
                if ($rm_me) {
                    igk_user_store_tokenid($t);
                }
                return true;
            } else {
                igk_environment()->set("connect_error", "user not active");
                $this->app->Session->ErrorString = "[connectfailed] : status of the requested user is not activated";
            }
            return false;
        }
        $e = Users::query_all([], [
            "Columns" => ["clLogin", "clPwd"]
        ]);
        if ($e) {
            if (!preg_match("/@(.)+$/i", $log)) {
                $log = $log . "@" . igk_configs()->website_domain;
            }
            $tab = ["clLogin" => $log, "clPwd" => $crypt_pwd];
            $t = $e->searchEqual($tab);
            if ($t && is_object($t)) {
                if ($t->clStatus == 1) {
                    $t = igk_sys_create_user($t);
                    $this->setGlobalUser($t);
                    $u = igk_app()->session->User;
                    if ($rm_me) {
                        igk_user_store_tokenid($t);
                    }
                    return true;
                } else {
                    $this->app->Session->ErrorString = "[connectfailed] : status of the requested user is not activated";
                    return false;
                }
            }
        } else {
            igk_notifyctrl("login")->addWarningr("warn.connection.db.failed");
        }
        return false;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function connectpage()
    {
        $u = igk_app()->session->User;
        if ($u != null) {
            igk_navto(igk_io_baseuri());
        }
        $doc = igk_get_document("system/connectionpage");
        $ctrl = igk_get_current_base_ctrl();
        $pa = $ctrl::handleView("connect");
        if ($pa)
            return;
        $doc->Title = __("title.welcome_1", igk_configs()->website_title);
        $mbox = $doc->body->getBodyBox()->setClass("igk-connect");
        $mbox->clearChilds();
        $mbox->addIGKHeaderBar()->Title = __("title.connect");
        $d = $mbox->addConnectForm();
        $d->GoodUri = $this->getAppUri("");
        $d->BadUri = $this->getAppUri("");
        $doc->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * setting config page name
     */
    public function getConfigPage()
    {
        return "users";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDataTableInfo(): ?IModelDefinitionInfo
    {
        return null;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDataTableName(): ?string
    {
        return igk_db_get_table_name(IGK_TB_USERS);
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getDb()
    {
        static $db;
        if ($db === null) {
            if (method_exists($this, "_createDbUtility")) {
                $db = $this->_createDbUtility();
            }
            if (!$db) {
                $db = new IGKDbModelUtility($this);
            }
        }
        return $db;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getName()
    {
        return IGK_USER_CTRL;
    }
    ///<summary></summary>
    ///<param name="u" default="null"></param>
    /**
     * 
     * @param mixed $u the default value is null
     */
    public function getRootUser($u = null)
    {
        if ($u == null)
            $u = $this->app->Session->User;
        if ($u == null)
            return null;
        $s = new IGKDbModelUtility($this);
        if ($s->connect()) {
            while ($u->clParent_Id) {
                $r = $s->selectFirstRow($this->getDataTableName(), array("clId" => $u->clParent_Id));
                if (!$r) {
                    $u = null;
                    break;
                }
                $u = $r;
            }
            $s->close();
        }
        return $u;
    }
    ///<summary></summary>
    ///<param name="u" default="null"></param>
    /**
     * 
     * @param mixed $u the default value is null
     */
    public function getSubUsers($u = null)
    {
        if ($u == null)
            $u = $this->app->Session->User;
        if ($u == null)
            return null;
        $s = new IGKDbModelUtility($this);
        if ($s->connect()) {
            $r = $s->select($this->getDataTableName(), array("clParent_Id" => $u->clId));
            if ($r->RowCount == 0)
                return array();
            return $r->Rows;
        }
        return null;
    }

    protected function initComplete($context = null)
    {
        parent::initComplete();
        if ( ($v_type = igk_environment()->context()) != IGKAppType::balafon){
            
            return;


            igk_reg_hook(IGKEvents::HOOK_DB_TABLECREATED, function ($e) {
                if (($e->args["1"] == Users::table())) {
                    // TODO : wait for init complete to init data
                    $this->setEnvParam('table_created_callback', $fc =  function(){
                        $this->initDataEntry();
                        $fc = $this->getEnvParam('table_created_callback');
                        igk_unreg_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, $fc);
                        igk_unreg_hook(IGKEvents::HOOK_DB_POST_GROUP, $fc);
                        $this->setEnvParam('table_created_callback', null);
                    });
                    igk_reg_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, $fc);
                    igk_reg_hook(IGKEvents::HOOK_DB_POST_GROUP, $fc); 
                }
            });
        }
    }
    ///insert data base
    /**
     * Initialize default users
     */
    protected function initDataEntry()
    {       
        if (igk_environment()->isDev()) {
            Users::InitSystemUsers(); 
        }
    }

    ///<summary></summary>
    ///<param name="func"></param>
    /**
     * @param mixed $func
     */
    protected function IsFunctionExposed($func)
    {
        return true;
    }
    ///<summary></summary>
    /**
     * logout the current user
     */
    public function logout()
    {
        $u = igk_app()->session->User;
        if ($u != null) {
            if (isset($u->clTokenStored))
                igk_user_set_info("TOKENID", null);
            igk_app()->session->setUser(null, $this);
        }
        if (igk_app()->getApplication()->lib("session")) {
            igk_app()->getApplication()->getLibrary()->session->destroy();
        }
        $n = igk_get_cookie_name(igk_sys_domain_name() . "/".Cookies::USER_ID );         
        setcookie($n, "", time() - (3600), igk_get_cookie_path());
        unset($_COOKIE[$n]);
        igk_clear_cookie(Cookies::USER_ID);
        return true;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function logout_lnk()
    {
        $this->logout();
        igk_navto(igk_io_baseuri());
    }
    ///<summary>List user group</summary>
    ///<param name="id" default="null"></param>
    /**
     * List user group
     * @param mixed $id the default value is null
     */
    public function lstgrp($id = null)
    {
        $id = $id ?? igk_getr("id");
        if ($id && ($user = Users::cacheRow($id))) {
            $n = igk_create_node("div")->addPanelBox();
            $frm = $n->addAJXForm();
            $frm->setStyle("min-width: 320px");
            $group = igk_db_user_groups($id);
            if (igk_count($group) > 0) {
                $frm->addObData(function () use ($group, $id) {
                    $dv = igk_create_node("div")->setClass("group-list");
                    $table = $dv
                        ->tablehost()->table()->header(
                            "",
                            __("name"),
                            __("controller"),
                            __("path"),
                            "",
                            ""
                        );
                    foreach ($group as $v) {
                        $gid = $v->clId;
                        $tr = $table->tr();
                        $tr->td()->nbsp();
                        $tr->td()->Content = $v->clName;
                        $tr->td()->Content = $v->clController;
                        $tr->td()->Content = StringUtility::AuthorizationPath($v->clName, $v->clController);
                        $tr->td()->a($this->getUri("rm_grp_from_group&id={$id}&gid=" . $gid))
                            ->google_icon(
                                $v->clStatus == 2? 'enable' : "delete");
                        $tr->td()->nbsp();
                    }
                    $dv->renderAJX();
                });
                igk_ajx_panel_dialog(__("User's group") . " - " . $user->fullname(), $n);
            } else {
                igk_ajx_toast("no group", "igk-danger");
            }
        }
    }
    /**
     * remove user from group
     * @param mixed $userid 
     * @param mixed $groupid 
     * @return void 
     * @throws IGKException 
     */
    function rm_grp_from_group($userid = null, $groupid = null)
    {
        $userid = $userid ?? igk_getr("id");
        $groupid = $groupid ?? igk_getr("gid");
        $r = Usergroups::delete([
            "clGroup_Id" => $groupid,
            "clUser_Id" => $userid
        ]);
        $msg = "";
        $type = "success";
        if (!$r) {
            $type = "danger";
            $msg = __("failed to remove user from group");
        }

        if (igk_is_ajx_demand()) {
            igk_ajx_toast($msg, $type);
            igk_exit();
        }
        igk_notifyctrl("base")->setResponse(["msg" => $msg, "type" => $type]);
        igk_navto_referer();
    }
    ///<summary></summary>
    ///<param name="login"></param>
    ///<param name="pwd"></param>
    ///<param name="firstname"></param>
    ///<param name="lastname"></param>
    ///<param name="parentclass" default="null"></param>
    ///<param name="level" default="1"></param>
    /**
     * 
     * @param mixed $login
     * @param mixed $pwd
     * @param mixed $firstname
     * @param mixed $lastname
     * @param mixed $parentclass the default value is null
     * @param mixed $level the default value is 1
     */
    public function register($login, $pwd, $firstname, $lastname, $parentclass = null, $level = 1)
    {
        $row = Users::createEmptyRow();        
        $row->clLogin = $login;
        $row->clPwd = IGKSysUtil::Encrypt($pwd);
        $row->clFirstName = $firstname;
        $row->clLastName = $lastname;
        $row->clLevel = $level;
        $row->clStatus = 1;
        $row->clClassName = $parentclass;
        $row->clParent_Id = null; 
        $result = Users::insert($row);       
        return $result;
    }
    ///<summary>register or connect </summary>
    public function registerOrConnect($_edata)
    {
        if (igk_is_uri_demand($this->getUri(__FUNCTION__)))
            igk_die("uri request not allowed");

        if (!($user = igk_get_user_bylogin($_edata->email))) {
            $user = $this->register(
                $_edata->email,
                igk_create_guid(),
                igk_getv($_edata, "given_name"),
                igk_getv($_edata, "family_name")
            );
            $user = igk_get_user_bylogin($_edata->email);
            $user->clDisplay = igk_getv($_edata, "name");
            $user->clLocale = igk_getv($_edata, "locale");
            $user->clPicture = igk_getv($_edata, "picture");
            // $user->update();
            if (($ad = igk_get_data_adapter($this->getDataAdapterName())) && $ad->connect()) {
                $ad->update($this->getDataTableName(), $user);
                $ad->close();
            }
        }
        return $user;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function registerHook()
    {
        igk_reg_hook(IGKEvents::HOOK_DB_DATA_ENTRY, function ($hook) {
            $tb = igk_db_get_table_name($this->getDataTableName(), $this);
            if ($hook->args[1] == $tb) {
                $this->initDataEntry($hook->args[0], $tb);
            }
        });
    }
    ///<summary></summary>
    ///<param name="u"></param>
    /**
     * 
     * @param mixed $u
     */
    public function setGlobalUser($u)
    {
        igk_app()->session->setUser($u, $this);
    }
    ///<summary></summary>
    ///<param name="u"></param>
    /**
     * 
     * @param mixed $u
     */
    public function setUser($u)
    {
        if (is_object($u)) {
            $tb = $this->getDataTableName();
            //+ check that the current user exists
            $tu = ["clId" => $u->clId, "clLogin" => $u->clLogin];
            $k = Users::select_row($tu);
            if ($k) {
                $this->setGlobalUser($u);
                return 1;
            }
        }
        return 0;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function signup()
    {
        $doc = igk_get_document("system/signup");
        $doc->Title = __("title.welcome_1", igk_configs()->website_title);
        $mbox = $doc->body->getBodyBox()->setClass("igk-register");
        $mbox->clearChilds();
        $mbox->addIGKHeaderBar()->Title = __("title.registration");
        $d = $mbox->div()->container()->addRow();
        $regfrm = $d->addCol()->setClass("igk-col-4-4")->addRegistrationForm();
        $regfrm->Action = $this->getUri("uc_auf");
        $regfrm->GoodUri = "";
        $regfrm->BadUri = "";
        $regfrm->initView();
        $d = $mbox->div();
        $d->div()->Content = igk_configs()->copyright;
        $doc->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * toggle lock activate
     */
    public function u_block()
    {
        if (!igk_sys_authorize('sys://auth/blockuser')) {
            igk_exit();
        }
        $u = $this->getuser(igk_getr("id"));
        if ($u) {
            // too
            if ($u->clStatus == 1) {
                $u->clStatus = 2;
            } else
                $u->clStatus = 1;
            $u->save();
            igk_notifyctrl(self::NOTIFY_KEY)->addSuccessr("msg.user.inforupdated");
            $this->View();
        }
        igk_wl($this->ConfigNode->getinnerHtml());
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function u_edit()
    {
        if (!igk_sys_authorize('sys://auth/edituser')) {
            igk_exit();
        }
        $id = igk_getr("id");
        igk_getctrl(IGK_MYSQL_DB_CTRL)->db_edit_entry_frame($this, igk_configs()->db_name, $this->getDataTableName(), $id, IGK_FD_ID, true);
        SysUtils::exitOnAJX();
    }
    ///<summary>add user frame</summary>
    /**
     * add user frame - - 
     */
    public function uc_auf()
    {
        $data = Request::getInstance()->getJsonData();
        $model_data = array_keys((array)\IGK\Models\Users::createEmptyRow());
        $model_data[] = 'clRePwd';
        if ($data) {
            $o = igk_get_robj(implode('|', $model_data), 0, (array)$data);
        } else {
            $o = igk_get_robj(implode('|', $model_data));
        }
        $not = igk_notifyctrl(self::NOTIFY_KEY);
        if (igk_qr_confirm()) {           
            if (IGKValidator::IsStringNullOrEmpty($o->clLogin)) {
                $not->addErrorr("e.loginIsNullOrEmpty");
                return;
            }
            if (!igk_user_pwd_required($o->clPwd, $o->clRePwd)) {
                $not->addErrorr("e.passwordnotmatchrequirement");
                return;
            }
            $o->clLogin = strtolower($o->clLogin);
            unset($o->clRePwd);
            unset($o->clAcceptCondition);
            // if($o->clPwd)
            //     $o->clPwd= IGKSysUtil::Encrypt($o->clPwd);
            $i = 0;
            if (Users::Get('clLogin', $o->clLogin)) {
                $not->danger(__('user already register'));
            } else {
                try {
                    $i = Users::Register($o, null);
                } catch (\Exception $ex) {
                    igk_ilog('--- failed to register user ----');
                    igk_ilog($ex->getMessage());
                }
            }
            if ($i) {
                $not->addSuccessr("msg.useradded");                                           
            } else {
                $not->addErrorr("e.registrationnotpossible");
            }
            $nonav = !igk_getr("noNavigation");
            igk_resetr();
            $this->View();
            if (igk_is_ajx_demand()){
                igk_navto($this->getUri('showConfig'));
            }
            if ($nonav)
                igk_navtocurrent();
            return;
        } else {
            $frm = igk_create_node("form");
            $frm["action"] = $this->getUri(__FUNCTION__);
            $frm["autocomplete"] = "off";
            $frm->cref()->ajx(); // ["autocomplete"] = "off";
            $ul = $frm->add("ul");
            igk_html_build_form($ul, array(
                "clFirstName" => array("require" => 0),
                "clLastName" => array("require" => 1),
                "clLogin" => array("require" => 1, "attribs" => array("autocomplete" => "nope")),
                "clPwd" => array(
                    "require" => 1,
                    "type" => "password",
                    "attribs" => array("autocomplete" => "off")
                ),
                "clRePwd" => array(
                    "require" => 1,
                    "type" => "password",
                    "attribs" => array("autocomplete" => "off")
                ),
                "clLevel" => array("require" => 1, "attribs" => array("value" => "0"))
            ));
            $frm->addInput("confirm", "hidden", 1);
            $frm->addInput("conf", "hidden", 1);
            $frm->addHSep();
            $frm->addInput("btn.add", "submit", __("add"));
            $frm->script()->Content = file_get_contents(IGK_LIB_DIR . "/Inc/js/register_user.js");
            igk_ajx_panel_dialog(__("Add user"), $frm);
        }
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="frm"></param>
    /**
     * 
     * @param mixed $frm
     */
    private function uc_options($frm)
    {
        $g = $frm->addActionBar();
        HtmlUtils::AddImgLnk($g, igk_js_post_frame($this->getUri("uc_auf")), "add_16x16")->setClass("igk-btn");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function us_activate()
    {
        /**
         * @var mixed $e
         */

        $rp = igk_getquery_args(base64_decode(igk_getr("q")));
        $email = igk_getv($rp, "u");
        $gooduri = igk_getv($rp, "redirect");
        $date = igk_getv($rp, "d");
        if (is_object($e = $this->getDbEntries())) {
            $t = $e->searchEqual(array("clLogin" => $email));
        }
        if ($t && is_object($t)) {
            $o = 0;
            if ($t->clStatus != 1) {
                $t->clStatus = 1;
                unset($t->clPwd);
                $o = igk_db_update($this, $this->getDataTableName(), $t);
            }
            if ($o && $gooduri) {
                igk_navto($gooduri);
                igk_exit();
            }
            $f = igk_io_basedir() . "/pages/signup_confirmation.php";
            if (file_exists($f)) {
                $f = igk_uri(igk_io_baseuri() . "/" . igk_io_basepath($f));
                igk_hook("user/activate_login", $this);
                igk_navto($f);
            }
        }
        igk_navtocurrent();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function us_lockuser()
    {
        /**
         * @var object $e
         */
        $email = igk_getr("email");
        $e = $this->getDbEntries();
        $t = $e->searchEqual(array("clLogin" => $email));
        if ($t && is_object($t)) {
            $t->clStatus = 2;
            igk_db_update($this, $this->getDataTableName(), $t);
            igk_navtocurrent("userlocked");
        } else {
            igk_navtocurrent();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function us_resetpwd()
    {
        /**
         * @var mixed $e
         */
        $npwd = igk_getr("clNewPwd");
        $e = $this->getDbEntries();

        $t = $e->searchEqual(array("clPwd" => igk_getr("clLogin")));
        if ($t && is_object($t)) {
            $t->clStatus = 1;
            $this->update($t);
            igk_navtocurrent("pwdchanged");
        } else {
            igk_navtocurrent();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function View(): BaseController
    {
        $t = $this->getTargetNode();
        if (!$this->getIsVisible()) {
            igk_html_rm($t);
            return $this;
        }
        $cnf = $this->getConfigNode();
        $cnf->add($t);
        $search = $this->getParam("search");
        $condition = null;
        if ($search) {
            $condition = [(object) [
                "operand" => "OR",
                "conditions" => [
                    "@@clFirstName" => "%" . $search . "%",
                    "@@clLogin" => "%" . $search . "%",
                    "@@clLastName" => "%" . $search . "%"
                ]
            ]];
        }

        $t->clearChilds();
        $t = $t->addPanelBox();
        igk_html_add_title($t, __("System's Users"));
        $t->addPanelBox()->div()->setClass("article-host")->article($this, "users");
        $t->nav()->setClass("actions")->host(self::view_action);
        $frm = $t->addForm();
        $frm->addNotifyHost(self::NOTIFY_KEY); 
        $table = $frm->div()->setClass("igk-table-host overflow-x-a")->addTable();
        $table["class"] = "igk-table igk-table-striped igk-users-list";
        $this->uc_options($frm);
        Views::ModelViewHandleLimit($frm, $table, Users::class, function ($table, $v) {
            static $header;
            if (!$header) {
                $header = true;
                $tr = $table->addTr();
                $tr->add("th")->addSpace();
                $tr->add("th")->Content = __("lb.clFirstName");
                $tr->add("th")->Content = __("lb.clLastName");
                $tr->add("th")->setClass("fitw")->Content = __("lb.clLogin");
                $tr->add("th")->Content = __("lb.clLevel");
                $tr->add("th")->Content = __("lb.clStatus");
                $tr->add("th")->Content = __("lb.clDate");
                $tr->add("th")->Content = __("lb.clClassName");
                $tr->add("th")->addSpace();
                $tr->add("th")->addSpace();
                $tr->add("th")->addSpace();
                $tr->add("th")->addSpace();
            }
            $grpuri = $this->getUri("lstgrp&id=");
            $edit_uri = $this->getUri('u_edit&id=' . $v->clId);
            $lock_uri = $this->getUri('u_block&id=' . $v->clId);
            $tr = $table->addTr();
            $tr->addTd()->addInput(null, "checkbox", $v->clId)
                ->setAttribute('id', null)
                ->setAttribute('name', 'r[]');
            $tr->addTd()->addAJXA($edit_uri)->Content = $v->clFirstName;
            $tr->addTd()->addAJXA($edit_uri)->Content = $v->clLastName;
            $tr->addTd()->Content = $v->clLogin;
            $tr->addTd()->Content = $v->clLevel;
            $tr->addTd()->Content = $v->clStatus;
            $tr->addTd()->Content = $v->clDate;
            $tr->addTd()->Content = $v->clClassName;
            HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($edit_uri), "edit_16x16");
            $tr->addTd()->addAJXA($grpuri . $v->clId)->setClass("igk-svg-btn svg-32")->Content = igk_svg_use("people");
            $tr->addTd()->addAJXA($this->getUri("changePassword&id=" . $v->clId))->setClass("igk-svg-btn svg-32")
                ->setAttribute("title", __("change password"))
                ->Content =
                igk_svg_use("cog-outline");


            HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($lock_uri, '^.igk-cnf-content'), 
                $v->clStatus == 2 ? 'active_16x16': "drop_16x16" );
        }, $condition, null);

        // if (igk_environment()->isDev()){
        //     $frm->ajxa($this->getUri('update_ajx'))->Content = 'update_ajx';       
        // }
        return $this;
    }
     
    // public function update_ajx(){
    //     Users::delete(['clLogin'=>'dummy@igkdev.com']);
    //     $_REQUEST = [
    //         'clLogin'=>'dummy@igkdev.com',
    //         'clFirstName'=>'first',
    //         'clLastName'=>'first - last',
    //         'clPwd'=>'dummy123',
    //         'clRePwd'=>'dummy123',
    //         'confirm'=>1
    //     ];
    //     $this->uc_auf();
    //     $not = igk_notifyctrl(self::NOTIFY_KEY);
    //     $not->addSuccess('udpate_ajx');
    //     igk_navto($this->getUri('showConfig'));
    // }
    public function changeUserPassword($userid, $password, $repassword, &$msg = [])
    {
        if (igk_is_uri_demand($this->getUri(__FUNCTION__))) {
            igk_ilog("call on uri demand");
            return 0;
        }
        $msg = 'password not changed';
        $type = 'danger';
        if ($user = Users::Get(IGK_FD_ID, $userid)) {
            if (ActionHelper::ChangePassword($user, $password, $repassword)) {
                $msg = 'password changed';
                $type = 'success';
            }
        }
        SysUtils::Notify(__($msg), $type);
        return $user;
    }
    /**
     * change user password
     * @param int|null $id 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws CrefNotValidException 
     */
    public function changePassword(int $id = null)
    {

        if (!igk_is_conf_connected()) {
            igk_get_header_status(403);
        }
        $tb = $this->getDataTableName();
        $id = $id ? $id : igk_getr("id");
        if (!$id) {
            igk_die("id not set", 403);
        }
        $rid = Users::Get('clId', $id);
        if (!$rid) {
            igk_die("user not found", 403);
        }

        if (igk_server()->method("POST")) {
            if (!igk_valid_cref(1)) {
                igk_die("not a valid cref", 500);
            }
            $r = (object)igk_getr_k(["pwd", "rpwd"]);
            if (ActionHelper::ChangePassword($rid, $r->pwd, $r->rpwd)) {
                SysUtils::Notify(__('password changed'), 'igk-success');
            }else{
                SysUtils::Notify(__('password not changed'), 'igk-danger');
            }
            igk_ajx_panel_dialog_close();
            SysUtils::exitOnAJX();
        }

        $form = igk_create_node("form");
        $form["action"] = $this->getUri(__FUNCTION__);
        $form["igk-ajx-form"] = 1;
        $form->setStyle("min-width: 360px;");
        igk_html_form_initfield($form);

        $form->div()->setStyle("margin-bottom:2.1em")->Content = igk_user_fullname($rid);
        $form->span()->fields([
            "pwd" => ["type" => "password", "label_text" => __("Password"), 'placeholder' => __('password')],
            "rpwd" => ["type" => "password", "label_text" => __("Re-Password"), 'placeholder' => __('confirm password')],
            "id" => ["type" => "hidden", "value" => $id],
        ]);
        $form->addActions([
            "btn.ok" => ["value" => __("Modify"), "type" => "submit", "attribs" => ["class" => "igk-btn igk-default"]]
        ]);
        igk_ajx_panel_dialog(__("Change user's Password"), $form);
        SysUtils::exitOnAJX();
    }
}
