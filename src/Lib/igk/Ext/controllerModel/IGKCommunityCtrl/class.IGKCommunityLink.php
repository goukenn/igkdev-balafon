<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCommunityLink.php
// @date: 20220803 13:48:59
// @desc: 
use function igk_resources_gets as __;
use IGK\Controllers\BaseController;
use IGK\Models\Community;
use IGK\Resources\R;
use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Html\HtmlUtils;

/**
* Igkcommunity link.
*/
final class IGKCommunityLink extends ConfigControllerBase
{ 
	/**
	 * Return the configuration page identifier.
	 *
	 * @return string The configuration page name.
	 */
	public function getConfigPage()
	{
		return "community";
	}
	/**
	 * Return the path to the database constant configuration file.
	 *
	 * @return string Absolute path to the database constant file.
	 */
	public function getDbConstantFile(){
		return dirname(__FILE__)."/com.config.db.const";
	}
	/**
	 * Remove a community entry after confirmation.
	 *
	 * @return void
	 */
	public function comm_rm()
	{
		$id = igk_getr("clId");
		if (igk_qr_confirm())
		{
			igk_db_delete(IGK_MYSQL_DATAADAPTER, $this->getCommunityTable() ,array("clId"=>$id));
		}
		else{
			$frame = igk_frame_add_confirm($this,"confirm_rm_frame", $this->getUri("comm_rm&clId=".$id));
			$frame->Form->Div->Content = __("msg.confirmsuppression");
			$frame->Form->Div->addInput("clId", "hidden", $id);
		}
		$this->View();
	}
    /**
    * auto generate doc.
    * @param mixed $n
    * @return
    */
    private function _getCommunityID($n){
		$c = Community::select(["clName"=>$n]); 
		if(igk_count($c) == 1)
			return $c[0]->clId;
		if ( Community::insert(["clName"=>$n], null, true)){
			return igk_db_last_id($this, false);
		}
		return -1;
	}
    /**
    * Comm add comm.
    */
    public function comm_addComm(){
		$tb  = $this->getCommunityTable();
		$e = 0;
		if ($tb == null){
			$e = 1;
		}
			$k = igk_get_robj();
		$comid = empty($k->clName)?-1: $this->_getCommunityID($k->clName);
		if ($comid == -1)
			$e |= 0x2;
		$cc = igk_db_create_row($tb) ?? igk_die("no data found to create object table ".$tb);
		igk_db_load_row($k, $cc);
		$cc->clCommunity_Id = $comid;
		if ($e==0){
			Community::insertIfNotExists(array("clCommunity_Id"=>$comid));
		}
		if (igk_is_ajx_demand()){
			if ($e>0){
				igk_notifyctrl()->addError("There is some Error : ".$e);
			}
			$this->comm_addCommunityFrame_ajx();
			igk_ajx_replace_ctrl_view($this,1);
		}else{
			igk_navto($this->getUri("showConfig"));
		}
		igk_exit(); 
	}
    /**
    * Comm add community frame ajx.
    */
    public function comm_addCommunityFrame_ajx(){
		if (!igk_is_ajx_demand())
			igk_navto(igk_io_baseUri());
		$dv = igk_create_node();
		$frm = $dv->addForm();
		$frm["action"] = $this->getUri("comm_addComm");
		 $frm["igk-ajx-form"]=1;
		igk_notifyctrl()->setNotifyHost($frm->div());
		$ul = $frm->add("ul");
		$li = $ul->add("li");
		$li->addLabel("lb.name" , "clName");
		$li->addInput("clName", "text", "");
		$li = $ul->add("li");
		$li->addLabel("lb.Url" , "clLink");
		$li->addInput("clLink", "text", "");
		$li = $ul->add("li");
		$li->addLabel("lb.ImageKey" , "clImageKey");
		$li->addInput("clImageKey", "text", "");
		$li = $ul->add("li");
		$li->addLabel("lb.AVailable" , "clAvailable");
		$li->addInput("clAvailable", "checkbox", "1");
		$frm->addHLineSeparator();
		$d = $frm->div();
		$d->addInput("btnOk", "submit", R::ngets("btn.add"));
		igk_ajx_notify_dialog(
			R::Gets("title.addCommunity"),
			$dv,
			"default domain-conf-z"
			);
	}
    /**
    * Comm block.
    */
    public function comm_block()
	{
		$t = igk_getr("t");
		$id = igk_getr("clId");
		igk_db_update(IGK_MYSQL_DATAADAPTER, $this->getCommunityTable() ,array("clAvailable"=>$t), array("clId"=>$id));
		$this->View();
	}
    /**
    * Returns Community Table.
    */
    public function getCommunityTable()
	{
		$ctrl = igk_db_sys_ctrl("community");
		if ($ctrl)
			return $ctrl->getDataTableName();
		return null;
	}
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{
		$c = $this->TargetNode;
		if (!$this->IsVisible)
		{
			igk_html_rm($c);
			return $this;
		}
		$table = $this->getCommunityTable();
		$c = $c->clearChilds()->addPanelBox();
		igk_html_add_title($c->div(),"title.configure.community");
		if ($table == null){
			$c->div()->setClass("igk-danger")->Content = R::ngets("msg.community.addrequired");
			return $this;
		}
		$frm = $c->addForm();
		$frm["action"] = $this->getUri("update_community");
		$frm->addHSep();
		igk_html_article($this, "community_desc", $frm->div());
		$frm->addHSep();
		$ul = $frm->add("ul");
		$table = $this->getCommunityTable();
		$tab =  null;
		try{
			$tab = igk_db_get_entries(IGK_MYSQL_DATAADAPTER, $table);
		}
		catch(Exception $ex){
		}
		$addlink = 1;
		$update = 0;
		if (($tab==null) || ($tab->RowCount == 0))
		{
			$ul->add("li")->div()->setClass("igk-danger")->Content =
					($tab ==null)	?
					R::ngets("msg.community.notablefound_1",$table):
					R::ngets("msg.community.addlink");
			$addlink = ($tab!=null);
		}
		else{
			$update = 1;
			foreach($tab->Rows as $k)
			{
				if (!$k)
					continue;
				$li = $ul->li();
				$lb = $li->add("label");
				if(!($n = Community::select(array("clId"=>$k->clCommunity_Id))))
					continue;
				$n[0]->clName;
				$lb["for"] = $n.".clLink";
				$lb->Content = $n;
				$li->addInput("clLink[]", "text", $k->clLink);
				$li->addInput("clIds[]", "hidden", $k->clId);
				if (igk_getv($k, "clAvailable", 1)){
					HtmlUtils::AddImgLnk($li, $this->getUri("comm_block&t=0&clId=".$k->clId), "unblock_16x16");
				}
				else{
					HtmlUtils::AddImgLnk($li, $this->getUri("comm_block&t=1&clId=".$k->clId), "block_16x16");
				}
				HtmlUtils::AddImgLnk($li, $this->getUri("comm_rm&clId=".$k->clId), "drop");
			}
		}
		if ($addlink){
			$ul = $frm->div();
			HtmlUtils::AddImgLnk($ul, igk_js_post_frame($this->getUri("comm_addCommunityFrame_ajx")), "add_16x16");
		}
		if ($update){
			 $frm->AddHSep();
			 $frm->addBtn("btn_send", R::ngets("btn.update"));
		} 
		return $this;
	}
    /**
    * Updates community.
    */
    public function update_community()
	{
		$table = $this->getCommunityTable();
		if ($table==null) return;
		$d = igk_get_robj();
		if (isset($d->clIds))
		{
				$adapt = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, false);
				if($adapt)
				{
					$adapt->connect(null);
					 for($i =0; $i< count($d->clIds); $i++)
						{
							$adapt->update($table, array("clLink"=>$d->clLink[$i]), array("clId"=>$d->clIds[$i]));
						}
					$adapt->close();
					igk_notifyctrl()->addMsgr("msg.CommunityLinkUpdated");
				}
		}
		else{
					igk_notifyctrl()->addMsgr("msg.CommunityLinkUpdated");
		}
		$this->View();
	}
    /**
    * Adds Google Plus.
    * @param mixed $target
    */
    public function addGooglePlus($target){
		$lnk = $this->App->Doc->addLink("googleplus:uri");
		$lnk["rel"] = "canonical";
		$lnk["href"] = $this->App->Configs->community_googleplus_uri;
		$src = $this->App->Doc->addScript("https://apis.google.com/js/plusone.js");
		$gdiv = $target->add("span");
		$gdiv->add("g:plusone")->addNothing();
	} 
} 