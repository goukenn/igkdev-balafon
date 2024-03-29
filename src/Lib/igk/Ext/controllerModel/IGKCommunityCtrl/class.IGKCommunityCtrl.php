<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCommunityCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//controller code class declaration
//file is a part of the controller tab list
///<summary>used to manage comomunity site</summary>

use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\Models\Community;
use IGK\System\Models\IModelDefinitionInfo;

abstract class IGKCommunityCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getName()
	{
		return get_class($this);
	}
	protected function initComplete($context = null)
	{
		parent::initComplete();
		igk_db_reg_sys_ctrl("community", $this);
		//only one instance is allowed.
		igk_reg_hook("sys://events/community", "igk_community_init_node_callback");
	}
	public function dropController()
	{
		parent::dropController();
		igk_notification_unreg_event("sys://events/community", "igk_community_init_node_callback");
		igk_db_unreg_sys_ctrl("community");
	}
	 
	public function getCanAddChild()
	{
		return false;
	}
	public static function CanDbEditDataType()
	{
		return false;
	}
	public static function CanDbChangeDataSchema()
	{
		return false;
	}

	public function getUseDataSchema():bool
	{
		return false;
	}

	public function getCanEditDataTableInfo()
	{
		return false;
	}
	public function getDataTableName(): ?string
	{
		return "%prefix%site_community";
	}
	public function getDataTableInfo(): ?IModelDefinitionInfo
	{
		return Activator::CreateNewInstance(DbModelDefinitionInfo::class, array(
			new DbColumnInfo(array(IGK_FD_NAME => IGK_FD_ID, IGK_FD_TYPE => "Int", "clAutoIncrement" => true, IGK_FD_TYPELEN => 10, "clIsUnique" => true, "clIsPrimary" => true)),
			new DbColumnInfo(array(IGK_FD_NAME => "clCommunity_Id", IGK_FD_TYPE => "Int", "clIsUnique" => true, "clLinkType" => "tbigk_community")),
			new DbColumnInfo(array(IGK_FD_NAME => "clLink", IGK_FD_TYPE => "Text", "clDescription" => "Url to community")),
			new DbColumnInfo(array(IGK_FD_NAME => "clImageKey", IGK_FD_TYPE => "VarChar", IGK_FD_TYPELEN => 30)),
			new DbColumnInfo(array(IGK_FD_NAME => "clAvailable", IGK_FD_TYPE => "Int", "clNotNull" => 1))
		));
	}
	public static function initDb($force = false, bool $clean=false)
	{
		igk_set_env("sys://db/constraint_key", "igk_com");
		if (igk_is_conf_connected())
			self::ctrl()->initDbFromFunctions();
	}


	//@@@ parent view control
	public function View():BaseController
	{	
		// do nothing
		return $this;
	}
	public function loadCommunityNode($n)
	{
		$e = Community::select_all();
		if ($e) {
			$ul = $n->add("ul");
			$ul->loop($e)->host(function ($i, $t) {
				$t->li()->Content = $i->clName;
			});
		}
	}
}
