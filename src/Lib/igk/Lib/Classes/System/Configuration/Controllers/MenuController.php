<?php

namespace IGK\System\Configuration\Controllers;

use IGK\Database\DbColumnInfo;
use IGKException;
use IGK\Helper\IO;
use IGK\Helper\MenuUtils;
use IGK\Helper\SysUtils;
use IGK\Resources\R;
use IGK\System\Html\HtmlUtils;
use IGK\System\WinUI\Menus\MenuHostControl;
use IGK\System\WinUI\Menus\MenuItem;
use IGKEvents;
use IGKServer;
use IGKValidator;

use function igk_resources_gets as __;

///<summary> used to manage global menu and system's configuration menu.</summary>
/**
 *  used to manage global menu and system's configuration menu.
 */
final class MenuController extends ConfigControllerBase
{
    const CONFIG_MENU_FLAG = 0xa02;
    const CONFIG_SELECTED_GROUP = 0xa03;
    const CONFIG_SELECTED_MENU = 0xa04;
    const CONFIG_SELECTED_PAGE = 0xa05;
    const MENU_CHANGE_KEY = "CustomMenuChanged";
    const SYSTEM_MENU_FLAG = 0xa01;
    const USER_MENU_FLAG = 0xa0a;

    /**
     * state changed
     * @var mixed
     */
    private $m_menuChangedState;
    ///<summary></summary>
    ///<param name="storeconfig" default="true"></param>
    /**
     * 
     * @param mixed $storeconfig the default value is true
     */
    function __ClearConfigMenu($storeconfig = true)
    {
        $this->m_customMenu = array();
        $this->storeDBConfigsSettingMenu($storeconfig);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();  
    }
    
    ///<summary></summary>
    ///<param name="div"></param>
    ///<param name="selectedMenu"></param>
    ///<param name="key" default="lb.Controller"></param>
    ///<param name="remove" default="IGK_STR_EMPTY"></param>
    /**
     * 
     * @param mixed $div
     * @param mixed $selectedMenu
     * @param mixed $key the default value is "lb.Controller"
     * @param mixed $remove the default value is IGK_STR_EMPTY
     */
    public function __getEditController($div, $selectedMenu, $key = "lb.Controller", $remove = IGK_STR_EMPTY)
    {
        $tab = igk_sys_get_projects_controllers();
        $li = $div->li();
        $li->addLabel()->Content = __($key);
        $sel = $li->add("select");
        $sel["class"] = "igk-form-control";
        $sel["id"] =
            $sel["name"] = "clController";
        $sel->add("option", array("value" => "none"))->Content = IGK_HTML_SPACE;
        if (count($tab) > 0) {
            foreach ($tab as $v) {
                if (strtolower($remove) == strtolower($v->getName()))
                    continue;
                $opt = $sel->add("option", array("value" => $v->getName()));
                if ($selectedMenu == $v->getName())
                    $opt["selected"] = true;
                $opt->Content = $v->getName();
            }
        }
        return $sel;
    }
    ///<summary></summary>
    ///<param name="host"></param>
    ///<param name="ctrl"></param>
    ///<param name="target"></param>
    ///<param name="tab"></param>
    ///<param name="tname" default="li"></param>
    ///<param name="selected" default="null"></param>
    /**
     * 
     * @param mixed $host
     * @param mixed $ctrl
     * @param mixed $target
     * @param mixed $tab
     * @param mixed $tname the default value is "li"
     * @param mixed $selected the default value is null
     */
    private function __initBuildMenu($host, $ctrl, $target, $tab, $tname = "li", $selected = null)
    {
        $cp = array_merge($tab);
        $keys = array_keys($cp);
        $values = array_values($cp);
        $pile = array();
        $path = "";
        $m = null;
        $ul = null;
        while ((igk_count($cp) > 0) && ($q = (object)array(
            "c" => array_shift($cp),
            "key" => array_shift($keys),
            "target" => $target
        )) || ((igk_count($pile) >= 0) && ($m = array_pop($pile)))) {
            if ($m) {
                $keys = array_keys($m->c);
                $cp = $m->c;
                $target = $m->target;
                $path = $m->key;
                $m = null;
                continue;
            }
            $li = $q->target->add($tname);
            $k = $q->key;
            $v = $q->c;
            if ($selected == $k) {
                $li->setClass("igk-active");
            }
            if ($host->Diseable == $k) {
                $li->setClass("igk-diseable");
            }
            $uri = "#";
            if (is_string($v))
                $uri = $v;
            else if (is_object($v))
                $uri = $v->getUri();
            $a = $li->addA($uri)->setClass("");
            $a->Content = __("Menu." . $k);
            if (is_array($v)) {
                $ul = $li->add($target->TagName);
                array_push($pile, (object)array(
                    "c" => $v,
                    "key" => !empty($path) ? $path . "." . $k : $k,
                    "target" => $ul
                ));
            } else if (is_object($v) && method_exists($v, "getSubmenu")) {
                $t = $v->getSubmenu();
                if ($t && (igk_count($t) > 0)) {
                    array_push($pile, (object)array(
                        "c" => $v,
                        "key" => !empty($path) ? $path . "." . $k : $k,
                        "target" => $ul
                    ));
                }
            }
        }
    }
    ///<summary>load configuration menu</summary>
    /**
     * load configuration menu
     */
    private function __loadConfigurationMenuSetting()
    {
        $tab = array();
        $f = IGK_LIB_DIR . "/" . IGK_DATA_FOLDER . "/config.menu.xml";
        if (file_exists($f)) {
            $d = igk_create_node("div");
            $d->Load(igk_io_read_allfile($f));
            
            $e = igk_getv($d->getElementsByTagName("configmenu"), 0);
            $c = $e ? $e->getChilds() : null;
            if ($c) {
                foreach ($c as  $v) {
                    $s = array();
                    $ch = $v->getChilds();
                    if ($ch) {
                        $ch = $ch->to_array();
                        foreach ($ch as $c => $m) {
                            $s[$m->TagName] = $m->getInnerHtml();
                        }
                        $tab[$v->TagName] = (object)$s;
                    }
                }
            } 
        }
        return (object)$tab;
    }
    ///<summary></summary>
    /**
     * 
     */
    function __saveConfigMenu()
    {
        igk_debug_wln("warning: _saveConfigMenu [" . igk_count($this->m_customMenu) . "]");
        $out = IGK_STR_EMPTY;
        $i = false;
        $line = null;
        foreach ($this->m_customMenu as $v) {
            if ($i)
                $out .= IGK_LF;
            $line = IGK_STR_EMPTY;
            $v_sep = false;
            foreach ($v as $c => $m) {
                $m = trim($m);
                if ($v_sep) {
                    $line .= igk_csv_sep();
                } else {
                    $v_sep = true;
                }
                if (!empty($m))
                    $line .= $m;
            }
            $out .= $line;
            $i = true;
        }
        $v = igk_io_save_file_as_utf8(igk_io_currentrelativepath(IGK_MENU_CONF_DATA), $out, true);
        if ($v)
            igk_sys_regchange(self::MENU_CHANGE_KEY, $this->m_menuChangedState);
        return $v;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $name
     */
    private function _getParentName($name)
    {
        return MenuUtils::GetParentName($name);
    }
    ///<summary></summary>
    ///<param name="menu"></param>
    /**
     * 
     * @param mixed $menu
     */
    private function _getRootMenu($menu)
    {
        if (($menu == null) || is_array($menu))
            return null;
        if ($menu->MenuParent === null)
            return $menu;
        return $this->_getRootMenu($menu->MenuParent);
    }
    ///<summary></summary>
    ///<param name="e" ref="true"></param>
    ///<param name="v_ctab"></param>
    ///<param name="cul"></param>
    ///<param name="bygroup" default="false"></param>
    /**
     * 
     * @param mixed * $e
     * @param mixed $v_ctab
     * @param mixed $cul
     * @param mixed $bygroup the default value is false
     */
    private function _initConfigMenu(&$e, $v_ctab, $cul, $bygroup = false)
    {
        if ($bygroup) {
            $group = array();
            foreach ($v_ctab as $t => $m) {
                $gp = $m->Group;
                if (!empty($gp)) {
                    if (!isset($group[$gp])) {
                        $group[$gp] = array();
                    }
                    $group[$gp][] = (object)array("Menu" => $m, "Node" => IGK_STR_EMPTY);
                } else {
                    igk_trace();
                    igk_wln_e("no group " . $m . " Please Configure the <b>%lib%/Data/config.menu.xml</b> --- ".$gp);
                }
            }
            //igk_wln_e("the text ", $text);
            $div = $cul->addAccordeon();
            foreach ($group as $k => $v) {
                $ul = igk_create_node("div");
                $t = igk_create_node();
                $t["class"] = "igk-row thead";
                $t->div()->setClass("ico " . strtolower($k));
                $t->div()->Content =  __("group." . $k);
                $pan = $div->addPanel($t, $ul, false);
                $pan->setClass("fmenu-h");
                foreach ($v as $t => $m) {
                    $this->_initMenu($ul, $m->Menu, $e);
                    $e[$m->Menu->Name] = $m->Menu;
                }
            }
        } else {
            foreach ($v_ctab as $t => $m) {
                $this->_initMenu($cul, $m, $e);
                $e[$m->name] = $m;
            }
        }
    }
    ///<summary></summary>
    ///<param name="ul"></param>
    ///<param name="menu"></param>
    ///<param name="pages" default="null" ref="true"></param>
    /**
     * 
     * @param mixed $ul
     * @param mixed $menu
     * @param mixed * $pages the default value is null
     */
    private function _initMenu($ul, $menu, &$pages = null)
    {
        MenuUtils::InitMenu($ul, $menu, $pages);
    }
    ///<summary></summary>
    /**
     * 
     */
    private function _initSysConfigMenu()
    {
        igk_set_env("sys://configs/menu", $this->__loadConfigurationMenuSetting());
        //$tab=array();
        $ctab = array();
        $v_ctab = array();
        //$v_Menus=array();
        $v_CPages = array();
        $v_confctrl = igk_getconfigwebpagectrl();

        /// TASK: INIT Configuration Controllers
        $v_load_controller = SysUtils::GetConfigurationControllers();

        $ctab = $v_confctrl->initConfigMenu();

        // igk_wln_e("load controller .... ",  $v_load_controller);

         
        foreach ($v_load_controller as $v) {
            if ($v !== $v_confctrl) {
                if (!($v instanceof ConfigControllerBase) || !$v->getIsConfigPageAvailable())
                    continue;
                $cm = $v->initConfigMenu(); 

                if ($cm !== null) {
                    $v_ctab = array_merge($v_ctab, $cm);
                }
            }
        }
        $c_array = (object)[
            "data"=> & $v_ctab
        ];
        igk_hook(IGKEvents::FILTER_CONFIG_MENU, [$c_array] );
        // igk_wln_e("bind.....", $v_ctab);
        $v_sortByDisplayText = array(MenuItem::class, "SortMenuByDisplayText");
        $v_configTargetNode = igk_create_node("div");
        $v_configTargetNode["class"] = "igk-config-menu-font google-Roboto";
        // $v_configTargetNode["igk-js-autofix"]=1;
        // $v_configTargetNode["igk-autofix-style"]="{'left':'0px', 'top':'10px', 'bottom':'10px', 'width':'200px'}";
        $v_configTargetNode->Index = -9999;
        $v_configTargetNode->clearChilds();
        
        $div = $v_configTargetNode->li()->div();
        $ul = $div->add("ul");
        $this->_initConfigMenu($v_CPages, $ctab, $ul, false);

        //configuration menu tab
        igk_usort($v_ctab, $v_sortByDisplayText);
        $this->_initConfigMenu($v_CPages, $v_ctab, $v_configTargetNode->li()->ul(), true);

        $v_configTargetNode->addBalafonJS()->Content = <<<EOF
ns_igk.readyinvoke('igk.configmenu.init', ns_igk.getParentScript());
EOF;
        $v_configTargetNode->vscrollbar();
        //igk_wln_e( __FILE__.":".__LINE__, $v_configTargetNode);
        return $v_configTargetNode;
    }
    ///<summary></summary>
    /**
     * 
     */
    private function _LoadConfigMenu()
    {
        $this->m_customMenu = array();
        $f = igk_io_syspath(IGK_MENU_CONF_DATA);
        $txt = IO::ReadAllText($f);
        $lines = explode(IGK_LF, $txt);
        foreach ($lines as $l) {
            if (empty($l))
                continue;
            $e = explode(igk_csv_sep(), $l);
            $name = strtoupper(trim($e[0]));
            if (empty($name))
                continue;
            $this->m_customMenu[$name] = array(
                IGK_FD_NAME => $name,
                "clIndex" => trim(igk_getv(
                    $e,
                    1
                )),
                "clController" => trim(igk_getv(
                    $e,
                    2
                )),
                "clMethod" => trim(igk_getv(
                    $e,
                    3
                )),
                "clPage" => trim(igk_getv(
                    $e,
                    4
                )),
                "clGroup" => trim(igk_getv(
                    $e,
                    5
                )),
                "clAvailable" => trim(igk_getv(
                    $e,
                    6
                )),

            );
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    private function _LoadMenu()
    {
        // throw new IGKException(__METHOD__. " Not implement");
    }
    ///<summary></summary>
    ///<param name="table"></param>
    ///<param name="oMenu" default="null"></param>
    /**
     * 
     * @param mixed $table
     * @param mixed $oMenu the default value is null
     */
    private function _m_loadTableHeader($table, $oMenu = null)
    {
        $tr = $table->addTr();
        HtmlUtils::AddToggleAllCheckboxTh($tr);
        $ct = $this->DataTableInfo;
        foreach ($ct as $k) {
            $tr->add("th")->add('a', array(
                "href" => $this->getUri("menu_sortby&n=" . $k->clName . (($oMenu == null) ? null : "&m=" . $oMenu)),
                "onclick" => igk_js_ajx_post_luri('table')
            ))->Content = __($k->clName);
        }
        $tr->add("th", array("style" => "width:8px; "))->Content = IGK_HTML_WHITESPACE;
        $tr->add("th", array("style" => "width:8px; "))->Content = IGK_HTML_WHITESPACE;
    }
    ///<summary></summary>
    ///<param name="target"></param>
    /**
     * 
     * @param mixed $target
     */
    private function _m_otherMenuView($target)
    {
        $this->addTitle($target, __("Custom menu"));
        if (file_exists($a = $this->getArticle("menu.othermenudescription"))){    
            igk_html_article($this, $a, $target->div()->setClass("article-host"));       
        }
        $frm = $target->addForm();
        $li = $frm->add("ul")->li();
        $li->addLabel("lb.Menus", "clMenus");
        igk_html_build_select($li, "clMenus", array());
        $table = $frm->addTable();
        $this->_m_loadTableHeader($table, "d");
        $btndiv = $frm->div();
        igk_html_toggle_class($table);
    }
    ///<summary></summary>
    /**
     * 
     */
    private function _ReLoadMenu()
    {
        $this->_LoadMenu();
        $this->selectConfigMenu($this->m_menu_cselected ? $this->m_menu_cselected->Name : "default");
        $this->View();
    }
    ///<summary></summary>
    ///<param name="newPage" default="null"></param>
    /**
     * 
     * @param mixed $newPage the default value is null
     */
    public function changeDefaultPage($newPage = null)
    {
        $newPage = igk_gettv($newPage, igk_getr("defaultmenupage"));
        if ($newPage) {
            igk_app()->Configs->menu_defaultPage = $newPage;
            igk_save_config();
            igk_notifyctrl()->addMsg("configuration update");
        }
        $this->View();
        igk_navtocurrent();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigCurrentPage()
    {
        return $this->getFlag("m_CurrentPage");
    }
    ///<summary>get config menu node</summary>
    /**
     * get config menu node
     */
    public function getConfigMenu()
    {
        return $this->getFlag(self::CONFIG_MENU_FLAG);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigPage()
    {
        return "menu";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigSelectedGroup()
    {
        return $this->getFlag(self::CONFIG_SELECTED_GROUP);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigSelectedMenu()
    {
        return $this->getFlag(self::CONFIG_SELECTED_MENU);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigSelectedPage()
    {
        return $this->getFlag(self::CONFIG_SELECTED_PAGE);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigTargetNode()
    {
        static $config_target = null;
        if ($config_target === null) {
            $config_target = igk_create_node("div");
        }
        return $config_target;
        // return $this->getFlag("m_configTargetNode");
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getCurrentPage()
    {
        return $this->getFlag("currentPage", igk_app()->getConfigs()->get("menu_defaultPage", IGK_DEFAULT_VIEW));
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getCurrentPageIndex()
    {
        return $this->getFlag("currentPageIndex", 0);
    }


    ///<summary>get data table info</summary>
    /**
     * return data table info
     */
    public function getDataTableInfo()
    {
        return array(
            new DbColumnInfo(array(
                IGK_FD_NAME => IGK_FD_NAME,
                IGK_FD_TYPE => "VARCHAR",
                IGK_FD_TYPELEN => 255,
                "clIsUnique" => true,
                "clIsPrimary" => true
            )),
            new DbColumnInfo(array(IGK_FD_NAME => "clIndex", IGK_FD_TYPE => "Int")),
            new DbColumnInfo(array(
                IGK_FD_NAME => "clController",
                IGK_FD_TYPE => "VARCHAR",
                IGK_FD_TYPELEN => 255
            )),
            new DbColumnInfo(array(
                IGK_FD_NAME => "clMethod",
                IGK_FD_TYPE => "VARCHAR",
                IGK_FD_TYPELEN => 255
            )),
            new DbColumnInfo(array(
                IGK_FD_NAME => "clPage",
                IGK_FD_TYPE => "VARCHAR",
                IGK_FD_TYPELEN => 255
            )),
            new DbColumnInfo(array(
                IGK_FD_NAME => "clAvailable",
                IGK_FD_TYPE => "VARCHAR",
                IGK_FD_TYPELEN => 1,
                "clDefault" => 1
            ))
        );
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDataTableName()
    {
        return '%prefix%globalmenu';
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDefaultEntry()
    {
        return array(
            IGK_FD_NAME => null,
            "clIndex" => 0,
            "clController" => null,
            "clMethod" => null,
            "clPage" => null,
            "clGroup" => null,
            "clAvailable" => null
        );
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getGlobalMenu()
    {
        if (is_array($this->m_Menus)) {
            $t = array();
            $t = array_merge($t, $this->m_Menus);
            return $t;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $name
     */
    public function getMenu($name)
    {
        if ($v = igk_getv($this->getGlobalMenu(), strtoupper($name)))
            return $v;
        return null;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getName()
    {
        return IGK_MENU_CTRL;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getPageList()
    {
        if ($this->m_Pages)
            return array_keys($this->m_Pages);
        return array();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
     * 
     * @param mixed $name
     */
    public function getRootMenu($name)
    {
        return $this->_getRootMenu($name);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getRoots()
    {
        $t = array();
        $h = $this->getGlobalMenu();
        if ($h && is_array($h)) {
            foreach ($h as $k) {
                if ($k->MenuParent == null)
                    $t[] = $k;
            }
        }
        return $t;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getUserMenu()
    {
        return $this->m_customMenu;
    }
    
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="ctrl"></param>
    ///<param name="target"></param>
    ///<param name="tab"></param>
    ///<param name="li" default="li"></param>
    ///<param name="selected" default="null"></param>
    /**
     * 
     * @param mixed $name
     * @param mixed $ctrl
     * @param mixed $target
     * @param mixed $tab
     * @param mixed $li the default value is "li"
     * @param mixed $selected the default value is null
     */
    public function initCustomMenu($name, $ctrl, $target, $tab, $li = "li", $selected = null)
    {
        static $cs_regmenu = null;
        if ($cs_regmenu == null) {
            $cs_regmenu = array();
        }
        $e = igk_getv($cs_regmenu, $name);
        if ($e == null) {
            $e = new MenuHostControl();
            $cs_regmenu[$name] = $e;
        }
        $this->__initBuildMenu($e, $ctrl, $target, $tab, $li, $selected);
        return $e;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected static function initDb($force = false)
    {
        $f = igk_io_syspath(IGK_MENU_CONF_DATA);
        $ctrl = igk_getctrl(__CLASS__);
        if (file_exists($f) == false) {
            $content = <<<EOF
DEFAULT,0,,,default,1
DEFAULT,0,,,contact,2
DEFAULT,0,,,about,3
EOF;
            igk_io_save_file_as_utf8($f, $content, true);
            $ctrl->_ReLoadMenu();
            return 1;
        }
        return 0;
    }
    public function getDataAdapterName()
    {
        return IGK_CSV_DATAADAPTER;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function initTargetNode()
    {
        $ul = igk_create_node("ul");
        return $ul;
    }
    ///<summary></summary>
    ///<param name="navigate" default="true"></param>
    /**
     * 
     * @param mixed $navigate the default value is true
     */
    public function menu_add_menu($navigate = true)
    {
        $this->reg_menu($_REQUEST);
        $this->View();
        if ($navigate) {
            igk_navtocurrent();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_add_menu_frame_ajx()
    {
        $frame = igk_html_frame($this, "theme_menu_add_menu_frame");
        $frame->Title = __("title.Menu");
        $d = $frame->BoxContent;
        $d->clearChilds();
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("menu_add_menu");
        $div = $frm->div();
        $div->li()->addSLabelInput(IGK_FD_NAME, "text", null, null, true);
        $div->li()->addSLabelInput("clIndex", "text", null, array("isnumeric" => true), true);
        $div->li()->addSLabelInput("clPage");
        $this->__getEditController($div, null);
        $div->li()->addSLabelInput("clMethod");
        $div->li()->addSLabelInput("clGroup");
        $li = $div->li();
        $li->addLabel()->Content = __("clAvailable");
        $chb = $li->addInput("clAvailable", "checkbox");
        $chb["checked"] = true;
        $div->addHSep();
        $frm->addBtn("btn_add", __("btn.Add"));
        igk_wl($frame->render());
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_Clearallmenu()
    {
        if (igk_qr_confirm()) {
            $this->m_customMenu = array();
            $this->__saveConfigMenu();
            $this->_ReLoadMenu();
            $this->View();
            igk_navtocurrent();
        } else {
            $frame = igk_frame_add_confirm($this, "menu_Clearallmenu_confirm_frame", $this->getUri("menu_Clearallmenu"));
            $frame->Form->Div->Content = __(IGK_MSG_DELETEMENU_QUESTION);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_drop_selected_menu()
    {
        $this->menu_drop_selected_menu_ajx();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_drop_selected_menu_ajx()
    {
        if (!$this->ConfigCtrl->IsConnected)
            return;
        $m = igk_getr("menu");
        $c = false;
        foreach ($m as  $n) {
            $n = strtoupper($n);
            if (isset($this->m_customMenu[$n])) {
                unset($this->m_customMenu[$n]);
                $c = true;
            }
        }
        if ($c) {
            $this->__saveConfigMenu();
            $this->_ReLoadMenu();
            igk_notifyctrl()->addMsg("menu " . $n . " removed");
        } else {
            igk_notifyctrl()->addError("menu " . $n . " not removed");
        }
        igk_navtocurrent();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_dropmenu()
    {
        $n = igk_getr("n", IGK_STR_EMPTY);
        $n = strtoupper($n);
        if (isset($this->m_customMenu[$n])) {
            unset($this->m_customMenu[$n]);
            $this->__saveConfigMenu();
            $this->_ReLoadMenu();
            igk_notifyctrl()->addMsg("menu " . $n . " removed");
        } else {
            igk_notifyctrl()->addError("menu " . $n . " not removed");
        }
        $this->View();
        igk_navtocurrent();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_dropmenu_ajx()
    {
        if (igk_qr_confirm()) {
            $this->menu_dropmenu();
        } else {
            $frame = igk_frame_add_confirm($this, __FUNCTION__ . "_frame", $this->getUri("menu_dropmenu_ajx"));
            $frame->Form->addInput("n", "hidden", igk_getr("n"));
            $frame->Form->Div->Content = __("msg.deletemenu.question_1", igk_getr("n"));
            $frame->renderAJX();
        }
    }
    ///<summary></summary>
    ///<param name="name" default="null"></param>
    /**
     * 
     * @param mixed $name the default value is null
     */
    public function menu_editmenuframe($name = null)
    {
        $name = ($name == null) ? igk_getr("n") : $name;
        if (!isset($this->m_customMenu[$name]))
            return;
        $v_menu = $this->m_customMenu[$name];
        $frm = igk_getctrl(IGK_FRAME_CTRL)->createFrame("theme_editMenu_frame", $this);
        igk_app()->getDoc()->getBody()->add($frm);
        $frm->Title = __("title.EditMenu", $name);
        $d = $frm->Box;
        $frm->clearChilds();
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("save_menu");
        $div = $frm->div();
        $index = igk_getsv(igk_getv($v_menu, "clIndex"), '0');
        $div->li()->addSLabelInput(IGK_FD_NAME, "text", igk_getv($v_menu, IGK_FD_NAME), null, true);
        $div->li()->addSLabelInput("clIndex", "text", $index, array("isnumeric" => true), true);
        $div->li()->addSLabelInput("clPage", "text", igk_getv($v_menu, "clPage"), null);
        $this->__getEditController($div, igk_getv($v_menu, "clController"));
        $div->li()->addSLabelInput("clMethod", "text", igk_getv($v_menu, "clMethod"));
        $div->li()->addSLabelInput("clGroup", "text", igk_getv($v_menu, "clGroup"));
        $li = $div->li();
        $li->addLabel()->Content = __("clAvailable");
        $chb = $li->addInput("clAvailable", "checkbox");
        if (igk_getv($v_menu, "clAvailable")) {
            $chb["checked"] = true;
        }
        $div->addInput("confirm", "hidden", 1);
        $div->addHSep();
        $frm->addBtn("btn_add", __("btn.save"));
    }
    ///<summary></summary>
    /**
     * 
     */
    public function menu_sortby()
    {
        $r = igk_getr("n");
        $m = igk_getr("m");
        $index = 0;
        if ($m == null) {
            if ($r == $this->m_sortby)
                $this->m_sortby = "r_" . $r;
            else
                $this->m_sortby = $r;
        } else {
            $index = 1;
            if ($r == $this->m_sortby)
                $this->m_osortby = "r_" . $r;
            else
                $this->m_osortby = $r;
        }
        $this->View();
        igk_wl(igk_getv($this->TargetNode->getElementsByTagName("table"), $index)->render());
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t
     */
    public function MenuConfig($t)
    {
        $v_mdiv = $t->div();
        $v_mdiv["class"] = "alignt marg4";
        $frm = $v_mdiv->addForm();
        $this->addTitle($frm, __("Menus"));
        $frm["action"] = $this->getUri("changeDefaultPage");
        $frm["method"] = "post";
        $frm->addLabel("defaultMenuPage");
        $frm->addInput("defaultmenupage", "text", igk_gettv(igk_app()->Configs->menu_defaultPage, IGK_DEFAULT_VIEW));
        $frm->addBr();
        $frm->addInput("btn_d", "submit", __("btn.submit"));
        $c = $v_mdiv->addForm();
        $c["id"] = "config-menu_form";
        $c["action"] = "#" . $c["id"];
        $this->addTitle($c, "title.MenuManager");
        $c->addHSep();
        igk_html_article($this, "menu.description", $c->div());
        $c->addHSep();
        $c->addBr();
        $c->addBr();
        $tab = $c->add("table", array("class" => "fitw"));
        $ct = $this->DataTableInfo;
        $this->_m_loadTableHeader($tab);
        if (is_array($d = $this->m_customMenu)) {
            usort($d, array($this, "sortmenu"));
            foreach ($d as $v) {
                $tr = $tab->addTr();
                $tr->addTd()->addInput("menu[]", "checkbox", $v[IGK_FD_NAME]);
                foreach ($ct as $m) {
                    $oi = $m->clName;
                    switch (strtolower($oi)) {
                        case "clindex":
                            $tr->addTd()->Content = igk_parse_num($v[$oi]);
                            break;
                        default:
                            if (isset($v[$oi])) {
                                if ($oi == IGK_FD_NAME) {
                                    $tr->addTd()->add("a", array("href" => $this->getUri("menu_editmenuframe&n=" . $v[IGK_FD_NAME])))->Content = $v[$oi];
                                } else
                                    $tr->addTd()->Content = $v[$oi];
                            } else
                                $tr->addTd()->Content = IGK_HTML_WHITESPACE;
                            break;
                    }
                }
                HtmlUtils::AddImgLnk($tr->addTd(), $this->getUri("menu_editmenuframe&n=" . $v[IGK_FD_NAME]), "edit_16x16");
                HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("menu_dropmenu_ajx&n=" . $v[IGK_FD_NAME])), "drop_16x16");
            }
        }
        $c->addBr();
        $div = $c->div();
        $a = HtmlUtils::AddImgLnk($div, $this->getUri("menu_drop_selected_menu_ajx"), "drop_16x16");
        $a["onclick"] = "javascript: var q =  \$igk(this).getParentByTagName('form'); q.action = this.href; q.submit();  return false;";
        HtmlUtils::AddBtnLnk($c, "btn.add", igk_js_post_frame($this->getUri("menu_add_menu_frame_ajx")));
        HtmlUtils::AddBtnLnk($c, "btn.rmAll", $this->getUri("menu_Clearallmenu"));
        igk_html_toggle_class($tab);
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function onConfigPageChanged()
    {
        $this->m_configCurrentPageChangedEvent->Call($this, null);
    }
    ///<summary></summary>
    /**
     * 
     */
    private function onPageChanged()
    {
        $this->m_CurrentPageChangedEvent->Call($this, null);
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="saveconfig" default="true"></param>
    /**
     * 
     * @param mixed $t
     * @param mixed $saveconfig the default value is true
     */
    public function reg_menu($t, $saveconfig = true)
    {
        if (is_array($t) == false)
            return;
        extract($t);
        $clIndex = igk_getsv($clIndex, 0);
        $this->val->clearChilds();
        if (IGKValidator::IsStringNullOrEmpty($clName))
            $this->val->add("Name is null or empty");
        if (isset($this->m_customMenu[IGK_FD_NAME])) {
            $this->val->add("Menu already registered");
        }
        if ($this->val->HasChilds) {
            $this->msbox->copyChilds($this->val);
        } else {
            $clName = strtoupper($clName);
            $this->m_customMenu[$clName] = array(
                IGK_FD_NAME => trim($clName),
                "clIndex" => trim($clIndex),
                "clController" => trim($clController) == "none" ? null : trim($clController),
                "clMethod" => trim($clMethod),
                "clPage" => trim($clPage),
                "clGroup" => trim($clGroup),
                "clAvailable" => (isset($clAvailable) ? 1 : 0)
            );
            $this->storeDBConfigsSettingMenu($saveconfig);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function registerHook()
    {
    }
    ///<summary></summary>
    ///<param name="pageName"></param>
    /**
     * 
     * @param mixed $pageName
     */
    public function registerPage($pageName)
    {
        $pages = $this->getParam("pages", array(), 1);
        if (!isset($pages[$pageName])) {
            $pages[$pageName] = array();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function save_menu()
    {
        if (!igk_qr_confirm()) {
            return;
        }
        $this->menu_add_menu(false);
        $this->View();
        igk_frame_close("theme_editMenu_frame", false);
    }
    ///<summary></summary>
    ///<param name="page"></param>
    ///<param name="fromcontext" default="null"></param>
    /**
     * 
     * @param mixed $page
     * @param mixed $fromcontext the default value is null
     */
    public function selectConfigMenu($page, $fromcontext = null)
    {
        $page = strtolower($page);
        $v_page = $this->getConfigSelectedPage();
        if ($v_page != $page) {
            $menu = $this->getConfigSelectedMenu();
            if ($menu)
                $menu["class"] = "-igk-active";
            $menu = null;
            $this->setConfigSelectedPage($page);
            $this->setConfigSelectedMenu($menu);
        }
    }
    ///<summary></summary>
    ///<param name="page"></param>
    ///<param name="index"></param>
    /**
     * 
     * @param mixed $page
     * @param mixed $index the default value is 0
     */
    public function selectGlobalMenu($page, $index = 0)
    {
        $page = strtolower($page);
        if (isset($this->m_Pages[$page])) {
            if ($this->m_menu_selected != null) {
                $this->m_menu_selected["class"] = "-igk-menu_selected";
            }
            $v_rootmenu = null;
            $v_p = $this->m_Pages[$page];
            if (!is_array($v_p))
                $v_rootmenu = $this->_getRootMenu($v_p);
            else {
                if (isset($v_p[$this->m_CurrentPageIndex]))
                    $v_rootmenu = $this->_getRootMenu($v_p[$this->m_CurrentPageIndex]);
            }
            if ($v_rootmenu) {
                $this->m_menu_selected = $v_rootmenu->MenuItem;
                $this->m_menu_selected["class"] = "+igk-menu_selected";
            }
        } else {
            if (IGKServer::IsLocal()) {
                igk_notifyctrl()->addError("[web_global_menu] not define [" . $page . "] - " . igk_io_request_uri());
            }
        }
    }
    ///<summary></summary>
    ///<param name="node"></param>
    /**
     * 
     * @param mixed $node
     */
    public function setConfigParentView($node)
    {
        if ($node) {
            $t = $this->getconfigTargetNode();
            $b = $this->_initSysConfigMenu();
            $t->add($b);
            $node->add($t);
        }
    }
    ///<summary></summary>
    ///<param name="menu"></param>
    /**
     * 
     * @param mixed $menu
     */
    public function setConfigSelectedMenu($menu)
    {
        $this->setFlag(self::CONFIG_SELECTED_MENU, $menu);
    }
    ///<summary></summary>
    ///<param name="page"></param>
    /**
     * 
     * @param mixed $page
     */
    public function setConfigSelectedPage($page)
    {
        $this->setFlag(self::CONFIG_SELECTED_PAGE, $page);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
     * 
     * @param mixed $n
     */
    // public function setconfigTargetNode($n){
    //     $this->setFlag("m_configTargetNode", $n);
    //     return $this;
    // }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setMenuhostCtrl($value)
    {
        $mhostctrl = $this->getParam("menuhostctrl");
        if ($mhostctrl != $value) {
            if (is_object($mhostctrl)) {
                $mhostctrl->unregView($this);
            }
            $mhostctrl = $value;
            if ($mhostctrl != null)
                $mhostctrl->regView($this, "setMenuview");
            $this->setParam("menuhostctrl", $mhostctrl);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function setMenuview()
    {
        if (igk_app()->Configs->menuHostCtrl) {
            $menu_host = igk_getctrl(igk_app()->Configs->menuHostCtrl);
            if ($menu_host != null) {
                $this->setMenuhostCtrl($menu_host);
                $this->setParentView($menu_host->TargetNode);
            }
        }
    }
    ///<summary></summary>
    ///<param name="page"></param>
    ///<param name="index"></param>
    /**
     * 
     * @param mixed $page
     * @param mixed $index
     */
    public function setPage($page, $index)
    {
        if (!$this->ConfigCtrl->IsConfiguring) {
            $this->m_CurrentPage = $page;
            $this->m_CurrentPageIndex = $index;
            $this->onPageChanged();
        } else {
            if (($this->m_configCurrentPage != $page) || ($this->m_configCurrentPageIndex != $index)) {
                $this->m_configCurrentPage = $page;
                $this->m_configCurrentPageIndex = $index;
                $this->onConfigPageChanged();
            }
        }
    }
    ///<summary></summary>
    ///<param name="node"></param>
    /**
     * 
     * @param mixed $node
     */
    public function setParentView($node)
    {
        if ($node) {
            $node->add($this->m_menuTargetNode);
        }
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    /**
     * 
     * @param mixed $a
     * @param mixed $b
     */
    public function sortmenu($a, $b)
    {
        if ($this->m_sortby) {
            $m = $this->m_sortby;
            if (0 === strpos($this->m_sortby, "r_")) {
                switch (strtolower($m)) {
                    case "clindex":
                        $i = igk_getv($a, "clIndex");
                        $j = igk_getv($b, "clIndex");
                        if ($i < $j)
                            return -1;
                        else if ($i > $j)
                            return 1;
                        break;
                    case "clcontroller":
                    default:
                        return strcmp(igk_getv($b, $m), igk_getv($a, $m));
                }
                return strcmp(igk_getv($b, IGK_FD_NAME), igk_getv($a, IGK_FD_NAME));
            } else {
                switch (strtolower($m)) {
                    case "clindex":
                        $i = igk_getv($a, "clIndex");
                        $j = igk_getv($b, "clIndex");
                        if ($i < $j)
                            return -1;
                        else if ($i > $j)
                            return 1;
                        break;
                    case "clcontroller":
                    default:
                        return strcmp(igk_getv($a, $m), igk_getv($b, $m));
                }
            }
        }
        return strcmp(igk_getv($a, IGK_FD_NAME), igk_getv($b, IGK_FD_NAME));
    }
    ///<summary></summary>
    ///<param name="saveconfig" default="true"></param>
    /**
     * 
     * @param mixed $saveconfig the default value is true
     */
    private function storeDBConfigsSettingMenu($saveconfig = true)
    {
        if ($saveconfig) {
            if ($this->__saveConfigMenu()) {
                $this->_ReLoadMenu();
                igk_notifyctrl()->addMsgr("msg.globalmenuupdated");
                igk_debug_wln("notice:save and reloaded");
            } else {
                igk_debug_wln("error:not saved menu");
            }
        }
    }
    ///<summary></summary>
    ///<param name="pageName"></param>
    /**
     * 
     * @param mixed $pageName
     */
    public function unregisterPage($pageName)
    {
        $pages = $this->getParam("pages", array());
        unset($pages[$pageName]);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function View()
    {
        $t = $this->TargetNode;
        if (!$this->getIsVisible()) {
            if (!$this->ConfigCtrl->IsConfiguring) {
                $this->selectGlobalMenu(strtolower($this->m_CurrentPage), $this->m_CurrentPageIndex);
            }
            igk_html_rm($t);
            $t->clearChilds();
        } else {
            if (igk_sys_ischanged(self::MENU_CHANGE_KEY, $this->m_menuChangedState)) {
                $this->_LoadMenu();
            }
            $this->ConfigNode->add($t);
            $t->clearChilds();
            $box = $t->addPanelBox();
            $box->addSectionTitle(4)->Content = __("Menu");
            $this->MenuConfig($box->div());
            //
            // Configure custom menu 
            // 
            $v_mdiv = $box->div();
            $v_mdiv["class"] = "alignt marg4";
            $this->_m_otherMenuView($v_mdiv);
        }
        $this->_onViewComplete();
    }
}
