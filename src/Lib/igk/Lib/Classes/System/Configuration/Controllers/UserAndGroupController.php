<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UserAndGroupController.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\Helper\NotifyHelper;
use IGK\Helper\StringUtility;
use IGK\Helper\SysUtils;
use IGK\Models\Authorizations;
use IGK\Models\Groups;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\Resources\R;
use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Html\HtmlUtils;
use IGK\System\Http\WebResponse;
use IGK\System\WinUI\Menus\MenuItem;
use IGK\System\WinUI\Views;
use IGKEvents;

use function igk_resources_gets as __;

/**
 * use and group control
 * @package IGK\System\Configuration\Controllers
 */
class UserAndGroupController extends ConfigControllerBase{
    public function getName(){
        return IGK_USER_AND_GROUP_CTRL;
    }
    public function getIsConfigPageAvailable(){
        return true;
    }
    public function initConfigMenu(){
        return [
            new MenuItem(
                "usergroups",
                __("User's group"),
                $this->getUri("showConfig"),50,null,
                "administration"
            )
        ];
    }
    public function View():BaseController{
        $t = $this->getTargetNode();
        $t->clearChilds();
        $t->panelbox()->host(static::class."::Presentation", $this);
        return $this;
    }
    public static function Presentation($t, $ctrl){
        $t->h2()->Content = __("User's group");
        $t->hr();
        $t->blockquote()->article($ctrl, "group.description");

        $frm=$t->form();
        $table=$frm->addDiv()->setClass("overflow-x-a")->addTable();
        $table["class"]="igk-table";
        $tr=$table->tr();
        $tr->th()->setAttributes(array("style"=>"width:16px;"))->addSpace();
        $tr->th()->Content= __("Name");
        $tr->th()->setAttributes(array("class"=>"fitw"))->Content = __("Users");
        $tr->th()->setAttributes(array("style"=>"width:16px;"))->Content=IGK_HTML_SPACE;
        $tr->th()->setAttributes(array("style"=>"width:16px;"))->Content=IGK_HTML_SPACE;
        $e= \IGK\Models\Groups::select_all();
        if($e) 
        foreach($e as $v){
            $tr=$table->addTr();
            $tr->addTd()->addInput("r", "checkbox", $v->clId);
            $tr->addTd()->Content=$v->clName;
            $tr->addTd()->Content="0";
            $tr->td()->ajxa($ctrl->getUri("group_view_user&clId=".$v->clId))->Content =
            igk_svg_use("user");            
            $tr->td()->ajxa($ctrl->getUri("group_dropgroup_ajx&clId=".$v->clId))->Content = igk_svg_use("drop");
        }
         
        $uri = $ctrl->getUri("group_add_group_ajx");
        $frm->actionbar(function($a)use($uri){
            $a->ajxa($uri)->setClass("igk-btn")->Content = igk_svg_use("add");
        }); 
        
    }

     ///<summary>Represente addAuthToGroup function</summary>
    ///<param name="groupname"></param>
    ///<param name="n"></param>
    /**
    * Represente addAuthToGroup function
    * @param  $groupname
    * @param  $n
    */
    public function addAuthToGroup($groupname, $n){
        Groups::grantAuthorization($groupname, $n);
        $ad=igk_get_data_adapter($this);
        if(!$ad->connect()){
            return false;
        }
        $gid=igk_db_table_select_where(IGK_TB_GROUPS, array(IGK_FD_NAME=>$groupname), $this)->getRowAtIndex(0);
        if(!$gid)
            return false;
        $auth=igk_db_table_select_where(IGK_TB_AUTHORISATIONS, array(IGK_FD_NAME=>$n), $this)->getRowAtIndex(0);
        if(!$auth)
            return false;
        $b=array("clGroup_Id"=>$gid->clId, "clAuth_Id"=>$auth->clId);
        $h=igk_db_table_select_where(IGK_TB_GROUPAUTHS, $b, $this);
        $s=0;
        if(!$h || ($h->RowCount == 0)){
            $obj=igk_db_create_row(IGK_TB_GROUPAUTHS);
            $obj->clGroup_Id=$gid->clId;
            $obj->clAuth_Id=$auth->clId;
            $obj->clGrant=1;
            $s=igk_db_insert_if_not_exists($this, IGK_TB_GROUPAUTHS, $obj);
        }
        $ad->close();
        return $s;
    }
    ///<summary>Represente addUserToGroup function</summary>
    ///<param name="groupname"></param>
    ///<param name="u"></param>
    /**
    * Represente addUserToGroup function
    * @param  $groupname
    * @param  $u
    */
    public function addUserToGroup($groupname, $u){
        if(empty($groupname) || !$u)
            return false;
        $ad=igk_get_data_adapter($this);
        if(!$ad->connect()){
            return false;
        }
        $gid=igk_db_table_select_where(IGK_TB_GROUPS, array(IGK_FD_NAME=>$groupname), $this)->getRowAtIndex(0);
        if($gid == null){
            $gid=igk_db_create_row(IGK_TB_GROUPS);
            $gid->clName=$groupname;
            if(igk_db_insert($this, IGK_TB_GROUPS, $gid)){
                $gid->clId=$ad->getlastId();
            }
            else{
                $ad->close();
                return false;
            }
        }
        $b=array(IGK_FD_USER_ID=>$u->clId, IGK_FD_GROUP_ID=>$gid->clId);
        $h=igk_db_table_select_where(IGK_TB_USERGROUPS, $b, $this);
        $s=0;
        if(!$h || ($h->RowCount == 0)){
            $s=igk_db_insert_if_not_exists($this, IGK_TB_USERGROUPS, $b);
        }
        $ad->close();
        return $s;
    }
     ///<summary>return an array of authorisation that this user support</summary>
    /**
    * return an array of authorisation that this user support
    */
    public function getUserAuths($u){
        igk_die( __METHOD__." not implement");
        // $ad=igk_get_data_adapter($this);
        // if(!$ad->connect())
        //     return null;
        // $t=array();
        // $c= Authorizations::select_row igk_db_table_select_where(IGK_TB_AUTHORISATIONS, null, $this);
        // if($c && $c->Success){
        //     foreach($c->Rows as $k=>$v){
        //         if($u->IsAuthorize($v->clName)){
        //             $t[$v->clId]=$v->clName;
        //         }
        //     }
        // }
        // $ad->close();
        // return $t;
    }
    ///<summary>return an array of groups that this user is member of</summary>
    /**
    * return an array of groups that this user is member of
    */
    public function getUserGroups($u){
        $ad=igk_get_data_adapter($this);
        if(!$ad->connect())
            return;
        $gid=igk_db_table_select_where(IGK_TB_USERGROUPS, array(IGK_FD_USER_ID=>$u->clId), $this);
        $t=array();
        if($gid && $gid->Success){
            foreach($gid->Rows as $k=>$v){
                $g=$ad->select(IGK_TB_GROUPS, array(IGK_FD_ID=>$v->clGroup_Id))->getRowAtIndex(0);
                $t[$v->clGroup_Id]=$g->clName;
            }
        }
        $ad->close();
        return $t;
    }
    ///<summary>add group</summary>
    ///<param name="n" default="null"></param>
    /**
    * add group
    * @param  $n the default value is null
    */
    public function group_add($n=null){
        $conditions = null;
        if($n == null){
            $field = $this->getAddGroupFields();
            $v = \IGK\System\Html\Forms\FormValidation::ValidateFormFields($field);            
            if($v){
                $conditions = array(IGK_FD_NAME=>$v->clName);
            }             
        } else {
            $conditions = array(IGK_FD_NAME=>$n);
        }
        if ($conditions){
            $e = Groups::select_row($conditions); 
            if(!$e){                   
                Groups::insert($v);                    
            }
            $this->View();
        }
 
    }
    private function getAddGroupFields(){
        $fields = [
            IGK_FD_NAME=>["type"=>"text", "pattern"=>"^[_a-zA-Z][a-zA-Z0-9\/_]*$","required"=>1, "maxlength"=>30]
        ];
        return $fields;
    }
    ///<summary>Represente group_add_group_ajx function</summary>
    /**
    * Represente group_add_group_ajx function
    */
    public function group_add_group_ajx(){
        $fields = $this->getAddGroupFields();
        // $frame=igk_html_frame($this, "group_add_new_frame");
        // $frame->title= __("title.AddNewGroup");
        $div= igk_create_node("div"); // $frame->BoxContent;
        $frm=$div->form();
        $frm["action"]=$this->getUri("group_add");        
        $frm->fields($fields); //addSLabelInput(IGK_FD_NAME, IGK_STR_EMPTY);
        $frm->addHSep();
        $frm->addInput("btn.add", "submit",__("btn.Add"));
        // $frame->RenderAJX();
        igk_ajx_panel_dialog(__("add group"), $div);
    }
    ///<summary>Represente group_add_userto_group function</summary>
    /**
    * Represente group_add_userto_group function
    */
    public function group_add_userto_group(){
        $obj=igk_get_robj();
        if(igk_db_insert_if_not_exists($this, IGK_TB_USERGROUPS, array(
            IGK_FD_USER_ID=>$obj->clUser_Id,
            IGK_FD_GROUP_ID=>$obj->clGroup_Id
        ))){
            igk_notifyctrl()->addMsgr("msg.group.association.success");
        }
        else{
            igk_notifyctrl()->addErrorr("e.group.association.failed");
        }
        $this->View();
    }
    ///<summary>Represente group_default_view function</summary>
    /**
    * Represente group_default_view function
    */
    public function group_default_view(){
        $this->CurrentView=null;
        $this->View();
    }
    ///<summary>Represente group_dropgroup_ajx function</summary>
    /**
    * drop group
    */
    public function group_dropgroup_ajx(){
        $id=igk_getr("clId");
        if(igk_qr_confirm() && igk_server()->method("POST")){
            // try remove group
            if($id){
                if (Groups::delete($id)){
                    igk_ajx_toast(__("Group removed"), "igk-success");
                }else{
                    igk_ajx_toast(__("Group not removed"), "igk-danger");
                }
            }  
            $this->View();
            igk_ajx_replace_node($this->getTargetNode(), "#igk-cnf-content #cb");
            igk_ajx_panel_dialog_close();
            igk_ajx_replace_uri($this->getUri("showConfig"));
            SysUtils::exitOnAJX();         
        }
       
        $d = igk_create_node('div');
        $frame= $d->form();
        $frame["action"] = $this->getUri("group_dropgroup_ajx&clId=".$id);
        $frame["igk-ajx-form"] = 1;
        $frame->p()->Content = R::ngets("msg.confirmsuppression");
        $frame->input("clId", "hidden", $id);
        $frame->confirm();
        $frame->actionbar(Views::ActionBarConfirmDialog(), ["lb.submit"=>__("Delete")])->setClass("dispflex");
        igk_ajx_panel_dialog(__("Drop group"), $d );
       
    }
    ///<summary>Represente group_remove_user function</summary>
    /**
    * Represente group_remove_user function
    */
    public function group_remove_user(){
        igk_db_delete($this, IGK_TB_USERGROUPS, igk_getr("clId"));
        $this->View();
    }
    ///<summary>Represente group_view_auth function</summary>
    /**
    * Represente group_view_auth function
    */
    public function group_view_auth(){
        $this->CurrentView="viewauth";
        $this->View();
    }
    ///<summary>view user in groups</summary>
    /**
    * view user in groups
    */
    public function group_view_user(){
        $id = igk_getr("clId");
        if ($id){
            $this->CurrentView="viewusers";
            $this->selectedGroup= $id; 
            $d = igk_create_node("div"); 
            $g = Usergroups::prepare()->join([
                    Groups::table()=>[
                        Groups::column("clId")."=".Usergroups::column("clGroup_Id")
                    ]
                ])->join([
                Users::table()=>[
                    Users::column("clId")."=".Usergroups::column("clUser_Id")
                ]])
                ->conditions([Usergroups::column("clGroup_Id")=>$id])
                ->distinct(true)
                ->orderBy([
                    "clFirstName|Asc"
                ])
                ->query();             
            if($g && ($g->RowCount > 0)){                  
                $d->ul()->loop($g->getRows())->host(function($c, $i)use($id){
                    $li = $c->li();
                    $li->Content = StringUtility::NameDisplay($i->clFirstName, $i->clLastName);
                    $li->span()->ajxa($this->getUri("remove_user_from_group&id=".$id."&uid=".$i->clUser_Id."&gid=".$i->clGroup_Id))->Content = igk_svg_use("drop");
                });
                igk_ajx_panel_dialog(__("User in group - {0}", $g->getRowAtIndex(0)->clName), $d);
            }else {
                igk_ajx_toast(__("no user found in this group"), "igk-danger");
            }
        }
        $this->View(); 
        SysUtils::exitOnAJX(); 
    }
    public function remove_user_from_group(){
        extract((array)igk_get_robj('uid|gid|id'));
        NotifyHelper::Notify(
            "usergroup",
            Usergroups::delete(["clGroup_Id"=>$gid, "clUser_Id"=>$uid]),
            __("group removed"),
            __("faild to remove")
        );
        igk_ajx_panel_dialog_close();
        $_REQUEST["clId"] = $id;
        $this->group_view_user();
        SysUtils::exitOnAJX(); 

    }
    ///<summary>Represente registerHook function</summary>
    /**
    * Represente registerHook function
    */
    protected function registerHook(){
        igk_reg_hook(IGKEvents::HOOK_DB_DATA_ENTRY, function($hook){
            $db=$hook->args[0];
            $n=$hook->args[1];
            if($n == $this->getDataTableName()){
                $db->insert($n, array(IGK_FD_NAME=>"user"));
                $db->insert($n, array(IGK_FD_NAME=>"administrator"));
                $db->insert($n, array(IGK_FD_NAME=>"client"));
                $db->insert($n, array(IGK_FD_NAME=>"vendor"));
                $db->insert($n, array(IGK_FD_NAME=>"robot"));
            }
        });
    }
}

