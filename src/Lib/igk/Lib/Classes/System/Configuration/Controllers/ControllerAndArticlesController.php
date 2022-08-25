<?php
// @file: IGKControllerAndArticlesCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\Controllers\PageControllerBase;
use IGK\Database\DbColumnDataType;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlComponents;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\Html\HtmlUtils;
use IGK\System\Http\JsonResponse;
use IGKControllerTypeManager;
use IGKOb;
use IGKValidator;
use stdClass;
use function igk_resources_gets as __;


final class ControllerAndArticlesController extends ConfigControllerBase
{
    const SL_SELECTCONTROLLER = 1;
    ///<summary></summary>
    public function __construct()
    {
        parent::__construct();
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    private function __updateview($ctrl)
    {
        if ($ctrl && $ctrl->getIsVisible()) {
            if (($this->CurrentPageFolder == IGK_CONFIG_PAGEFOLDER) && igk_reflection_class_extends(get_class($ctrl), IGKConfigCtrlBase::class))
                $ctrl->showConfig();
            else
                $ctrl->View();
        }
    }

    private static function GetSysProject(){
        static $projects;
        if ($projects===null){
            return $projects = igk_sys_get_projects_controllers();
        }
        return $projects;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    private function __viewDefaultPageCtrl($t)
    {
        $frm = $t->addForm();
        $frm->setId("view-default-form");
        $frm["action"] = $this->getUri("setdefaultpage");
        igk_html_add_title($frm, "title.defaultpagectrl");
        $ul = $frm->add('ul');        
        $ctrltab = igk_get_all_uri_page_ctrl();
        if (!$ctrltab || ($ctrltab["total"] == 0)) {
             igk_app()->getConfigs()->default_controller = null;
            $ul->li()->addspan()->Content = __("no controller found");
        } else {
            $sl = $ul->li()->add("select")->setClass("igk-form-control");
            $sl["id"] = $sl["name"] = "clDefaultCtrl";
            $sl["onchange"] = "javascript:window.igk.ajx.post('" . $this->getUri('setdefaultpage_ajx') . "&'+this.id+'='+this.value, null, null);";       
          
            $this->setup_defaultpage($ctrltab);
            $v_kn = strtolower(igk_app()->getConfigs()->default_controller);
            foreach ($ctrltab["@base"] as $k) {
                $opt = $sl->add("option");
                $n = strtolower($k->Name);
                $opt["value"] = $k->Name;
                if ($n == $v_kn) {
                    $opt["selected"] = "true";
                }
                $opt->Content = $k->getDisplayName();
            }
            $tt = $sl->div();
            $tt["class"] = "t";
            if ($templates = igk_getv($ctrltab, "@templates")) {
                foreach ($templates as $k) {
                    $opt = $sl->add("option");
                    $n = strtolower($k);
                    $opt["value"] = $k;
                    if ($n == $v_kn) {
                        $opt["selected"] = "true";
                    }
                    $opt->Content = $k;
                }
            }
        }
        // $frm->div()->add("noscript")->addInput("btn_add", "submit");
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t 
     * @return void 
     * @deprecated disable configuration of menu host
     */
    private function __viewMenuHostCtrl($t)
    {
        // $frm = $t->AddForm();
        // $frm->setId("menuhost-form");
        // $frm["action"] = $this->getUri("ca_setmenuhost");
        // $tab = self::GetSysProject(); 
        // igk_html_add_title($frm, "title.menuController");
        // if (igk_count($tab) == 0) {
        //     $frm->ul()->li()->add("div")->Content = __("msg.nocontroller.for.menu");
        // } else {
        //     igk_html_add_title($frm, "lb.MenuHostCtrl");
        //     $sl = $frm->addUl()->li()->add("select")->setClass("igk-form-control");
        //     $sl->setId("clCtrlMenuHost");
        //     $sl["onchange"] = "javascript:window.igk.ajx.post('" . $this->getUri('ca_setmenuhost_ajx&') . "'+this.id+'='+this.value, null, null);";
        //     $sl->add("option", array("value" => IGK_STR_EMPTY))->Content = IGK_HTML_SPACE;
        //     $v_menuhost = igk_configs()->menuHostCtrl;
        //     foreach ($tab as $v) {
        //         $opt = $sl->add("option", array("value" => $v->getName()));
        //         if ($v->getName() == $v_menuhost) {
        //             $opt["selected"] = "true";
        //         }
        //         $opt->Content = $v->getDisplayName();
        //     }
        //     $frm->div()->add("noscript")->addInput("btn_add", "submit");
        // }
    }
    ///<summary></summary>
    ///<param name="file"></param>
    ///<param name="v_content"></param>
    ///<param name="property" default="null"></param>
    private function __write_article_for_tiny($file, $v_content, $property = null)
    {
        if (empty($file))
            return false;
        $v_dummy = igk_create_node("dummy");
        $v_dummy->Load($v_content);
        if ($v_dummy->HasChilds) {
            if ($property) {
                if ($property->RemoveImgSize) {
                    $tab = $v_dummy->getElementsByTagName("image");
                    foreach ($tab as $k) {
                        $k->setAttributes(["width" => null, "height" => null]);
                    }
                }
                $tab = $v_dummy->getElementsByTagName("*");
                if ($property->RemoveStyles) {
                    foreach ($tab as $k) {
                        $k["style"] = null;
                    }
                }
            }
            $s = null;
            if ($v_dummy->ChildCount === 1) {
                $s = igk_xml_create_render_option();
                $s->Indent = true;
                $s->ParentDepth = $v_dummy->Childs[0];
                if (get_class($s->ParentDepth) === HtmlTextNode::class) {
                    igk_io_save_file_as_utf8($file, $s->ParentDepth->render(null), true);
                } else
                    igk_io_save_file_as_utf8($file, $s->ParentDepth->getinnerHtml($s), true);
            } else {
                $s = igk_xml_create_render_option();
                $s->Indent = true;
                $s->ParentDepth = $v_dummy;
                igk_io_save_file_as_utf8($file, $s->ParentDepth->getinnerHtml($s), true);
            }
            return true;
        }
        igk_io_save_file_as_utf8($file, $v_content, true);
        return true;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="p"></param>
    public function _buildAdditionalInfo($ctrl, $p)
    {
        $d =
            $tab =
            $conf = null;
        if (is_object($ctrl)) {
            $n = igk_sys_ctrl_type($ctrl);
            $d = igk_getv(IGKControllerTypeManager::GetControllerTypes(), $n);
            if (method_exists($ctrl, "GetCustomConfigInfo")) {
                $tab = $ctrl->GetCustomConfigInfo();
            }
        } else if (is_string($ctrl)) {
            $d = igk_getv(IGKControllerTypeManager::GetControllerTypes(), $ctrl);
        }
        if ($d !== null) {
            if ($h = call_user_func(array($d, "GetAdditionalConfigInfo"), array())) {
                $tab = array_merge($h, $tab ?? array());
            }
        }
        if (is_array($tab)) {
            $conf = is_object($ctrl) ? $ctrl->Configs : new stdClass();
            foreach ($tab as $k => $v) {
                if (is_object($v)) {
                    $li = $p->li();
                    $lb = $li->addLabel($k, __("lb." . $k));
                    if (igk_getv($v, "clRequire")) {
                        $lb->setClass("clrequired");
                    }
                    $defaultv = igk_getv($conf, $k, igk_getv($v, "clDefaultValue"));
                    $v_type = strtolower(igk_getv($v, "clType", "text"));
                    switch ($v_type) {
                        case "select":
                            igk_html_build_select($li, $k, $v->clValues, null, $defaultv, null);
                            break;
                        case "file":
                            die(__FILE__ . ":" . __LINE__ . " > handle file not implement");
                            break;
                        case "bool":
                            $chk = $li->addInput($k, "checkbox", "1");
                            if ($defaultv == 1)
                                $chk->activate('checked');
                            break;
                        default:
                            $li->addInput($k, "text", $defaultv);
                            break;
                    }
                } else {
                    $p->li()->addSLabelInput($v, "text", igk_getv($conf, $v));
                }
            }
        }
    }
    ///<summary></summary>
    ///<param name="div"></param>
    ///<param name="ctrl" default="null"></param>
    private function _buildViewArticle($div, $ctrl = null)
    {
        $div->div()->Content = "Not Implement: " . __FUNCTION__;
    }
    ///<summary>build adapter selection list</summary>
    private function _ca_add_adapter($node, $k, $default = null, $nonevalue = false)
    {
        $t = \IGK\DataBase\DataAdapterBase::GetAdapters();
        $node->add("label", array("for" => $k))->Content = __("lb." . $k);
        $sl = $node->addSelect("ctrl-adapter");
        $uri = $this->getUri("lst_adapter_ajx");
        $sl["onchange"] = "javascript: ns_igk.ajx.get('{$uri}&t='+this.value,null, ns_igk.ajx.fn.replace_content( \$igk(this.parentNode).select('.igk-db-ad').getItemAt(0).o)); return false;";
        $sl["id"] =
            $sl["name"] = $k;
        foreach ($t as $m => $c) {
            $opt = $sl->add("option");
            $opt["value"] = $m;
            if ($m == $default)
                $opt["selected"] = "true";
            $opt->Content = $m;
        }
        $node->div()->setClass("igk-db-ad")->Content = "";
    }
    ///<summary></summary>
    ///<param name="li"></param>
    ///<param name="name"></param>
    ///<param name="value" default="null"></param>
    ///<param name="showspace" default="true"></param>
    private function _frm_tablevisiblectrl($li, $name, $value = null, $showspace = true)
    {
        $tab = self::GetSysProject();
        if (count($tab) > 0) {
            $li->addLabel()->Content = __("lb.parentctrl");
            $sel = $li->add("select");
            $sel["id"] =
                $sel["name"] = $name;
            $sel["class"] = "igk-form-control";
            if ($showspace)
                $sel->add("option", array("value" => IGK_STR_EMPTY))->Content = IGK_HTML_SPACE;
            foreach ($tab as $v) {
                $opt = $sel->add("option", array("value" => $v->getName()));
                $opt->Content = $v->DisplayName;
                if ($value && !$value == $v->getName()) {
                    $this->value = $v->getName();
                }
            }
        }
    }
    ///<summary></summary>
    private function _getarticleid()
    {
        return $this->getName() . "_articles";
    }
    ///<summary></summary>
    private function _getviewid()
    {
        $this->getName() . "_views";
    }
    ///<summary></summary>
    ///<param name="col"></param>
    private function _view_ctrl_EditCtrl($col)
    {
        $frm = $col->addColViewBox()->addForm();
        $frm["action"] = $this->getUri("ca_drop_controller");
        igk_html_add_title($frm, "title.controllers");
        $ul = $frm->ul();
        if (igk_count($tab = self::GetSysProject()) > 0) {
            usort($tab, function($a, $b){
                return strcasecmp($a->getDisplayName(), $b->getDisplayName());
            });// SORT_FLAG_CASE| SORT_REGULAR);
            $select = $ul->li()->select(); 
            $target = $this->TargetNode["id"];
            $uri = $this->getUri('select_controller_ajx&n=');
            $select["onchange"] = "javascript: return \$ns_igk.ctrl.ca.editChange(this, '{$target}', '{$uri}');";
            $select["class"] = "igk-form-control";
            $select["name"] =
                $select["id"] = "controller";

            $g = null;
            if (($ts = $this->SelectedController) && !($g = igk_getctrl($this->SelectedController, false))) {
                $this->SelectedController = null;
            }
            if (count($tab) > 0) {
                foreach ($tab as $v) {
                    if ($g === null) {
                        $this->SelectedController = $v->getName();
                        $g = $v;
                    }
                    $opt = $select->add("option", array("value" => $v->getName()));
                    if ($v->getName() == $this->SelectedController)
                        $opt["selected"] = "true";
                    $opt->Content = $v->getDisplayName();
                }
                $dv = $frm->div();
                $this->_view_ctrl_options($g, $dv);
            }
        }
        else {
            $ul->li()->Content = __("no sys controller found.");
        }
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="target"></param>
    protected function _view_ctrl_info($ctrl, $target)
    {
        if ($ctrl == null)
            return;
        $p = $target->div();
        $p["class"] = "igk-cnf-ctrl-info";
        $p->div()->setAttributes(array("class" => "igk-cnf-selected-ctrl"))->span_label(__("Name : "), $ctrl->getName());
        $p->div()->Content = __("Q.ISWEBPAGECTRL_1", ($ctrl instanceof PageControllerBase));
        $p->div()->Content = __("lb.CtrlType") . " : " . igk_sys_ctrl_type($ctrl);
        $p->div()->Content = __("lb.Location_1", $ctrl->getDeclaredDir());
        $p->addBr();
        if (method_exists($ctrl, "getAppUri")) {
            $dv = $p->div();
            $appuri = $ctrl->getAppUri();
            if ($appuri)
                $dv->addABtn($appuri)->Content = __("Visit");
        }
        $table = $p->div()->setClass("fitw")->setStyle("overflow-x:auto")->addTable();
        $v_p = $ctrl->Configs->clParentCtrl;
        $v_parent = $v_p ? igk_getctrl($v_p, false) : null;
        if ($v_parent != null) {
            $t = $table->addTr();
            $t->add("th")->Content = __("Parent");
            $t->add("th")->Content = IGK_HTML_SPACE;
            $tr = $table->addTr();
            $tr->addTd()->li()->add("a", array(
                "href" => "#",
                "onclick" => "javascript:window.igk.fn.config.select_ctrl(this, '" . $this->TargetNode["id"] . "', '" . $this->getUri('select_controller_ajx&n=' . $ctrl->Configs->clParentCtrl) . "'); "
            ))->Content = $v_p;
            HtmlUtils::AddImgLnk($tr->add("td", array("style" => "min-with:16px; min-height:16px;")), $this->getUri("ca_remove_parent&clCtrl=" . $ctrl->getName() . "&clParent=" . $ctrl->Configs->clParentCtrl), "drop_16x16");
        } else {
            $tr = $table->addTr();
            $tr->add("td", array("colspan" => 2))->Content = __("No parent found");
        }
        $p->addBr();
        $table = $p->div()->setClass("fitw")->setStyle("overflow-x:auto")->addTable();
        $table["class"] = "fitw";
        $tr = $table->addTr();
        $tr->add("th")->Content = __("Childs");
        $tr->add("th")->Content = __("Index");
        $tr->add("th", array("style" => "width:16px"))->addSpace();
        if (igk_count($ctrl->Childs) > 0) {
            $tab = $ctrl->Childs;
            usort($tab, "igk_sort_byNodeIndex");
            foreach ($tab as $k) {
                $tr = $table->addTr();
                $tr->addTd()->add("a", array(
                    "href" => IGK_JS_VOID,
                    "onclick" => "javascript:window.igk.fn.config.select_ctrl(this, '" . $this->TargetNode["id"] . "', '" . $this->getUri('select_controller_ajx&n=' . $k->Name) . "'); return false; "
                ))->Content = $k->Name;
                $tr->addTd()->Content = $k->TargetNode->Index;
                HtmlUtils::AddImgLnk($tr->add("td", array("style" => "min-with:16px; min-height:16px;")), $this->getUri("ca_remove_child&clParentCtrl=" . $ctrl->getName() . "&clChild=" . $k->Name), "drop_16x16");
            }
        } else {
            $tr = $table->addTr();
            $tr->add("td", array("colspan" => 3))->Content = __("msg.nochilds");
        }
        $p->addHSep();
        $table = $p->div()->setClass("fitw")->setStyle("overflow-x:auto")->addTable();
        $tr = $table->addTr();
        $tr->add("th")->Content = __("lb.properties");
        $tr->add("th")->Content = __("lb.values");
        foreach ($ctrl->Configs as $k => $v) {
            if ($k == "clParentCtrl")
                continue;
            $tr = $table->addTr();
            $tr->addTd()->addLabel()->Content = __("lb." . $k);
            $tr->addTd()->addLabel()->Content = $v;
        }
        $div = $p->div();
        $this->_view_ctrl_options($ctrl, $div);
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="dv"></param>
    private function _view_ctrl_options(\IGK\Controllers\BaseController $ctrl, $dv)
    {
        $dv["class"] = "+c-opts";
        $bar = $dv->addActionBar();
        HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_add_ctrl_frame_ajx")), "add_16x16")->setClass("igk-btn");
        HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_edit_ctrl_ajx")), "edit_16x16")->setClass("igk-btn");
        HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_edit_ctrl_properties_ajx")), "setting_16x16")->setClass("igk-btn");
        if ($ctrl->CanEditDataTableInfo) {
            HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_edit_db_ajx")), "ico_db_16x16")->setClass("igk-btn");
        }
        if ( $ctrl->getUseDataSchema()) {
            HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_reset_db_ajx")), "db_reset_16x16")->setClass("igk-btn");
        }
        if (class_exists(ZipArchive::class)) {
            $btn = igk_html_installer_button($bar, IGK\System\Installers\IGKBalafonProjectInstaller::class, __("Update Project"), "/update?controller=" . urlencode(get_class($ctrl)));
            $btn->setClass("igk-btn");
        }
        HtmlUtils::AddImgLnk($bar, igk_js_post_frame($this->getUri("ca_ctrl_drop")), "drop_16x16")->setClass("igk-btn");
        $dv->div()->setId("update_target");
    }
    ///<summary></summary>
    ///<param name="t"></param>
    private function _view_default_tab($t)
    {
        $t->addNotifyHost();
        $tv = $t->addRow();
        $this->__viewDefaultPageCtrl($tv->addCol("igk-col-3-3")->div()->setClass("igk-col-view-box"));
        // $this->__viewMenuHostCtrl($tv->addCol("igk-col-4-2 igk-col-sm-3-3")->div()->setClass("igk-col-view-box"));
        $row = $t->addRow();
        $this->_view_ctrl_EditCtrl($row->addCol("igk-col-3-3")->setId("edit_ctrl"));
        $v_dv = $row->addCol("igk-col-3-3")->div()->setClass("cnf-edit-view-result igk-row");
        $this->_viewCtrlEditResult($v_dv);
        if (igk_get_defaultwebpagectrl() == null) {
            $_dv = $row->addCol("igk-col-3-3")->div();
            $_box = $_dv->addActionBar();
            $_box->addAJXA("#")->setAttribute("onclick", igk_js_post_frame($this->getUri("ca_add_ctrl_frame_ajx")) . " return false;")->Content = igk_svg_use("add");
        }
        $this->TargetNode->script()->Content = <<<EOF
window.igk.system.createNS("igk.fn.config", {select_ctrl: function(i, targetid, uri){var q = window.igk.getParentById(i, targetid ); window.igk.ajx.post(uri, null, function(xhr){  if (this.isReady()){ this.setResponseTo(q); var p = q.getElementsByTagName('select')[0]; p.focus(); }})}});
EOF;
    }
    ///<summary></summary>
    ///<param name="v_dv"></param>
    private function _viewCtrlEditResult($v_dv)
    {
        if (!($c = $this->SelectedController))
            return;
        $txb = $v_dv->addCol("igk-col-3-3")->addColViewBox()->addComponent(
            $this,
            HtmlComponents::AJXTabControl,
            "view_result",
            1
        );
        $suri = igk_register_temp_uri(__CLASS__) . "/controller";
        $ctab = ["Info" => (object)array("uri" => $suri . "/infotab", "tab" => "infotab"), "View" => (object)array("uri" => $suri . "/views", "tab" => "views"), "Articles" => (object)array("uri" => $suri . "/articles", "tab" => "articles")];
        !empty($vtab = $this->getParam("tab:editresult")) || ($vtab = "infotab");
        foreach ($ctab as $k => $v) {
            $txb->addTabPage($k, $v->uri, $vtab == $v->tab);
        }
    }
    ///<summary></summary>
    public function add_view()
    {
        $n = igk_getr(IGK_FD_NAME);
        $ctrl = $this->SelectedController;
        $content = trim(igk_getr("clContent"));
        $val = IGKValidator::Init();
        if (IGKValidator::IsStringNullOrEmpty($n)) {
            $val->li()->Content = "Name not defined";
        }
        if (empty($content)) {
            $content .= "<?php\n";
            $content .= "// @file: " . $n . "\n";
            $content .= "// date: " . date("Ymd H:i:s") . "\n";
            $content .= "// author : " . igk_sys_getconfig("script_author", IGK_AUTHOR) . " \n";
            $content .= "// copyright : " . igk_sys_getconfig("script_copyright", IGK_COPYRIGHT) . " \n";
            $content .= "// desc: \n";
            $content .= "\n\$t->clearChilds();\n";
        }
        if (!$val->HasChilds) {
            $a = igk_getctrl($ctrl)->getViewDir();
            if (IO::CreateDir($a)) {
                $file = $a . "/" . $n . "." . IGK_DEFAULT_VIEW_EXT;
                igk_io_save_file_as_utf8($file, igk_html_unscape($content), true);
            }
        } else {
            $this->msbox->copyChilds($this->val);
        }
        $this->View();
        igk_navtocurrent();
    }
    ///<summary></summary>
    public function ca_add_article()
    {
        $ctrl = igk_getr("clCtrl");
        $name = igk_getr(IGK_FD_NAME);
        $content = igk_getr("clContent");
        $lang = igk_getr("clLang", igk_sys_getconfig("default_lang", "fr"));
        $e = igk_create_node("error");
        if (IGKValidator::IsStringNullOrEmpty($name)) {
            $e->li()->Content = "name not defined";
        }
        if (IGKValidator::IsStringNullOrEmpty($lang)) {
            $e->li()->Content = "Language not define";
        }
        if (!$e->HasChilds) {
            $obj_ctrl = igk_getctrl($ctrl);
            if ($obj_ctrl == null) {
                die("not controller found");
            }
            $a = $obj_ctrl->getArticlesDir();
            IO::CreateDir($a);
            $file = igk_io_get_article_file($name, $a, $lang);
            if ($this->__write_article_for_tiny($file, igk_html_unscape($content))) {
                igk_resetr();
                igk_setr("controller", $ctrl);
            } else {
                igk_notifyctrl()->addErrorr("err.filenotsaved");
            }
            igk_set_env("replace_uri", igk_io_request_uri_path());
        } else {
            $this->msbox->copyChilds($e);
        }
        $this->View();
    }
    ///<summary></summary>
    public function ca_add_article_frame()
    {
        $ctrl = (($c = igk_getctrl(igk_getr("ctrlid", null), false)) != null) ? $c : $this->SelectedController;
        if ($ctrl == null) {
            igk_notifyctrl()->addMsg("no controller selected");
            return null;
        }
        $d = igk_create_node("div");
        $d->clearChilds();
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("ca_add_article");
        $frm->addSLabelInput(IGK_FD_NAME);
        $frm->addBr();
        $lg = $this->m_selectedLang ? $this->m_selectedLang : "fr";
        $frm->addLabel()->Content = __("lb.currentlang", $lg);
        $frm->addInput("clLang", "hidden", $lg);
        $frm->addInput("clCtrl", "hidden", $ctrl->getName());
        $frm->addBr();
        $txt = $frm->addTextArea("clContent", null);
        igk_js_enable_tinymce($frm, 'exact', 'clContent');
        $frm->addHSep();
        $frm->addBtn("btn_save", __("btn.save"));
        if (igk_is_ajx_demand()) {
            igk_ajx_panel_dialog(__("Add Article"), $d);
        }
        return $d;
    }
    ///<summary></summary>
    public function ca_add_article_frame_ajx()
    {
        $frame = $this->ca_add_article_frame();
        if ($frame) {
            $frame->renderAJX();
        }
    }
    ///<summary>add controller request</summary>
    public function ca_add_ctrl()
    {
        $this->ca_add_ctrl_frame_ajx();
    }
    ///<summary></summary>
    public function ca_add_ctrl_frame()
    {
        $frameid = __FUNCTION__ . "::Frame";
        if (igk_qr_confirm()) {
            igk_frame_close($frameid);
            igk_ajx_panel_dialog_close();
            $this->ca_addCtrl();
            unset($frame);
            $this->view();
            igk_js_ajx_view_ctrl($this);
        }
        $frame = igk_create_node("div");//igk_html_frame($this, $frameid);
        $frame->div()->Content = "Form DATA";
        // $frame->Title = __("title.AddController");
        // $frame->BoxContent->clearChilds();
        $frm = $frame->form();
        $frm["action"] = $this->getUri("ca_add_ctrl");        
        $js_change_func = <<<EOF
if (ns_igk.ctrl.ca_ctrl_change)
ns_igk.ctrl.ca_ctrl_change('{$this->getUri("ca_get_ctrl_type_info_ajx&n=")}', this);
EOF;

        $frm["onsubmit"] = "javascript: window.igk.ajx.postform(this,'" . $this->getUri("ca_add_ctrl_frame_ajx") . "', ns_igk.ajx.fn.replace_or_append_to_body, false ); this.reset(); return false;";
        $frm->div()->addNotifyHost('controller');
        $frm->addInput('notification', 'hidden', 'controller');
        $ul = $frm->ul()->setClass("add_ctrl_ul")->setStyle("overflow-y:auto; max-height:300px");
  
        $ul->li()->addSLabelInput(IGK_FD_NAME, "text", null, null, true);
        $ul->li()->addSLabelInput("clDisplayName");
        $h = $ul->li()->addSLabelInput("clRegisterName");
        $h->input["tooltip"] = __("tooltip.controller.registername");
        $li = $ul->li();
        $li->add("label", array("for" => "clCtrlType"))->Content = __("lb.ctrlType");
        $ul->li()->addSLabelInput("clOutFolder");
        $t = array_keys(IGKControllerTypeManager::GetControllerTypes());
        sort($t);
        $sel = $li->select("clCtrlType");
        foreach ($t as $k => $v) {
            $opt = $sel->add("option");
            $opt->Content = $v;
            if ($v == "DefaultPage") {
                $opt["selected"] = true;
            }
            $opt["value"] = $v;
        }
        $sel["onchange"] = "javascript:{$js_change_func};";
        $p = $this->getParam("ca:view_frame");
        if ($p == null) {
            $p = $ul->li()->div();
            $p->setId("view_frame");
            $p["class"] = "igk-ctrl-additionnal-properties";
            $this->setParam("ca:view_frame", $p);
        }
        $p->clearChilds();
        $ul->add($p); 
        $this->_ca_add_adapter($ul->li(), "clDataAdapterName", IGK_MYSQL_DATAADAPTER);
        $li = $ul->li();
        $li->addLabel("clDataSchema");
        $sl = $li->addSelect('clDataSchema');
        foreach (['true', 'false'] as $k) {
            $op = $sl->addOption();
            $op["value"] = $k == 'true' ? 1 : 0;
            $op->Content = __("enum." . $k);
            if ($k == "false") {
                $op->setAttribute("selected", true);
            }
        }
        $this->_frm_tablevisiblectrl($ul->li(), "clParentCtrl");
        $ul->li()->addSLabelInput("clTargetNodeIndex");
        $ul->li()->addSLabelInput("clVisiblePages", 'text', "*");
        $ul->li()->addSLabelInput("clDescription");
        $frm->addHSep();
        $frm->addInput("confirm", "hidden", "1");
        $frm->addBtn("btn_add", __("btn.Add"));
        if (!igk_is_ajx_demand()) {
            $u = igk_io_baseuri(IGK_BALAFON_JS_CORE_FILE);
            $frm->script()->setAttribute('src', $u);
        }
        $frm->addBalafonJS()->Content =  <<<EOF
ns_igk.ready(
function(){
var r = \$igk(\$ns_igk.getParentScriptForm());
if (!r)
    return;

 (function(q){ if (!q)return;
 var p = q.select("#liPageName").getItemAt(0);
 var c = q.select("#clWebPage").getItemAt(0);
 if (c && p)
	c.reg_event("change",function(){ if (this.checked) p.css('display:block;'); else p.css('display:none'); });
})(r);
	var q = (r).select("#clCtrlType").first();
	if (q && ns_igk.ctrl.ca_ctrl_change)
		ns_igk.ctrl.ca_ctrl_change('{$this->getUri("ca_get_ctrl_type_info_ajx&n=")}', q.o);
});
EOF;


        return $frame;
    }
    ///<summary>view add controller frame</summary>
    public function ca_add_ctrl_frame_ajx($renderframe = true)
    {
        $frame = $this->ca_add_ctrl_frame();
        if ($renderframe) {
            igk_ajx_panel_dialog(__("Add new Controller"), $frame);
        }
    }
    ///<summary>build a add view frame</summary>
    public function ca_add_view_frame()
    {
        $frame = igk_create_node("div");
        $d = $frame;
        $d->clearChilds();
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("add_view");
        $frm->addSLabelInput(IGK_FD_NAME);
        $frm->addBr();
        $frm->addTextArea("clContent")->setClass("php-code")->setStyle("min-width: 360px; min-height:320px; padding: 4px;");
        $frm->addHSep();
        $frm->addBtn("btn_save", __("Save"));
        if (igk_is_ajx_demand()) {
            igk_ajx_panel_dialog(__("Add view"), $frame);
        } else {
            igk_app()->Doc->body->addObData(
                function () use ($frame) {
                    igk_ajx_panel_dialog(__("Add view"), $frame);
                },
                IGK_HTML_NOTAG_ELEMENT
            );
        }
        return $frame;
    }
    ///<summary>Request add controller</summary>
    public function ca_addCtrl()
    {
        
        if (igk_qr_confirm() && $this->ConfigCtrl->getIsConnected() && ($ctrl = igk_getctrl(IGK_CTRL_MANAGER, false))) {
            $g = 0;
            $msg = "msg.ctrl.notadded";
            $v_not = igk_notifyctrl(igk_getr("notification", 'controller'));
            if ($ctrl->addControllerRequest(null, igk_getr("clWebPage", false), igk_getr("clParentCtrl", null))) {
                $g = 1;
                !igk_is_ajx_demand() && $v_not->addMsgr("msg.controlleradded");
                $msg = "msg.ctrl.added";
            } else {
                !igk_is_ajx_demand() && $v_not->addErrorr("err.controllernotadded");
                $g = 4;
            }
            igk_ajx_toast(__($msg), igk_css_type($g));
            igk_resetr();
            $this->setParam("ca:view_frame", null);
            $this->View();
            if (igk_is_ajx_demand()) {
                igk_ajx_replace_ctrl_view($this);
                igk_exit();
            }
        } else {
            igk_ajx_toast(__("-- failed to add controller --"), "igk-danger");
            igk_exit();
        }
    }
    ///<summary></summary>
    public function ca_addfield_ajx()
    {
        $c = $this->ca_getFieldInfo(DbColumnInfo::NewEntryInfo());
        if ($table = $this->getParam("ctrl:ca_tabInfo")) {
            igk_html_add($c, $table);
        }
        igk_wln($c->render());
    }
    ///<summary></summary>
    public function ca_ClearTableList_ajx()
    {
        $this->setParam("ctrl:ca_tabInfo", null);
        $f = igk_getctrl($this->SelectedController)->getDataSchemaFile();
        if (file_exists($f))
            @unlink($f);
        igk_wl($this->ca_getTableInfo()->render());
    }
    ///<summary></summary>
    public function ca_ctrl_article_select_lang_ajx()
    {
        $ctrl = igk_getctrl(igk_getr("ctrl"), false);
        $this->m_selectedLang = igk_getr("n");
        $div = igk_create_node("div");
        $this->_buildViewArticle($div, $ctrl);
        igk_wl($div->getInnerHtml());
    }
    ///<summary></summary>
    public function ca_ctrl_drop()
    {
        $this->ca_drop_controller_ajx($this->SelectedController);
    }
    ///<summary></summary>
    public function ca_db_drop_db_file_ajx()
    {
        $f = igk_getctrl($this->SelectedController)->getDataSchemaFile();
        if (file_exists($f)) {
            unlink($f);
            $this->View();
            igk_frame_close("add_edit_db_frame");
        }
        igk_exit();
    }
    ///<summary></summary>
    public function ca_download_view()
    {
        $n = igk_getr("n");
        $ctrl = $this->SelectedController;
        if (($ctrl == null) || !isset($n))
            return null;
        $f = igk_io_dir(igk_getctrl($ctrl)->getViewDir() . "/" . $n);
        if (file_exists($f)) {
            igk_download_file(basename($f), $f);
            igk_exit();
        }
    }
    ///<summary>drop article in ajx request</summary>
    public function ca_drop_article_ajx()
    {
        $n = base64_decode(igk_getr("n"));
        $ctrl = ($c = igk_getr("ctrlid", null)) ? igk_getctrl($c) : $this->SelectedController;
        $f = igk_realpath($n);
        $_FRAMENAME = "frame_" . __FUNCTION__;
        if (!file_exists($f)) {
            if (($ctrl == null) || !isset($n)) {
                igk_notifyctrl()->addErrorr("err.nocontroller.selected");
                igk_notifyctrl()->TargetNode->renderAJX();
                igk_notifybox_ajx("controller not selected");
                return null;
            }
            $f = igk_io_dir(igk_getctrl($ctrl)->getArticlesDir() . "/" . $n);
        }
        if (file_exists($f)) {
            if (igk_qr_confirm()) {
                unlink($f);
                $this->View();
                if (igk_is_ajx_demand()) {
                    igk_ajx_replace_ctrl_view($this);
                }
            } else {
                $frame = igk_frame_add_confirm($this, $_FRAMENAME, $this->getUri("ca_drop_article_ajx"));
                $frame->Form->Div->Content = __(IGK_MSG_DELETEFILE_QUESTION, basename($n));
                $frame->Form["igk-confirmframe-response-target"] = strtolower($ctrl);
                $frame->Form->addInput("n", "hidden", base64_encode($n));
                $frame->Form->addInput("navigate", "hidden", igk_getr("navigate"));
                $frame->Form->addInput("ctrlid", "hidden", $ctrl ? $ctrl->getName() : null);
                $frame->renderAJX();
            }
        } else {
            igk_notifyctrl()->addInfor("msg.ca_drop_article_ajx_no_article_to_remove");
            igk_notifyctrl()->TargetNode->renderAJX();
        }
    }
    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    ///<param name="reconnect" default="1"></param>
    public function ca_drop_controller_ajx($ctrl = null, $reconnect = 1)
    {       
        $a = $ctrl ? $ctrl : (($ctrl = igk_getr("clController")) ? $ctrl : igk_getr("n"));
     
        if ($a) {
            $ctrl = igk_getctrl($a, false);
            if ($ctrl == null){
                igk_environment()->isDev() && igk_ilog("no selected controller");
                igk_ajx_toast(__("no selected controller"), "danger");
                return;
            }
            $canDelete = !BaseController::IsSysController($ctrl);
            if (!$canDelete){
                igk_ajx_toast(__("Can't delete controller"), "danger");
                igk_exit();
            }
            $is_ajx = igk_is_ajx_demand(); 
            if (igk_qr_confirm()) {
                if ($canDelete) {
                    $uri = igk_getconfigwebpagectrl()->getReconnectionUri();
                    if (igk_getctrl(IGK_CTRL_MANAGER)->removeCtrl($a)) {
                        $this->SelectedController = null;
                        $this->View();
                        $ctrl = igk_getconfigwebpagectrl();
                        if ($is_ajx) {
                            $doc = igk_get_last_rendered_document();
                            if ($doc === null)
                                $doc = igk_app()->Doc;
                            if (defined('IGK_CONFIG_PAGE'))
                                igk_getconfigwebpagectrl()->View();
                            $doc->body->renderAJX();
                            igk_exit();
                        } else {
                            igk_navto(igk_server()->HTTP_REFERER);
                        }
                    } else {
                        igk_ilog("drop the controlleur failed : " . $a, __FUNCTION__);
                    }
                } else {
                    $this->msbox->addErrorr("err.dropctrlfailed");
                }
            } else {
                $d = igk_create_node("div");
                $d->div()->Content = __(IGK_MSG_DELETECTRL_QUESTION, $a);
                $frm = $d->addForm();
                $frm["action"] = $this->getUri("ca_drop_controller_ajx");
                $frm->addInput("clController", "hidden", $a);
                $frm->addInput("forceview", "hidden", igk_getr("forceview", null));
                if (igk_is_ajx_demand()) {
                    $frm->addInput("ajx", "hidden", 1);
                }
                $b = $frm->div();
                $b->addInput("yes", "submit", __("btn.yes"));
                $b->addInput("no", "button", __("btn.no"))
                ->setAttribute("data-type", "cancel");
                $frm->addConfirm();
                $frm->addToken();
                igk_ajx_notify_dialog(__("title.dropController"), $d);
            
            }
        }
        else {
            igk_environment()->isDev() && igk_ilog("no selected controller");
        }
    }
    ///<summary></summary>
    public function ca_drop_view()
    {
        $n = igk_getr("n");
        $ctrl = $this->SelectedController;
        if (($ctrl == null) || !isset($n))
            return null;
        $f = igk_io_dir(igk_getctrl($ctrl)->getViewDir() . "/" . $n);
        if (file_exists($f)) {
            if (igk_qr_confirm()) {
                if (file_exists($f))
                    unlink($f);
                $this->View();
                if (igk_is_ajx_demand()) {
                    igk_ajx_panel_dialog_close();
                    igk_ajx_replace_ctrl_view($this);
                    igk_exit();
                }
            } else {
                $frame = igk_create_node("div");
                $form = $frame->add("form");
                $form["action"] = $this->getUri("ca_drop_view");
                $form->div()->Content = __(IGK_MSG_DELETEFILE_QUESTION, $n);
                $form->addInput("n", "hidden", $n);
                $form->addInput("confirm", "hidden", 1);
                igk_frame_bind_action($form->addActionBar(), 0);
                if (igk_is_ajx_demand()) {
                    $form["igk-ajx-form"] = 1;
                    igk_ajx_panel_dialog(__("Confirm"), $frame);
                    return;
                }
            }
            $this->View();
        }
    }
    ///<summary></summary>
    public function ca_dropfield()
    {
        $n = igk_getr("n");
        $table = $this->getParam("ctrl:ca_tabInfo");
        $tr = $table->getElementByAttribute("__id", $n);
        if ($tr) {
            igk_html_rm($tr);
        }
    }
    ///<summary> edition d'article simple par une demande ajax</summary>
    public function ca_edit_article_ajx($ctrlid = null, $name = null)
    {
        $ajx = 0;
        $f = "";
        $n = "";
        $f = base64_decode(igk_getr("fn"));
        $ctrlid = $ctrlid ?? igk_getr("ctrlid");
        $ctrl = igk_getctrl($ctrlid);
        $f = file_exists($f) ? $f : $ctrl->getArticle($f);
        if (file_exists($f)) {
            $str = igk_io_read_allfile($f);
            $t = igk_create_node("div");
            $t->div()->Content = "Path: " . igk_io_basepath(igk_io_basedir($f));
            $frm = $t->addForm();
            $frm["action"] = $this->getUri("update_article" . (($ajx == 1) ? null : "#" . $this->_getarticleid()));
            $ul = $frm->add("ul");
            $ul["class"] = "fitw";
            $ul->li()->addTextArea("clContent", $str)->setClass("igk-winui-text-editor");
            $txt["class"] = "fitw tyni";
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("navigate", "hidden", igk_getr("navigate", 0));
            $frm->addInput("ajx", "hidden", 1);
            $frm->addInput("cluri", "hidden", igk_getv($_SERVER, 'HTTP_REFERER'));
            $frm->addActionBar()->addInput("btn.edit", "submit", __("btn.edit"));
            igk_js_enable_tinymce($ul, "textarea#clContent");
            igk_ajx_panel_dialog(__("title.edit_1", basename($f)), $t);
        } else {
            igk_ajx_panel_dialog(__("Edit"), __("File not found: {0}", $f));
        }
        igk_exit();
    }
    ///<summary> create d'un FrameDialog pour l'édition d'article </summary>
    ///<params>
    ///$ctrlid: controller ou id du controller
    ///$name: nom ou chemin d'accèss au fichier
    ///$ajx:  s'il s'agit d'un context ajax ou nom
    ///$mode: si mode = 1 alors le name un le chemin d'accès complet au fichier sinon il s'agit du nom dans le repertoire Articles du controlleur
    ///$force: force creation if not exists
    ///</params>
    public function ca_edit_article_frame($ctrlid = null, $name = null, $ajx = 0, $mode = 0, $force = false)
    {
        $ctrl = igk_getctrl($ctrlid ? $ctrlid : igk_getr("ctrlid"), false);
        $n = $name ? $name : igk_getr("n");
        $f = igk_realpath(base64_decode($n));
        if (!file_exists($f)) {
            if (($ctrl == null) || !isset($n))
                return null;
            if ($mode == 0)
                $f = igk_io_dir($ctrl->getArticlesDir() . "/" . $n);
        }
        if ($force || file_exists($f)) {
            $articleid = $this->_getarticleid();
            $frame = igk_html_frame($this, "frame_edit_article", $ajx == 1 ? null : "#" . $articleid);
            $frame->clearChilds();
            $frame->Title = __("title.editarticle_1", basename($f));
            $str = IO::ReadAllText($f);
            $d = $frame->BoxContent;
            $frm = $d->addForm();
            $frm["action"] = $this->getUri("update_article" . (($ajx == 1) ? null : "#" . $articleid));
            $ul = $frm->add("ul");
            $txt = $ul->li()->addTextArea("clContent", $str);
            $txt["class"] = "frame_textarea";
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("clframe", "hidden", $frame["id"]);
            $frm->addInput("clctrl", "hidden", $ctrl ? $ctrl->getName() : null);
            $frm->addBtn("btn_update", __("Update"))->setClass("igk-btn igk-btn-default");
            $frame->Form = $frm;
            return $frame;
        }
        return null;
    }
    ///<summary>Represente ca_edit_articlewtiny function</summary>
    public function ca_edit_articlewtiny()
    {
        $q = igk_getr("q");
        $h = igk_html_uri(IGK_APP_DIR . base64_decode($q));
        if (file_exists($h)) {
            if (igk_server()->method("POST")) {
                if (igk_getr("btn_save")) {
                    igk_header_set_contenttype("txt");
                    igk_io_w2file($h, igk_getr("content"));
                    igk_navto_referer();
                }
            }
            $frm = igk_create_node('form');
            $frm["action"] = $this->getUri(__FUNCTION__);
            $frm["method"] = "POST";
            igk_html_form_initfield($frm);
            $frm->addFields(["q" => ["value" => $q, "type" => "hidden"], "content" => ["value" => file_get_contents($h), "type" => "textarea"]]);
            $acbar = $frm->addActionBar();
            $acbar->addInput("btn.save", "submit", __("Save"));
            igk_js_enable_tinymce($frm, "#content");
            igk_ajx_panel_dialog(__("Edit"), $frm);
        }
    }
    ///<summary></summary>
    ///<param name="ctrlid" default="null"></param>
    ///<param name="name" default="null"></param>
    public function ca_edit_articlewtiny_f_ajx($ctrlid = null, $name = null)
    {
        $n = $name ? $name : igk_getr("n", igk_getr("fn"));
        $frame = $this->ca_edit_articlewtiny_f_frame($ctrlid, $n, 1, igk_getr("m", 0), igk_getr("fc"));
        if ($frame) {
            igk_ajx_notify_dialog(__("title.editarticlewtiny_1", basename(base64_decode($n))), $frame->render(null));
        } else {
            igk_ilog(__METHOD__ . " with tiny failed to create frame");
            igk_ilog(igk_ob_get($_REQUEST));
            igk_ilog(base64_decode(igk_getr("fn")));
        }
    }
    ///<summary></summary>
    ///<param name="ctrlid" default="null"></param>
    ///<param name="name" default="null"></param>
    ///<param name="ajx"></param>
    ///<param name="mode"></param>
    ///<param name="force" default="false"></param>
    public function ca_edit_articlewtiny_f_frame($ctrlid = null, $name = null, $ajx = 0, $mode = 0, $force = false)
    {
        $ctrl = igk_getctrl($ctrlid ? $ctrlid : igk_getr("ctrlid"), false);
        $n = $name ? $name : igk_getr("n");
        if ((($mode == 0) && ($ctrl === null)) || !isset($n)) {
            igk_ilog("not set");
            return null;
        }
        if ($mode == 0)
            $f = igk_io_dir($ctrl->getArticlesDir() . "/" . $n);
        else
            $f = base64_decode($n);
        if ($force || file_exists($f)) {
            $str = IO::ReadAllText($f);
            $frm = igk_create_node('form');
            $frm["action"] = $this->getUri("ca_update_articlewtiny_f");
            $ul = $frm->add("ul");
            $ul->li()->addSLabelInput("clRemoveStyles", "checkbox");
            $ul->li()->addSLabelInput("clRemoveImgSize", "checkbox");
            $ul->li()->addTextArea("clContent", $str);
            igk_js_enable_tinymce($ul, "clContent");
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("clframe", "hidden", 'frame_edit_article');
            if ($ctrl)
                $frm->addInput("clctrl", "hidden", $ctrl->getName());
            $frm->addBtn("btn_update", __("Update"));
            return $frm;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="ctrlid" default="null"></param>
    ///<param name="name" default="null"></param>
    ///<param name="ajx"></param>
    ///<param name="mode"></param>
    public function ca_edit_articlewtiny_frame($ctrlid = null, $name = null, $ajx = 0, $mode = 0)
    {
        $ctrl = igk_getctrl($ctrlid ? $ctrlid : igk_getr("ctrlid"), false);
        $n = $name ? $name : igk_getr("n");
        if (($ctrl == null) || !isset($n))
            return null;
        if ($mode == 0)
            $f = igk_io_dir($ctrl->getArticlesDir() . "/" . $n);
        else
            $f = base64_decode($n);
        if (file_exists($f)) {
            $articleid = $this->_getarticleid();
            $frame = igk_html_frame($this, "frame_edit_article", $ajx == 1 ? null : "#" . $articleid);
            $frame->clearChilds();
            $frame->Title = __("title.editarticlewtiny_1", basename($f));
            $str = IO::ReadAllText($f);
            $d = $frame->BoxContent;
            $frm = $d->addForm();
            $frm["action"] = $this->getUri("update_articlewtiny" . (($ajx == 1) ? null : "#" . $articleid));
            $ul = $frm->add("ul");
            $ul->li()->addSLabelInput("clRemoveStyles", "checkbox");
            $ul->li()->addSLabelInput("clRemoveImgSize", "checkbox");
            $ul->li()->addTextArea("clContent", $str);
            igk_js_enable_tinymce($ul, "clContent");
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("clframe", "hidden", $frame["id"]);
            $frm->addInput("clctrl", "hidden", $ctrl->getName());
            $frm->addBtn("btn_update", __("Update"));
            return $frame;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="oldcontent" default="null"></param>
    public function ca_edit_ctrl_ajx($oldcontent = null)
    {
        $name = igk_getr("n", null);
        $ctrl = $name == null ? igk_getctrl($this->SelectedController, false) : igk_getctrl($name, false);
        if ($ctrl == null)
            return null;
        $f = igk_io_dir($ctrl->getDeclaredFileName());
        if (file_exists($f)) {
            $frame = igk_create_node("div");
            $frame->clearChilds();
            $frame->Title = __("title.editctrl", basename($f));
            $str = null;
            if (!$oldcontent)
                $str = IO::ReadAllText($f);
            $d = $frame->div();
            $frm = $d->addForm();
            $frm["action"] = $this->getUri("update_ctrl" . ($name == null ? IGK_STR_EMPTY : "&n=" . $name));
            if ($oldcontent) {
                $frm->add("div", array("class" => "notification_bad"))->Content = __("Old Content is not defined");
            }
            $ul = $frm->add("ul");
            $v_txtarea = $ul->li()->div()->setClass("fitw overflow-x-a")->addTextArea("clContent", ($oldcontent == null) ? $str : $oldcontent)->setClass("igk-php-code")->setAttribute("spellcheck", "false")->setAttribute("cols", 40)->setAttribute("rows", 30);
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("clframe", "hidden", $frame["id"]);
            $frm->addHSep();
            $frm->addBtn("btn_update", __("Update"));
            igk_ajx_panel_dialog("Edit controller", $frame);
        }
    }
    ///<summary></summary>
    ///<param name="rendering" default="true"></param>
    public function ca_edit_ctrl_atricles_ajx($rendering = true)
    {
        $frame_name = "ca_edit_ctrl_atricles_ajx_frame";
        $frame = igk_html_frame($this, $frame_name);
        $p = $frame->ForCtrl;
        $n = igk_getr("n", $p ? $p->Name : null);
        $ctrl = igk_getctrl($n);
        if ($ctrl == null) {
            $frame->ForCtrl = null;
            $frame->IsRegister = null;
            igk_frame_close($frame_name);
            return;
        }
        $frame->Title = __("title.editctrlaticles_1", $ctrl->getName());
        $frame->CloseUri = $this->getUri("unreg_view_frame");
        $c = $frame->BoxContent;
        $c->clearChilds();
        $d = $c->div();
        $this->_buildViewArticle($d, $ctrl);
        $t = $frame->IsRegister;
        if (!$t) {
            $t = true;
            $frame->IsRegister = $t;
            $ctrl->addViewCompleteEvent($this, "view_frame_complete");
        }
        if ($rendering)
            $frame->renderAJX();
        $frame->ForCtrl = $ctrl;
    }
    ///<summary></summary>
    public function ca_edit_ctrl_force_view_ajx()
    {
        $n = igk_getctrl(igk_getr("n"));
        if ($n) {
            igk_ajx_replace_ctrl_view($n);
        }
    }
    ///<summary></summary>
    ///<param name="render" default="true"></param>
    public function ca_edit_ctrl_properties_ajx($render = true)
    {
        $name = igk_getr("n", null);
        $ctrl = $name == null ? igk_getctrl($this->SelectedController, false) : igk_getctrl($name, false);
        if ($ctrl == null)
            return null;
        $title = __("title.editctrl.properties_1", $ctrl->getName());
        $frm = igk_create_node('form');
        $frm["action"] = $this->getUri("ca_update_ctrl_properties" . ($name == null ? IGK_STR_EMPTY : "&n=" . $name));
        $ul = $frm->div()->setClass("igk-v-h")->setStyle("max-height:200px; overflow-y:auto;")->add("ul");
        $d = igk_sys_getdefaultctrlconf();
        $v_itab = null;
        $v_classname = get_class($ctrl);
        if (method_exists($v_classname, "GetNonConfigurableConfigInfo")) {
            $v_itab = igk_array_tokeys(call_user_func_array(array($v_classname, "GetNonConfigurableConfigInfo"), array()));
        }
        foreach (array_keys($d) as $k) {
            if (isset($v_itab[$k]))
                continue;
            $vv = igk_getv($ctrl->Configs, $k);
            switch (strtolower($k)) {
                case "clparentctrl":
                    $t = igk_getctrl(IGK_MENU_CTRL)->__getEditController($ul, $vv, "lb.parentcontroller", $this->SelectedController);
                    $t->setId($k);
                    break;
                case "cldataadaptername":
                    $li = $ul->li();
                    $this->_ca_add_adapter($li, $k, $vv);
                    break;
                case "cldataschema":
                    $li = $ul->li();
                    $li->add("label", array("for" => $k))->Content = __("lb." . $k);
                    $sl = $li->addSelect($k);
                    foreach (["enum.false" => 0, "enum.true" => 1] as $ck => $cv) {
                        $opt = $sl->addOption();
                        $opt->setAttribute("value", $cv);
                        $opt->Content = __($ck);
                        if ($vv == $cv) {
                            $opt->setAttribute("selected", true);
                        }
                    }
                    break;
                default:
                    $li = $ul->li();
                    $li->add("label", array("for" => $k))->Content = __("lb." . $k);
                    $li->addInput($k, "text", $vv);
                    break;
            }
        }
        $this->_buildAdditionalInfo($ctrl, $ul);
        $frm->addHSep();
        $frm->addBtn("btn_update", __("Update"))->setClass("igk-btn");
        if ($render)
            igk_ajx_panel_dialog($title, $frm);
        return $frm;
    }
    ///<summary></summary>
    ///<param name="render" default="true"></param>
    public function ca_edit_ctrl_views_ajx($render = true)
    {
        igk_die(__METHOD__);
    }
    ///<summary>request edit data table structures with ajx </summary>
    public function ca_edit_db_ajx($ctrl = null, $table = null)
    {
        $ctrl = $ctrl == null ? igk_getctrl($this->SelectedController) : $ctrl;
        if ($ctrl == null)
            return;
        $t = $this->ca_getTableInfo($ctrl, $table);
        if ($t == null)
            return;
        $table = $table == null ? $ctrl->DataTableName : $table;
        $frame = igk_html_frame($this, "add_edit_db_frame", $table ? $this->getUri("ca_edit_db_close_frame&db_tbr=" . $table) : null);
        $frame->Title = __("title.editdbArticle", $ctrl);
        $d = $frame->BoxContent;
        $d->clearChilds();
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("ca_update_dbdata");
        $div = $frm->div();
        $div["style"] = "max-width:824px; max-height:400px; min-height: 200px;  overflow:auto;";
        $ul = $div->add("ul");
        $ul->Index = -10;
        $ul->li()->addSLabelInput("clTableName", "text", $table);
        $div->addHSep();
        $div->div()->setClass("fitw overflow-x-a")->add($t);
        $frm->addHSep();
        $div = $frm->div();
        $a = HtmlUtils::AddImgLnk($div, "#", "add_16x16");
        $a["onclick"] = igk_js_ajx_post_auri($this->getUri("ca_addfield_ajx"), "window.igk.ctrl.ca_update");
        $a = HtmlUtils::AddImgLnk($div, "#", "drop_16x16");
        $a["onclick"] = igk_js_ajx_post_auri($this->getUri("ca_ClearTableList_ajx"), "window.igk.ctrl.ca_updatetable");
        $frm->addInput("btn.update", "submit", __("Update"));
        $f = $ctrl->getDataSchemaFile();
        if (file_exists($f)) {
            $frm->addInput("btn.update", "button", __("btn.dropdb"))->setAttribute("onclick", igk_js_ajx_post_auri("ca_db_drop_db_file_ajx"));
        }
        igk_wl($frame->render());
    }
    ///<summary></summary>
    public function ca_edit_db_close_frame()
    {
        $table = igk_getr("db_tbr");
        $key = "ctrl:ca_tabInfo" . ($table ? "/" . $table : "");
        $this->setParam($key, null);
    }
    ///<summary>edit controller view</summary>
    ///<param name="oldcontent" default="null">the old content</param>
    ///<param name="errormesage" default="null">error message</param>
    ///<param name="error" default="null"></param>
    public function ca_edit_view($oldcontent = null, $errormesage = null, $error = null)
    {
        $ctrl = igk_getctrl($this->SelectedController, false);
        $n = igk_getr("n");
        if (($ctrl == null) || !isset($n))
            return null;
        $f = igk_io_dir($ctrl->getViewDir() . "/" . $n);
        if (file_exists($f)) {
            $frame = igk_html_frame($this, "frame_edit_view", "#" . $this->_getviewid());
            $frame->clearChilds();
            $frame->Title = __("title.editview_1", basename($f));
            $str = null;
            if (!$oldcontent)
                $str = utf8_decode(IO::ReadAllText($f));
            $d = $frame->BoxContent;
            $frm = $d->addForm();
            $frm["action"] = $this->getUri("ca_update_view#" . $this->_getviewid());
            if ($error) {
                $frm->add("div", array("class" => "notification_bad"))->Content = "Somme Error Append When try to save the file: <br/ >" . $errormesage . "<br/>" . $oldcontent;
            }
            $k = ($oldcontent == null) ? $str : $oldcontent;
            $ul = $frm->add("ul");
            $area = $ul->li()->addTextArea("clContent", $k);
            $area["style"] = "width: 400px; min-height: 300px;";
            $frm->addInput("clfile", "hidden", base64_encode(urlencode($f)));
            $frm->addInput("clframe", "hidden", $frame["id"]);
            $frm->addInput("n", "hidden", $n);
            $frm->addHSep();
            $frm->addBtn("btn_update", __("Update"));
            $frm->Width = "400px";
            $frm->Height = "300px";
            if (igk_is_ajx_demand()) {
                igk_ajx_panel_dialog(__("title.editview_1", basename($f)), $frame->BoxContent);
            } else {
                igk_navtocurrent();
            }
            return $frame;
        }
    }
    ///<summary>get controller type addition info</summary>
    public function ca_get_ctrl_type_info_ajx()
    {
        $p = $this->getParam("ca:view_frame");
        $n = igk_getr("n");
        if ($p != null) {
            $p->clearChilds();
            $this->_buildAdditionalInfo($n, $p);
            $p->renderAJX();
        } else {
            igk_notifybox_ajx("no [ca:view_frame] setup");
        }
    }
    ///<summary></summary>
    ///<param name="info"></param>
    public function ca_getFieldInfo($info)
    {
        $tr = igk_create_node("tr");
        $tr["__id"] = igk_new_id();
        $tr->addTd()->Content = IGK_HTML_SPACE;
        foreach ($info as $v => $k) {
            $td = $tr->addTd();
            switch (strtolower($v)) {
                case "clisunique":
                case "clautoincrement":
                case "clisprimary":
                case "clisindex":
                case "clnotnull":
                case "clisuniquecolumnmember":
                case "clisnotinqueryinsert":
                    $c = $td->add("div", array("class" => "dispb fitw", "style" => "text-align:center;"))->addInput("__cl" . $v . "[]", "checkbox", null, array("onchange" => "javascript:(function(q){window.igk.ctrl.ca_update_checkchange(q, '" . $v . "[]');})(this);"));
                    if ($k)
                        $c["checked"] = "true";
                    $td->addInput($v . "[]", "hidden", $k);
                    break;
                case "cltype":
                    igk_html_build_select($td, $v . "[]", DbColumnDataType::GetDbTypes(), null, $k);
                    break;
                default:
                    $i = $td->addInput($v . "[]", "text", $k);
                    $i["class"] = "-cltext";
                    $i["style"] = "max-width:125px;";
                    break;
            }
        }
        HtmlUtils::AddImgLnk($tr->addTd(), $this->getUri("ca_dropfield&n=" . $tr["__id"]), "drop_16x16");
        return $tr;
    }
    ///<summary>retrieve data table info</summary>
    ///<param name="ctrl" default="null">controller table</param>
    ///<param name="table" default="null">table name</param>
    public function ca_getTableInfo($ctrl = null, $table = null)
    {
        $key = "ctrl:ca_tabInfo" . ($table ? "/" . $table : "");
        $t = $this->getParam($key);
        $tb = null;
        if ($t != null)
            return $t;
        else
            $tb = igk_create_node("table");
        $ctrl = $ctrl == null ? igk_getctrl($this->SelectedController) : $ctrl;
        $tr = $tb->addTr();
        $t = DbColumnInfo::GetColumnInfo();
        $tr->add("th")->Content = IGK_HTML_SPACE;
        foreach ($t as $v => $k) {
            $tr->add("th")->Content = __("lb." . $v);
        }
        $tr->add("th")->Content = IGK_HTML_SPACE;
        if ($table == null) {
            if (file_exists($ctrl->getDataSchemaFile())) {
                $tab = $ctrl->getDataTableInfo();
                foreach ($tab as $k) {
                    $tb->add($this->ca_getFieldInfo($k));
                }
            } else {
                $info = DbColumnInfo::NewEntryInfo();
                $info->clIsPrimary = true;
                $tb->add($this->ca_getFieldInfo($info));
            }
        } else {
            $inf = igk_db_get_table_info($table);
            if (isset($inf["ColumnInfo"]))
                $inf = $inf["ColumnInfo"];
            foreach ($inf as $k) {
                $tb->add($this->ca_getFieldInfo($k));
            }
        }
        $this->setParam($key, $tb);
        return $tb;
    }
    ///<summary></summary>
    public function ca_remove_child()
    {
        $ctrl = igk_getctrl(igk_getr("clParentCtrl"));
        $p = igk_getctrl(igk_getr("clChild"));
        if (!$ctrl || !$p)
            return;
        $p->setWebParentCtrl(null, true);
        $ctrl->unregChildController($p);
        $this->View();
    }
    ///<summary></summary>
    public function ca_remove_parent()
    {
        $ctrl = igk_getctrl(igk_getr("clCtrl"));
        $p = igk_getctrl(igk_getr("clParent"));
        if (!$ctrl || !$p)
            return;
        $p->unregChildController($ctrl);
        $ctrl->setWebParentCtrl(null, true);
        $this->View();
    }
    ///<summary>use to reset data base for the current controller</summary>
    public function ca_reset_db_ajx()
    {
        if (igk_qr_confirm()) {
            $ctrl = igk_getctrl($this->SelectedController, false);
            $c = igk_getctrl("api");
            IGKOb::Start();
            if ($c->datadb("resetctrldb", $ctrl)) {
                igk_notifyctrl()->addMsgr("msg.database_reset");
            } else {
                igk_notifyctrl()->addErrorr("err.database_reset");
            }
            IGKOb::Clear();
            igk_navtocurrent();
        }
        $frame = igk_frame_add_confirm($this, __METHOD__, $this->getUri(__FUNCTION__));
        $frame->Title = "Confirm ?";
        $dc = $frame->Form->Div;
        $dc->div()->Content = __("warn.question.dbwillbedestroyed");
        $frame->renderAJX();
    }
    ///<summary></summary>
    public function ca_selectedCtrlChanged()
    {
        $t = $this->getParam("ctrl:ca_tabInfo");
        if ($t != null)
            $t->clearChilds();
        $this->setParam("ctrl:ca_tabInfo", null);
    }
    ///<summary></summary>
    public function ca_setmenuhost()
    {
        $v_n = igk_getr("clCtrlMenuHost");
        igk_configs()->menuHostCtrl = $v_n;
        igk_save_config();
        $this->View();
        $ctrl = igk_getctrl($v_n);
        igk_getctrl(IGK_MENU_CTRL)->setMenuhostCtrl($ctrl);
        igk_sys_viewctrl($v_n);
    }
    ///<summary></summary>
    public function ca_setmenuhost_ajx()
    {
        $this->ca_setmenuhost();
    }
    ///<summary></summary>
    public function ca_tabv_ajx()
    {
        if (!igk_is_ajx_demand()) {
            igk_navto(igk_io_baseuri());
        }
        $g = igk_getr("g");
        // $n=$this->getTempParam(__CLASS__."://tabview_node/");        
        $n = igk_create_node("div");
        $n->clearChilds();
        switch ($g) {
            case 1:
                $this->_view_default_tab($n);
                break;
            case 2:
                $frm = $n->addForm();
                $frm->panel()->div()->Content = __("/!\\ Not Implement");
                break;
            case 3:
                $c = igk_template_mananer_ctrl();
                if ($c) {
                    $c->showConfig($n);
                } else {
                    $n->div()->setClass("igk-danger")->Content = __("/!\\ No template found");
                }
                break;
            default:
                $frm = $n->form();
                $frm->panel()->div()->Content = __("/!\\ Not Implement");
                break;
        }
        $n->renderAJX();
    }
    ///<summary></summary>
    public function ca_update_articlewtiny_f()
    {
        if (!igk_app()->ConfigMode) {
            igk_navtocurrent();
            return;
        }
        $this->update_articlewtiny();
        $_REQUEST["ctrlid"] = igk_getr("clctrl");
        $_REQUEST["n"] = basename(urldecode(base64_decode(igk_getr("clfile"))));
        igk_frame_close("frame_edit_article");
        igk_navtocurrent();
    }
    ///<summary></summary>
    public function ca_update_ctrl_properties()
    {
        $name = igk_getr("n", null);
        $ctrl = $name == null ? igk_getctrl($this->SelectedController, false) : igk_getctrl($name, false);
        if ($ctrl == null) {
            return null;
        }
        $c = igk_get_robj();
        $oldparent = $ctrl->Configs->clParentCtrl;
        foreach ($c as $k => $v) {
            $s = igk_getr($k);
            switch ($k) {
                case "clParentCtrl": {
                        if ($s == "none") {
                            $ctrl->Configs->$k = null;
                        } else {
                            if (igk_can_set_ctrlparent($ctrl, $s)) {
                                $ctrl->Configs->$k = $s;
                            } else {
                                igk_debug_wln("can't changed parent");
                            }
                        }
                    }
                    break;
                default:
                    $ctrl->Configs->$k = $s;
                    break;
            }
        }
        if (method_exists($ctrl, "SetCustomConfigInfo")) {
            $t = $ctrl->Configs;
            $ctrl->SetCustomConfigInfo($t);
        }
        $notify = igk_notifyctrl();
        if ($ctrl->storeConfigSettings()) {
            $notify->addSuccess(__("Controller [{0}] updated", $ctrl->getName()));
        } else {
            igk_ilog("configuration failed");
            $notify->addError(__("Update controller setting failed."));
        }
        if ($ctrl->Configs->clParentCtrl != $oldparent) {
            igk_sys_viewctrl($oldparent);
        }
        igk_ctrl_viewparent($ctrl);
        if (!igk_is_confpagefolder()) {
            $this->ca_edit_ctrl_properties_ajx(false);
        }
    }
    ///<summary></summary>
    public function ca_update_dbdata()
    {
        $obj = igk_get_robj();
        $e = igk_create_node(DbSchemas::DATA_DEFINITION);
        $e["TableName"] = igk_getr("clTableName");
        unset($obj->clTableName);
        $v_kexist = array();
        for ($i = 0; $i < igk_count($obj->clName); $i++) {
            if (empty($obj->clName[$i]) || isset($v_kexist[$obj->clName[$i]]))
                continue;
            $v_kexist[$obj->clName[$i]] = 1;
            $cl = $e->add(IGK_COLUMN_TAGNAME);
            foreach ($obj as $k => $v) {
                $cl[$k] = $v[$i];
            }
        }
        $f = igk_getctrl($this->SelectedController)->getDataSchemaFile();
        igk_io_save_file_as_utf8($f, $e->render((object)array("Indent" => true)));
        igk_frame_close("add_edit_db_frame");
        $this->setParam("ctrl:ca_tabInfo", null);
        $this->View();
    }
    ///<summary></summary>
    public function ca_update_view()
    {
        $f = urldecode(base64_decode(igk_getr("clfile")));
        $v_c = igk_str_remove_empty_line(igk_getr("clContent"));
        $v_frame = igk_getr("clframe");
        $ctrl = igk_getctrl($this->SelectedController, false);
        $v_old = IO::ReadAllText($f);
        igk_io_savecontentfromtextarea($f, $v_c, true);
        $error = array();
        $code = 0;
        if (!igk_is_function_disable("exec")) {
            @exec("php -l \"" . $f . "\"", $error, $code);
        }
        if ($code == 0) {
            igk_notifyctrl()->addMsg(__("MSG.ViewFileSaved", basename($f)));
        }
        $ctrl->View();
        igk_frame_close($v_frame);
    }
    ///<summary></summary>
    public function ca_view_body_ajx()
    {
        $uri = base64_decode(igk_getr("uri"));
        igk_loadr($uri);
        if (igk_app()->getControllerManager()->InvokeUri($uri, false)) {
            igk_app()->getControllerManager()->ViewControllers();
            igk_app()->Doc->body->renderAJX();
        }
    }
    ///<summary> handle view tab information </summary>
    public function controller($view = "infotab")
    {
        $t = igk_create_node("div");
        if (file_exists($file = $this->getViewFile("tab." . $view, 0))) {
            $this->loader->view($file, ["t" => $t, "viewid" => $this->_getviewid(), "s_ctrl" => igk_getctrl($this->SelectedController), "articleid" => $this->_getarticleid()]);
            $this->setParam("tab:editresult", $view);
        } else {
            $t->addPanel()->setClass("igk-danger")->Content = __("No configuration page available");
        }
        $t->renderAJX();
        igk_exit();
    }
    ///<summary>get an article and download it </summary>
    public function download_article()
    {
        $n = igk_getr("n");
        $ctrl = $this->SelectedController;
        if (($ctrl == null) || !isset($n))
            return null;
        $f = igk_io_dir(igk_getctrl($ctrl)->getArticlesDir() . "/" . $n);
        if (file_exists($f)) {
            igk_download_file(basename($f), $f);
            igk_navtocurrent();
            igk_exit();
        }
    }
    ///<summary>remove an article.	</summary>
    public function drop_article()
    {
        $n = igk_getr("n");
        $ctrl = $this->SelectedController;
        if (($ctrl == null) || !isset($n))
            return null;
        $f = igk_io_dir(igk_getctrl($ctrl)->getArticlesDir() . "/" . $n);
        $_FRAMENAME = "frame_drop_article_confirmation";
        if (file_exists($f)) {
            if (igk_qr_confirm()) {
                unlink($f);
                igk_wln_e("drop article");
            } else {
                $frame = igk_frame_add_confirm($this, $_FRAMENAME, $this->getUri("drop_article"));
                $frame->Form->Div->Content = __(IGK_MSG_DELETEFILE_QUESTION, $n);
                $frame->Form->addInput("n", "hidden", $n);
            }
            $this->View();
        }
    }
    ///<summary></summary>
    public function edit_article()
    {
        $this->ca_edit_article_frame($this->SelectedController, igk_getr("n"));
    }
    ///<summary></summary>
    public function edit_articlewtiny()
    {
        $this->ca_edit_articlewtiny_frame($this->SelectedController, igk_getr("n"));
    }
    ///<summary>filter article by language</summary>
    public function filter_article_by_lang()
    {
        $this->m_filter_article_lang = igk_getr("n");
        $this->View();
    }
    ///<summary></summary>
    public function getConfigPage()
    {
        return "articleconfig";
    }
    ///<summary></summary>
    public function getCtrlArticle()
    {
        $c = igk_getr("ctrl");
        $n = igk_getr("n");
        igk_getctrl($c)->getArticle($n);
        igk_exit();
    }
    ///<summary></summary>
    public function getName()
    {
        return IGK_CA_CTRL;
    }
    ///<summary></summary>
    public function getSelectedController()
    {
        return $this->getParam(self::SL_SELECTCONTROLLER);
    }
    ///<summary></summary>
    protected function initComplete($context=null)
    {
        parent::initComplete();
        $this->setup_defaultpage();
    }
    ///<summary></summary>
    protected function initialize()
    {
        igk_reg_hook("SelectedControllerChanged", array($this, "ca_selectedCtrlChanged"));
    }
    ///<summary></summary>
    ///<param name="funcname"></param>
    public function IsFunctionExposed($funcname)
    {
        $rgx = "/(view_body_ajx|update_article)/i";
        if (igk_is_conf_connected() || preg_match($rgx, $funcname))
            return true;
        return parent::IsFunctionExposed($funcname);
    }
    ///<summary></summary>
    public function lst_adapter_ajx()
    {
        $n = igk_create_node("div");
        $t = igk_getr("t");
        $cs = get_class(igk_get_data_adapter($t));
        if (method_exists($cs, "GetSchemaOptions")) {
            call_user_func_array(array($cs, "GetSchemaOptions"), array($n));
        }
        $n->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    protected function onSelectedControllerChanged()
    {
        igk_hook("SelectedControllerChanged", $this, array($this->getSelectedController()));
    }
    ///<summary>search article . reload the view</summary>
    public function search_article()
    {
        $this->m_search_article = igk_getr("m_search_article");
        $this->View();
    }
    ///<summary></summary>
    public function search_view()
    {
        $this->m_search_view = igk_getr("m_search_view");
        $this->View();
    }
    ///<summary></summary>
    public function select_controller_ajx()
    {
        $data = [];
        $this->SelectedController = igk_getr("n");
        $n = igk_create_node("div");
        $this->_viewCtrlEditResult($n);
        $data["select_result"] = $n->render();
        $n = igk_create_node("col")->setClass("igk-col-3-3")->setId("edit_ctrl");
        $this->_view_ctrl_EditCtrl($n);
        $data["edit_result"] = $n->render();
        $data["selected"] = $this->SelectedController;
        return new JsonResponse(json_encode($data));
    }
    ///<summary>set the default page controller</summary>
    public function setdefaultpage()
    {
        $n = igk_getr("clDefaultCtrl");
        if (igk_configs()->default_controller != $n) {
            igk_configs()->default_controller = $n;
            igk_save_config();
            $this->View();
            igk_app()->session->setParam("forceview", 1);
            igk_hook("sys://event/defaultpagechanged", $this);
            igk_kill_all_sessions(session_id());
            return 1;
        }
    }
    ///<summary></summary>
    public function setdefaultpage_ajx()
    {
        if ($this->setdefaultpage()) {
            igk_ajx_toast(__("default controller changed"), "igk-success");
        }
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setSelectedController($value)
    {
        if ($this->SelectedController != $value) {
            $this->setParam(self::SL_SELECTCONTROLLER, $value);
            $this->onSelectedControllerChanged();
        }
    }
    ///<summary></summary>
    ///<param name="ctrltab" default="null"></param>
    private function setup_defaultpage($ctrltab = null)
    {
        $ctrl = igk_get_defaultwebpagectrl();
        if (($ctrl == null) && (count($ctrltab = igk_getv($ctrltab == null ? igk_get_all_uri_page_ctrl() : $ctrltab, "@base")) > 0)) {
            $n = $ctrltab[0]->getName();
            $cnf = igk_app()->getConfigs();
            if ($cnf->default_controller != $n) {
                $cnf->default_controller = $ctrltab[0]->getName();
                $cnf->saveData(true); 
            }
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="list"></param>
    ///<param name="content"></param>
    public function TabViewPage($n, $list, $content)
    {
        $g = $this->getParam(__CLASS__ . "://tabselected", 1);
        $i = 1;
        $content->clearChilds();
        foreach ($list->getElementsByTagName("li") as $k) {
            if ($i == $g) {
                $k->setClass("+igk-active");
                $content->addAJXScriptContent($k->getParam("uri"), $k->getParam("method"));
            } else
                $k->setClass("-igk-active");
            $i++;
        }
    }
    ///<summary></summary>
    public function unreg_view_frame()
    {
        $frame_name = "ca_edit_ctrl_atricles_ajx_frame";
        $frame = igk_get_frame($frame_name);
        if ($frame != null) {
            $frame->ForCtrl->removeViewCompleteEvent($this, "view_frame_complete");
            igk_frame_close($frame_name);
        }
    }
    ///<summary></summary>
    public function update_article()
    {
        $ajx = igk_is_ajx_demand() || igk_getr("ajx");
        $f = urldecode(base64_decode(igk_getr("clfile")));
        $v_c = igk_str_remove_empty_line(igk_html_unscape(igk_getr("clContent")));
        $v_frame = igk_getr("clframe");
        $v_dummy = igk_create_node("div");
        $id = igk_getr("clctrl");
        $ctrl = igk_getctrl($id, false);
        $n = igk_getr("n");
        if ($n && !file_exists($f) && $ctrl) {
            $f = $ctrl->getArticle($n);
        }
        if (!empty($f)) {
            try {
                $v_dummy->Load($v_c);
                if (igk_io_save_file_as_utf8($f, $v_c, true, false)) {
                    $this->__updateview($ctrl);
                    igk_notifyctrl()->addMsg(__("msg.filesaved_1", basename($f)));
                } else
                    igk_notifyctrl()->addError(__("err.filenotsaved_1", basename($f)));
                if ($ctrl)
                    igk_ctrl_viewparent($ctrl, null);
                if (igk_frame_is_available($v_frame))
                    igk_frame_close($v_frame, false);
            } catch (\Exception $ex) {
                igk_notifyctrl()->addError(__("err.filenotsaved_1", basename($f)));
                igk_notifyctrl()->addError($ex);
                igk_show_exception($ex);
                igk_exit();
            }
        }
        $rf = igk_getv(parse_url(igk_sys_srv_referer()), "path");
        if (!$ajx) {
            igk_navtocurrent();
        }
        if (!empty($rf)) {
            igk_navto($rf);
        }
        igk_ajx_toast(__("msg.articleupdated"));
        igk_exit();
    }
    ///<summary></summary>
    public function update_articlewtiny()
    {
        $f = urldecode(base64_decode(igk_getr("clfile")));
        $v_c = igk_str_remove_empty_line(igk_html_unscape(igk_getr("clContent")));
        $id = igk_getr("clctrl");
        $v_frame = igk_getr("clframe");
        $property = (object)array(
            "RemoveImgSize" => igk_getr("clRemoveImgSize"),
            "RemoveStyles" => igk_getr("clRemoveStyles")
        );
        $ctrl = igk_getctrl($id, false);
        if ($this->__write_article_for_tiny($f, $v_c, $property)) {
            $this->__updateview($ctrl);
            igk_notifyctrl()->addMsg(__("msg.filesaved", basename($f)));
        } else {
            igk_notifyctrl()->addError(__("e.filenotsaved", basename($f)));
        }
    }
    ///<summary></summary>
    ///<param name="oldcontent" default="null"></param>
    public function update_ctrl($oldcontent = null)
    {
        $f = urldecode(base64_decode(igk_getr("clfile")));
        $v_c = utf8_encode(igk_html_unscape(igk_getr("clContent"), IGK_STR_EMPTY));
        $v_frame = igk_getr("clframe");
        $ctrl = igk_getctrl($this->SelectedController, false);
        if (igk_php_check_and_savescript($f, $v_c, $error, $code) == false) {
            $this->ca_edit_view($v_c, count($error) . "update_ctrl::failed: code : " . $code . " " . implode("<br />", $error), true);
        } else {
            igk_session_destroy();
            igk_getconfigwebpagectrl()->reconnect();
            igk_notifyctrl()->addMsg(__("MSG.ViewFileSaved", basename($f)));
            igk_exit();
        }
    }
    ///<summary></summary>
    public function View()
    {
        $t = $this->TargetNode;
        if ($this->getIsVisible()) {
            $this->ConfigNode->add($t);
            $t = $t->clearChilds()->addPanelBox();
            igk_html_add_title($t, "Controller & Articles");
            $t->addReplaceUri();
            $b = $t->div();
            igk_html_article($this, "controller_and_article", $b);
            $dv = $t->div()->setClass("gc-v");
            $v_tabc = $dv->addComponent($this, HtmlComponents::AJXTabControl, "tab", 1);
            $v_tabc->clearChilds();
            $g = $this->getParam(__CLASS__ . "://tabselected", 1);
            $h = array("controller");
            foreach ($h as $k => $v) {
                $r = $k + 1;
                $v_tabc->addTabPage(__("tab." . $v), $this->getUri("ca_tabv_ajx&g={$r}"), 0);
            }
            $v_tabc->setTabViewListener(null);
            $v_tabc->select(0);
        } else {
            $this->TargetNode->clearChilds();
            igk_html_rm($this->TargetNode);
        }
    }
    ///<summary></summary>
    public function view_frame_complete()
    {
        $tb = $_REQUEST;
        igk_resetr();
        $this->ca_edit_ctrl_atricles_ajx(false);
        $_REQUEST = $tb;
    }
}
