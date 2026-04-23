<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKMySQLDataTableCtrl.php
// @date: 20220803 13:48:59
// @desc: 

/*datatable controller
*/
/**
* Igkmy sql data table ctrl.
*/
abstract class IGKMySqlDataTableCtrl extends \IGK\Controllers\ControllerTypeBase
{
	/**
	 * Returns the list of configuration keys that cannot be edited by the user.
	 *
	 * @return array
	 */
	public static function GetNonConfigurableConfigInfo(){
		return array(
			"clDataAdapterName",
			"clVisible",
			"clVisiblePages",
			"clTargetNodeIndex",
			"clParentCtrl"
		);
	}
	/**
	 * Returns whether the controller is visible.
	 *
	 * @return bool
	 */
	public function getIsVisible():bool {
		return false;
	}
	/**
	 * Returns the data adapter name used by this controller.
	 *
	 * @return string
	 */
	public function getDataAdapterName():string{
		return IGK_MYSQL_DATAADAPTER;
	}
	/**
	 * Returns the name of the controller.
	 *
	 * @return string
	 */
	public function getName(): string{
		return parent::getName();
	}
	/**
	 * Returns the data adapter table name, falling back to the controller name.
	 *
	 * @return string
	 */
	public function getDataAdapterTableName(){
		return igk_getv($this->Configs , "clDataTableName", $this->getName());
	}
} 