<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCommunityCtrl.php
// @date: 20220803 13:48:59
// @desc: 
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\Models\Community;
use IGK\System\Models\IModelDefinitionInfo;

/**
* Igkcommunity ctrl.
*/
abstract class IGKCommunityCtrl extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string
	{
		return get_class($this);
	}
    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context = null)
	{
		parent::initComplete();
		igk_db_reg_sys_ctrl("community", $this);
		igk_reg_hook("sys://events/community", "igk_community_init_node_callback");
	}
    /**
    * Drops Controller.
    */
    public function dropController()
	{
		parent::dropController();
		igk_notification_unreg_event("sys://events/community", "igk_community_init_node_callback");
		igk_db_unreg_sys_ctrl("community");
	}
    /**
    * Returns Can Add Child.
    */
    public function getCanAddChild()
	{
		return false;
	}
    /**
    * Returns true if can Db Edit Data Type.
    */
    public static function CanDbEditDataType()
	{
		return false;
	}
    /**
    * Returns true if can Db Change Data Schema.
    */
    public static function CanDbChangeDataSchema()
	{
		return false;
	}
    /**
    * Returns Use Data Schema.
    * @return bool
    */
    public function getUseDataSchema():bool
	{
		return false;
	}
    /**
    * Returns Can Edit Data Table Info.
    */
    public function getCanEditDataTableInfo()
	{
		return false;
	}
    /**
    * Returns Data Table Name.
    * @return ?string
    */
    public function getDataTableName(): ?string
	{
		return "%prefix%site_community";
	}
    /**
    * Returns Data Table Info.
    * @return ?IModelDefinitionInfo
    */
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
    /**
    * Initializes Db.
    * @param mixed $force
    * @param bool $clean
    */
    public static function initDb($force = false, bool $clean=false)
	{
		igk_set_env("sys://db/constraint_key", "igk_com");
		if (igk_is_conf_connected())
			self::ctrl()->initDbFromFunctions();
	}
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController
	{	
		return $this;
	}
    /**
    * Loads Community Node.
    * @param mixed $n
    */
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