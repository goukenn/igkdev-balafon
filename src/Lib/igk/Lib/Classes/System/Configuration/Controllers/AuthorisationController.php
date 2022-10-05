<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthorisationController.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\Helper\NotifyHelper;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Users;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlComponents;
use IGK\System\Html\HtmlUtils;
use IGK\System\WinUI\Menus\MenuItem;
use IGK\System\WinUI\Views;

use function igk_resources_gets as __;

class AuthorisationController extends ConfigControllerBase{
    public function getName(){
        return  IGK_AUTH_CTRL;
    }
    public function getIsAvailable(){
        return true;
    }
    public function getIsVisible():bool
    {
        return true;
    }
    public function getIsConfigPageAvailable()
    {
        return true;
    }
     ///<summary>Represente getConfigCategory function</summary>
    /**
    * Represente getConfigCategory function
    */
    public function getConfigCategory(){
        return "administration";
    }
    ///<summary>Represente getConfigPage function</summary>
    /**
    * Represente getConfigPage function
    */
    public function getConfigPage(){
        return "auth";
    }
    public function initConfigMenu()
    { 
        return array(
            new MenuItem("auth",
            "menu-1",
            $this->getUri("showConfig"),
            80,
            "",
            "administration")
        );
    }
    
   
    ///<summary>Represente _auth_options function</summary>
    ///<param name="frm"></param>
    /**
    * Represente _auth_options function
    * @param  $frm
    */
    private function _auth_options($frm){
        $frm->span()->ajxa($this->getUri("auth_add_authorisation_ajx"))->Content = igk_svg_use("add");
        // IGKHtmlUtils::AddImgLnk($frm->addspan(), igk_js_post_frame($this->getUri("auth_add_authorisation_ajx")), "add_16x16");
    }
    ///<summary>Represente _isAuth function</summary>
    ///<param name="q"></param>
    ///<param name="t"></param>
    /**
    * Represente _isAuth function
    * @param  $q
    * @param  $t
    */
    private function _isAuth($q, $t){
        foreach($q->Rows as $k=>$v){
            if($v->clGroup_Id == $t)
                return $t;
        }
        return false;
    }
    // public static function ModelViewLimit($target, $model, callable $callback, $conditions=null, $options=null, $key = "page") {
    //     $options = $options ?? [];
    //     $c = $model::count($conditions, $options);
    //     $limit = null;
    //     if ($c>0){
    //         $blimit = igk_getv($options, "Limit", PageLayout::ItemLimits());
            
    //         if ($c < $blimit){
    //             $pan = new Pagination();
    //             $options["Limit"] = $pan->getLimit();
    //         }

    //         if ($r= $model::select_all($conditions, $options)){
    //             foreach($r as $v){
    //                 $callback($target, $v);
    //             }
    //         }
    //     }
    //     return $limit;
    // }
    ///<summary>Represente auth function</summary>
    /**
    * Represente auth function
    */
    public function auth(){ 
        $r = igk_create_notagnode(); 

        $r->balafonJS()->Content = "igk.winui.ajx.lnk.host = igk.dom.body().qselect('.igk-tabcontrol .igk-tabcontent').first();";

        $d = $r->div(); 
        $frm= $d->div()->setClass("auth-page")->form();
        $frm->notifyhost("auth");
        $this->_auth_options($frm);
        $table=$frm->div()->setClass("overflow-x-a")->table();
        $table["class"]="igk-table igk-table-hover";
        
        $tr=$table->addTr();
        $tr->th()->addSpace();
        $tr->th()->setClass("fitw")->Content=R::ngets("lb.clName");
        $tr->th()->addSpace();
        $tr->th()->addSpace();
        
        $limit = Views::ModelViewLimit($table, Authorizations::class, function($table, $v){           
            $tr=$table->addTr();
            $tr->td()->addInput("clAuths[]", "checkbox");
            $tr->td()->Content=$v->clName;
            HtmlUtils::AddImgLnk($tr->td(), igk_js_post_frame($this->getUri("auth_edit_frame_ajx&clId=".$v->clId)), "edit_16x16");
            HtmlUtils::AddImgLnk($tr->td(), igk_js_post_frame($this->getUri("auth_delete_authorisation_ajx&clId=".$v->clId)), "drop_16x16");
        });

      
        if ($limit){
            $d->add($limit->list(igk_is_ajx_demand()));
        } 
        // $r = null;
        // if ($c > PageLayout::ItemLimits()){
            
        // }
        // $r= Authorizations::select_all(); 
        // $d->div()->Content = "Total Item : ".$c . ":=". PageLayout::ItemLimits();
        // if($r){
        //     foreach($r as $v){
        //         $tr=$table->addTr();
        //         $tr->addTd()->addInput("clAuths[]", "checkbox");
        //         $tr->addTd()->Content=$v->clName;
        //         HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("auth_edit_frame_ajx&clId=".$v->clId)), "edit_16x16");
        //         HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("auth_delete_authorisation_ajx&clId=".$v->clId)), "drop_16x16");
        //     }
        // }
     
        return $r;
    }
    ///<summary>Represente auth_add_authorisation_ajx function</summary>
    /**
    * Represente auth_add_authorisation_ajx function
    */
    public function auth_add_authorisation_ajx(){
        if(igk_qr_confirm()){
            $o=igk_get_robj();
            NotifyHelper::Notify(
                "auth",
                Authorizations::insert($o),
                __("Auth added"),
                __("Auth not added")
            ); 
            $this->View();
            return igk_ajx_replace_node($this->getTargetNode());
        }
        $d = igk_create_node("div");
        $frm=$d->addForm();
        $frm["action"]=$this->getUri(__FUNCTION__);
        $frm["igk-ajx-form"] = 1;
        $m=$frm->div();
        $m["class"]="igk-form-group";
        $m->addSLabelInput(IGK_FD_NAME);
        $frm->hsep();
        $frm->confirm();
        $frm->submit("submit");
        igk_ajx_panel_dialog(__("add auth"), $d);
            // $frame=igk_html_frame($this, __FUNCTION__);
            // $frame->Title=R::ngets("title.edit_authorisation_1");
            // $d=$frame->BoxContent->div();
            // $frm=$d->addForm();
            // $frm["action"]=$this->getUri(__FUNCTION__);
            // $d=$frm->div();
            // $d["class"]="igk-form-group";
            // $d->addSLabelInput(IGK_FD_NAME);
            // $frm->addHSep();
            // $frm->addInput("confirm", "hidden", "1");
            // $frm->addInput("btn.confirm", "submit");
            // $frame->RenderAJX();
      
    }
    ///<summary>Represente auth_add_group_ajx function</summary>
    /**
    * Represente auth_add_group_ajx function
    */
    public function auth_add_group_ajx(){
        $id=igk_getr("clId");
        $row=igk_db_table_select_row(IGK_TB_AUTHORISATIONS, $id);
        if($row == null){
            igk_navtocurrent();
            igk_exit();
        }
        if(igk_qr_confirm()){
            igk_frame_close(__FUNCTION__);
            $gp=igk_getr("clGroups");
            igk_db_delete($this, $this->getDataTableName(), array(IGK_FD_AUTH_ID=>$id));
            if($gp){
                foreach($gp as $k=>$v){
                    igk_db_insert($this, $this->getDataTableName(), array(IGK_FD_AUTH_ID=>$id, IGK_FD_GROUP_ID=>$v));
                }
                igk_db_reload_index($this, $this->getDataTableName());
            }
            $this->View();
            igk_navtocurrent();
        }
        else{
            $frame=igk_html_frame($this, __FUNCTION__);
            $frame->Title=R::ngets("title.add_group_1", $row->clName);
            $frame->BoxContent->ClearChilds();
            $d=$frame->BoxContent->div();
            $frm=$d->addForm();
            $frm["action"]=$this->getUri(__FUNCTION__);
            $d=$frm->div();
            $d["class"]="igk-form-group";
            $table=$d->addTable();
            $table ["class"]="igk-table-striped";
            $table->setHeader(IGK_STR_EMPTY, R::ngets("lb.clGgroupName"), IGK_STR_EMPTY);
            $r=igk_db_table_select_where(IGK_TB_GROUPS);
            foreach($r->Rows as $k=>$v){
                $tr=$table->addRow();
                $tr->addTd()->addInput("clGroups[]", "checkbox", $v->clId);
                $tr->addTd()->Content=$v->clName;
            }
            $frm->addHSep();
            $frm->addInput("confirm", "hidden", "1");
            $frm->addInput("clId", "hidden", $id);
            $frm->addInput("btn.confirm", "submit");
            $frame->RenderAJX();
        }
    }
    ///<summary>check user auth </summary>
    /**
    * check user auth 
    */
    public function auth_check_auth(){
        $id=igk_getr("clUser");
        $t='danger';
        $auth=igk_getr("clAuth");
        $v_r = false;
        if ($id && $auth){
            $row= Users::select_row($id);
            $v_r=igk_sys_isuser_authorize($row, $auth);
            if($v_r){
                $t='success';
            }
        }
        $b=igk_notifyctrl()->getNotification("notify:checkauth", true);
        $d=igk_create_node();
        $d->addObData($v_r);
        $d->div()->Content="Autorisation : ".$v_r;
        $b->addMsg($d, $t);
        
        if (igk_is_ajx_demand()){
            igk_ajx_toast($t);
        }else{
            
        }
        //igk_navto($this->getUri('showConfig').'#'.__FUNCTION__);
    }
    ///<summary>Represente auth_delete_authorisation_ajx function</summary>
    /**
    * Represente auth_delete_authorisation_ajx function
    */
    public function auth_delete_authorisation_ajx(){
        $id=igk_getr("clId");
        $row=igk_db_table_select_row(IGK_TB_AUTHORISATIONS, $id);
        if(igk_qr_confirm() && $row){
            igk_db_delete($this, IGK_TB_AUTHORISATIONS, array("clId"=>$id));
            $this->View();
            igk_navtocurrent();
        }
        else{
            $frame=igk_frame_add_confirm($this, __FUNCTION__, $this->getUri(__FUNCTION__));
            $frame->Form->addInput("clId", "hidden", $id);
            $frame->Form->Div->Content=R::ngets("q.confirm_auth_suppression");
            $frame->RenderAJX();
        }
    }
    ///<summary>Represente auth_edit_frame_ajx function</summary>
    /**
    * Represente auth_edit_frame_ajx function
    */
    public function auth_edit_frame_ajx(){
        $id=igk_getr("clId");
        $row=igk_db_table_select_row(IGK_TB_AUTHORISATIONS, $id);
        if($row == null){
            igk_navtocurrent();
        }
        if(igk_qr_confirm()){
            igk_frame_close(__FUNCTION__);
            $tbname=$this->getDataTableName();
            $gp=igk_getr("clGroups");
            igk_db_delete($this, $tbname, array(IGK_FD_AUTH_ID=>$id));
            if($gp){
                foreach($gp as $k=>$v){
                    igk_db_insert($this, $tbname, array(IGK_FD_AUTH_ID=>$id, IGK_FD_GROUP_ID=>$v, "clGrant"=>1));
                }
                igk_db_reload_index($this, $tbname);
            }
            $this->View();
            igk_navtocurrent();
        }
        else{
            $frame=igk_html_frame($this, __FUNCTION__);
            $frame->Title=R::ngets("title.edit_authorisation_1", $row->clName);
            $frame->BoxContent->ClearChilds();
            $d=$frame->BoxContent->div();
            $frm=$d->addForm();
            $frm["action"]=$this->getUri(__FUNCTION__);
            $d=$frm->div();
            $d["class"]="igk-form-group";
            $table=$d->addTable();
            $table["class"]="igk-table igk-table-hover";
            $r=igk_db_table_select_where(IGK_TB_GROUPS, null);
            if($r){
                $g=igk_db_table_select_where($this->getDataTableName(), array(IGK_FD_AUTH_ID=>$id));
                $groupindex=array();
                foreach($g->Rows as $k=>$v){
                    $groupindex[$v->clGroup_Id]=$v;
                }
                $tr=$table->addTr();
                $tr->add("th")->addSpace();
                $tr->add("th")->Content=R::ngets("lb.Group");
                $tr->add("th")->addSpace();
                foreach($r->Rows as $k=>$v){
                    $tr=$table->addTr();
                    $tr->addTd()->addInput("clGroups[]", "checkbox", $v->clId)->setAttribute("checked", isset($groupindex[$v->clId]));
                    $tr->addTd()->Content=$v->clName;
                    // IGKHtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("auth_remove_group_ajx&clId=".$id."&clGroupId=".$v->clId)), "drop_16x16");
                    $tr->td()->ajxa($this->getUri("auth_remove_group_ajx&clId=".$id."&clGroupId=".$v->clId))->Content = igk_svg_use("drop"); 
                }
            }
            $div=$frm->div();
            $frm->addHSep();
            $frm->addInput("clId", "hidden", $id);
            $frm->addInput("confirm", "hidden", 1);
            $frm->addInput("btn.confim", "submit", R::ngets("btn.confirm"));
            $frame->RenderAJX();
        }
    }
    ///<summary>Represente auth_remove_group_ajx function</summary>
    /**
    * Represente auth_remove_group_ajx function
    */
    public function auth_remove_group_ajx(){
        if(igk_qr_confirm()){
            igk_frame_close(__FUNCTION__);
            $h=igk_db_delete($this, $this->getDataTableName(), array(
                "clId"=>igk_getr("clId"),
                IGK_FD_GROUP_ID=>igk_getr(IGK_FD_GROUP_ID)
            ));
            if($h)
                igk_notifyctrl()->addMsgr("msg.group.removed");
            else{
                igk_notifyctrl()->addErrorr("msg.group.not_removed");
            }
            $this->View();
            igk_navtocurrent();
        }
        else{
            $id = "confirm_dialog";
            $frame=igk_frame_add_confirm($this, __FUNCTION__, $this->getUri(__FUNCTION__));
            $frame->Form->addInput("clId", "hidden", $id);
            $frame->Form->Div->Content=R::ngets("q.confirm_remove_group_auth");
            $frame->RenderAJX();
        }
    }
    ///<summary>Represente checkauth function</summary>
    /**
    * Represente checkauth function
    */
    public function checkauth(){
        $d=igk_create_node("div");
        $row=$d->addRow();
        $frm=$row->addCol()->addForm();
        $frm["action"]=$this->getUri("auth_check_auth");
        $frm["class"]="dispb";
        $frm["igk-ajx-form"] = 1;
        igk_notify_sethost($frm->div(), "notify:checkauth");
        $ul=$frm->add("ul");
        $li=$ul->addLi();
        $li->addLabel()->Content=R::ngets("lb.users");
        $select=$li->addSelect("clUser");

       //  Users::select_fetch();


        $r= Users::select_all(); // igk_db_table_select_where(IGK_TB_USERS, null, $this);
        $select->add("option");
        if($r) foreach($r as  $v){
            if($v->clLastName == "IGKSystem")
                continue;
            $opt=$select->add("option");
            $opt["value"]=$v->clId;
            $fn=trim(igk_user_fullname($v));
            $opt->Content=(empty($fn) ? "NoName://[".$v->clLogin."]": $fn);
        }
        $li=$ul->addLi();
        $li->addLabel()->Content=__("Autorisation");
        $li->div()->addInput("clAuth", "text", "")->setStyle("width: 100%");
        $frm->addInput("btn.input", "submit", __("Check autorisation"));
        return $d;
    }
   
     
    
    ///<summary>Represente getDataTableName function</summary>
    /**
    * Represente getDataTableName function
    */
    public function getDataTableName(): ?string{
        return Groupauthorizations::table(); 
    }
    ///<summary>Represente IsUserAuthorized function</summary>
    ///<param name="s"></param>
    ///<param name="actionName"></param>
    ///<param name="authTable" default="IGK_TB_AUTHORISATIONS"></param>
    ///<param name="userGroupTable" default="IGK_TB_GROUPAUTHS"></param>
    /**
    * Represente IsUserAuthorized function
    * @param  $s
    * @param  $actionName
    * @param  $authTable the default value is IGK_TB_AUTHORISATIONS
    * @param  $userGroupTable the default value is IGK_TB_GROUPAUTHS
    */
    public function IsUserAuthorized($s, $actionName, $authTable=IGK_TB_AUTHORISATIONS, $userGroupTable=IGK_TB_GROUPAUTHS){
        if(($s == null) || empty($actionName))
            return false;
        if($s->clLevel == -1)
            return true;
        return igk_db_is_user_authorized($s, $actionName, $authTable, $userGroupTable);
    }
   
    ///<summary>Represente View function</summary>
    /**
    * Represente View function
    */
    public function View():BaseController{ 
        $t = $this->getTargetNode();
        if(!$this->getIsVisible()){ 
            $t->remove();
            return $this;
        } 		
		$cnf = $this->getConfigNode();	
        $cnf->add($t);      
        $t->ClearChilds();
        $box=$this->viewConfig($t, "title.manageauth", ".help/auth.managerdesc");
        $box->notifyhost("auth");
        $box->host(function($c){
            $buri=igk_register_temp_uri(__CLASS__);
            $tab=$c->component($this, HtmlComponents::AJXTabControl , "auth-tab");
            $tab->addTabPage(__("Authorization"), $buri."/auth");
            $tab->addTabPage(__("CheckAuth"), $buri."/checkauth"); 
            $tab->select(HtmlComponents::GetParam($this, "auth-tab", 0));
        });
        return $this;
		// return;
        // $row=$box->addRow();
        // $frm=$row->addCol()->addForm();
        // $frm["action"]=$this->getUri("auth_check_auth");
        // $frm["class"]="dispb";
        // igk_notify_sethost($frm->div(), "notify:checkauth");
        // $ul=$frm->add("ul");
        // $li=$ul->addLi();
        // $li->addLabel()->Content=R::ngets("lb.users");
        // $select=$li->addSelect("clUser");
        // $r=igk_db_table_select_where(IGK_TB_USERS, null, $this);
        // $select->add("option");
        // if($r) foreach($r->Rows as $k=>$v){
        //     if($v->clLastName == "IGKSystem")
        //         continue;
        //     $opt=$select->add("option");
        //     $opt["value"]=$v->clId;
        //     $fn=trim(igk_user_fullname($v));
        //     $opt->Content=(empty($fn) ? "NoName://[".$v->clLogin."]": $fn);
        // }
        // $li=$ul->addLi();
        // $li->addLabel()->Content=__("clAuth");
        // $li->div()->addInput("clAuth", "text", "")->setStyle("width: 100%");
        // $frm->addInput("btn.input", "submit", R::ngets("btn.CheckAuth"));
        // $frm=$row->addCol("igk-col")->addForm();
        // igk_notify_sethost($frm->div());
        // $this->_auth_options($frm);
        // $table=$frm->div()->setClass("overflow-x-a")->addTable();
        // $table["class"]="igk-table igk-table-hover";
        // $r=igk_db_table_select_where(IGK_TB_AUTHORISATIONS, null, $this);
        // $tr=$table->addTr();
        // $tr->add("th")->addSpace();
        // $tr->add("th")->setClass("fitw")->Content=R::ngets("lb.clName");
        // $tr->add("th")->addSpace();
        // $tr->add("th")->addSpace();
        // if($r){
        //     foreach($r->Rows as $k=>$v){
        //         $tr=$table->addTr();
        //         $tr->addTd()->addInput("clAuths[]", "checkbox");
        //         $tr->addTd()->Content=$v->clName;
        //         HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("auth_edit_frame_ajx&clId=".$v->clId)), "edit_16x16");
        //         HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("auth_delete_authorisation_ajx&clId=".$v->clId)), "drop_16x16");
        //     }
        // }
        // $this->_auth_options($frm); 
    }
}