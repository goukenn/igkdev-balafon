<?php
// @file: IGKSubDomainCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\Models\Subdomains;
use IGKSubDomainManager;
use function igk_resources_gets as __;


final class SubDomainController extends ConfigControllerBase
{
    // public function __construct()
    // {
    //     parent::__construct();
    //     igk_trace();
    //     igk_wln("create ");
    // }
    ///<summary>Represente __init_domain function</summary>
    private function __init_domain()
    {
        if (igk_is_cmd() || defined('IGK_NO_WEB') || defined('IGK_FRAMEWORK_ATOMIC'))
            return;
        if ($r = Subdomains::select_all()) {
            foreach ($r as $v) {
                $n = igk_getctrl($v->clCtrl, false);
                if ($n != null) {
                    igk_reg_subdomain($v->clName, $n, $v);
                }
            }
        }
    }
    ///<summary>Represente _updateview function</summary>
    private function _updateview()
    {
        igk_getctrl(IGK_CONF_CTRL)->setSelectedConfigCtrl($this);
        $s = $this->getConfigNode();
        $s->add($this->getTargetNode());
        $this->View();
        igk_ajx_replace_node($s, "#igk-cnf-content");
    }
    ///<summary></summary>
    public function dom_add_db_domain_ajx()
    { 
        if (!igk_is_ajx_demand()) {
            $uri = igk_server()->REQUEST_URI;
            $uri = explode("?", $uri)[0];
            igk_navto($uri);
        }
        if ( igk_qr_confirm() && igk_server()->method("POST") && igk_valid_cref(1)) {
            $obj = igk_get_robj();
            igk_getctrl(IGK_CONF_CTRL)->setSelectedConfigCtrl($this);
            $info = ["type" => "igk-success", "msg" => __("updated")];
            $r = 0;
            if (!empty($obj->clName) && ($r = Subdomains::createIfNotExists($obj))) {
                igk_notifyctrl(__FUNCTION__)->addSuccessr(__("mgs.dataupdated"));
                $this->__init_domain();
            } else {
                igk_notifyctrl(__FUNCTION__)->addErrorr("err.error_append_1", igk_debuggerview()->getMessage() . " code:" . $r);
                $info["type"] = "igk-danger";
                $info["msg"] = __("failed to add subdomain");
            }
            // Subdomains::delete();
            // igk_wln_e("file", $r);
            if (igk_is_ajx_demand())
                igk_ajx_toast($info["msg"], $info["type"]);
            // replace
            $s = $this->getConfigNode();
            $this->View();
            $s->add($this->getTargetNode());

            igk_ajx_replace_node($s, "#igk-cnf-content");
            igk_ajx_replace_uri(igk_io_baseuri() . "/Configs/#!p=" . $this->getConfigPage());
            igk_ajx_panel_dialog_close();
            igk_resetr();
        }
        $dv = igk_create_node();
        $frm = $dv->addForm();
        $frm["action"] = $this->getUri(__FUNCTION__);
        $frm["igk-ajx-form"] = 1;
        igk_include_view($this, $frm, "subdomain.add.form", array("func" => __FUNCTION__));
        $frm->addConfirm(1);
        igk_html_form_initfield($frm);
        igk_ajx_panel_dialog(__("Add domain"), $dv);
    }
    ///<summary></summary>
    public function dom_add_db_edit_domain_ajx()
    {
        if (igk_qr_confirm()) {
            $obj = igk_get_robj();
            $app = igk_app();
            if (!empty($obj->clName) && igk_is_domain_name($obj->clName)) {
                $app->Configs->website_domain = $obj->clName;
                $app->Session->Domain = $obj->clName;
                //IGKSubDomainManager::StoreBaseDomain($this, $obj->clName);
            }
            $this->View();
            igk_ajx_replace_ctrl_view($this);
            igk_ajx_panel_dialog_close();
            igk_flush_data();
            igk_exit();
        }
        $dv = igk_create_node();
        $frm = $dv->addForm();
        $frm["action"] = $this->getUri(__FUNCTION__);
        $frm["igk-ajx-form"] = 1;
        igk_include_view($this, $frm, "subdomain.editbasedomain.form");
        $frm->addConfirm(1);
        igk_ajx_panel_dialog(__("Edit Domain"), $dv);
    }
    ///<summary></summary>
    public function dom_drop_db_s_domain_ajx()
    {
        if (igk_qr_confirm()) {
            $items = $this->getParam("domain/deleteitems");
            if ($items) {
                $ad = igk_get_data_adapter($this);
                $ok = true;
                if ($ad->connect()) {
                    foreach ($items as $v) {
                        $ok = $ok & igk_db_delete($this, $this->DataTableName, array(IGK_FD_ID => $v));
                    }
                    $ad->close();
                }
                if ($ok) {
                    igk_notifyctrl()->addSuccessr("msg.items.deleted");
                    igk_hook("sys://domain/changed", $this);
                } else
                    igk_notifyctrl()->addErrorr("e.items.notdeleted");
                $this->View();
            }
            $this->setParam("domain/deleteitems", null);
            igk_navtocurrent();
        } else {
            $ti = igk_getr("item");
            if ($ti && (igk_count($ti) > 0)) {
                $frame = igk_frame_add_confirm($this, __FUNCTION__, $this->getUri(__FUNCTION__));
                $frame->Form->Div->Content = __("confirm.deletes");
                $this->setParam("domain/deleteitems", $ti);
                $frame->Form->addHidden("i", igk_getr("i"));
                $frame->renderAJX();
            }
        }
    }
    ///<summary></summary>
    public function dom_drop_domain_ajx()
    {
        $id = igk_getr("i");
        if (igk_qr_confirm()) {
            $msg = __("Domain dropped");
            $type = "igk-success";
            if (Subdomains::delete(array(IGK_FD_ID => $id))) {
                igk_hook("sys://domain/changed", $this);
                IGKSubDomainManager::getInstance()->Clear();
                $this->__init_domain();
            } else {
                $type = "danger";
                $msg = __("Failed to drop domain");
            }
            $this->_updateview();
            igk_ajx_replace_uri(igk_io_baseuri() . "/Configs/#!p=" . $this->getConfigPage());
            igk_ajx_toast($msg, $type);
            igk_notifyctrl()->bind($msg, $type);
            igk_ajx_panel_dialog_close();
            return;
        }
        $d = igk_create_node("div");
        $d->Content = __("remove this subdomain ? ");
        $form = igk_create_node("form");
        $form["action"] = $this->getUri(__FUNCTION__);
        $form["igk-ajx-form"] = 1;
        $form->addConfirm(1);
        $form->add($d);
        $form->actionbar(function ($a) {
            $a->input("y", "submit", __("btn.yes"));
            $a->button()->content = __("btn.no");
        });
        $form->input("i", "hidden", $id);
        igk_ajx_panel_dialog(__("Confirm dialog"), $form);

        // $frame=igk_frame_add_confirm($this, __FUNCTION__, $this->getUri(__FUNCTION__));
        // $frame->Form->Div->Content=__("confirm.delete");
        // $frame->Form->addHidden("i", igk_getr("i"));
        // $frame->renderAJX();
    }
    ///<summary></summary>
    public function dom_drop_domaintable()
    {
        if (!igk_is_conf_connected())
            return;
        $ad = igk_get_data_adapter($this);
        $t = "success";
        $table = Subdomains::table();
        $msg["success"] = __("Table {0} cleared", $table);
        $msg["danger"] = __("Table not found");
        $e = 0;
        if ($ad->connect()) {
            if (!($e = $ad->clearTable($table))) {
                $t = "danger";
            }
            $ad->close();
        }
        if (igk_is_ajx_demand()) {
            igk_ajx_toast($msg[$t], "igk-" . $t);
            $this->_updateview();
        } else {
            $_not = igk_notifyctrl();
            if ($e) {
                $_not->addSuccess($msg["success"]);
            } else {
                $_not->addError($msg["danger"]);
            }
        }
    }
    ///<summary> edit domain ajx </summary>
    public function dom_edit_domain_ajx()
    {
        if (igk_qr_confirm()) {
            $obj = igk_get_robj();
            if (SubDomains::update($obj)) {
                igk_notifyctrl("domain/dbz")->addSuccessr("mgs.dataupdated");
                igk_hook("sys://domain/changed", $this);
                IGKSubDomainManager::getInstance()->Clear();
                $this->__init_domain();
            } else {
                igk_notifyctrl("domain/dbz")->addErrorr("err.error_append_1", igk_debuggerview()->getMessage());
            }
            $this->update_view_list_ajx();
            igk_ajx_panel_dialog_close();
        } else {
            $data = Subdomains::select_row([IGK_FD_ID => igk_getr('i')]);
            if ($data == null) {
                return;
            }
            $dv = igk_create_node();
            $frm = $dv->addForm();
            $frm["action"] = $this->getUri(__FUNCTION__);
            $frm["igk-ajx-form"] = 1;
            igk_include_view($this, $frm, "subdomain.edit.form", array("data" => $data), true);
            $frm->addConfirm(1);
            igk_ajx_panel_dialog(__("title.editDomain"), $dv);
        }
        igk_flush_data();
    }
    ///<summary></summary>
    public function getConfigPage()
    {
        return "domain";
    }   
    ///get the controller that contain domain from setting. for the first usage
    public function getDomainCtrl($n, &$row)
    {
        $g = Subdomains::select([IGK_FD_NAME => $n]);
        // $g=igk_db_select_wherec($this, array(IGK_FD_NAME=>$n));
        if ($g && (igk_count($g) == 1)) {
            $row = $g[0];
            if ($ctrl = igk_getctrl($row->clCtrl, false)) {
                return $ctrl;
            }
            return igk_template_create_ctrl($row->clCtrl);
        }
        return null;
    }

    ///<summary></summary>
    public function getName()
    {
        return IGK_SUBDOMAINNAME_CTRL;
    }

    ///<summary></summary>
    public function View():BaseController
    {
        $t = $this->TargetNode;
        if (!$this->getIsVisible()) {
            $t->remove();
        } else {
            $box = $t->clearChilds()->addPanelBox()->div();
            igk_include_view($this, $box, "subdomain.config");
        }
        return $this;
    }
    protected function subdomain_view($n)
    {
        $ctrl = $this;
        $dom=IGKSubDomainManager::GetBaseDomain();
        $n->setClass("subdomain-listview");
        $n->div()->h2()->Content = __("Subdomain list");
        $tdv = $n->div();
        $tdv["class"] = "c-z igk-write";
        // $tdv->div()->addTitleLevel(5)->Content = R::ngets("title.DatbaseRegisteredDomain");
         
        $tdv->notifyhost("domain/dbz");
        // \opcache_reset(); 
        //igk_notifyctrl()->setNotifyHost($tdv->div(), "domain/dbz");
        $db = Subdomains::select_all();
        $c = 0;
        if (!$db) {
            $tdv->addNotifyBox("danger")->Content = __("No domain list"); 
        } else {
            $c = count($db);
            if ($c > 0) {
                $tdv->div()->Content = $c > 1 ? "Entries : " . $c : $c;
                $tb = $tdv->form()->setStyle("min-width:300px; overflow-x:auto")
                ->table();
                $tb["class"] = "igk-central fitw";
                $tb["style"] = "margin:auto; min-width: 500px;";
                $tr = $tb->add("tr");
                $tr->th()->toggleTableCheckbox();
                // HtmlUtils::AddToggleAllCheckboxTh($tr);
                $tr->add("th")->setClass("igk-col-4-2")->Content = __("Name");
                $tr->add("th")->Content = __("Controller");
                $tr->add("th")->setClass("fitw")->Content = __("View");
                $tr->add("th")->addSpace();
                $scheme = igk_sys_srv_uri_scheme() . "://";
                $port = (($p = igk_server()->SERVER_PORT) != 80) ? ":" . $p : "";
                foreach ($db as $k => $v) {
                    $tr = $tb->add("tr");
                    $tr->add("td")->addInput("item[]", "checkbox", $v->clId);
                    $uri = $scheme . $v->clName . "." . $dom . $port;
                    $tr->add("td")->addA($uri)->Content = $v->clName . "." . $dom;
                    $tr->add("td")->Content = $v->clCtrl;
                    $tr->add("td")->Content = $v->clView;
                    $tr->add("td")->addAJXA($ctrl->getUri("dom_edit_domain_ajx&i=" . $v->clId))->addResImg("edit_16x16");
                    $tr->add("td")->addAJXA($ctrl->getUri("dom_drop_domain_ajx&i=" . $v->clId))->setAttribute("domain", $v->clName)->addResImg("drop_16x16");
                }
            } else {
                $tdv->div()->addText($c);
            }
        }

       // $n->ajxa($this->getUri("update-view-list_ajx"))->Content = "Updatelist:";
    }
    private function update_view_list_ajx(){
        $n = igk_create_node("div");
        $this->subdomain_view($n);  
        igk_ajx_replace_node($n, ".subdomain-listview");
    }
}
