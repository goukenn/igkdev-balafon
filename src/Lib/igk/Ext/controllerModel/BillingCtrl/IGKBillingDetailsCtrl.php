<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingDetailsCtrl.php
// @date: 20220803 13:48:59
// @desc:


use IGK\Controllers\NonAtomicTypeBase;
use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;
use IGK\System\Models\IModelDefinitionInfo;

include_once __DIR__."/IGKBillingConstants.php";

/**
* Igkbilling details ctrl.
*/
final class IGKBillingDetailsCtrl extends NonAtomicTypeBase //first non atomic data
{
	use NoDbActiveControllerTrait;

	/**
	 * Retrieve the parent billing controller instance.
	 *
	 * @return mixed
	 */
	private function getBilling(){
		return igk_getctrl(IGKBillingConstants::BILL_CTRL, false);
	}

	/**
	 * Return the data table name derived from the parent billing controller.
	 *
	 * @return string|null
	 */
	public function getDataTableName(): ?string
	{
		if(is_null($m = $this->getBilling())){
			return null;
		}
		return igk_getv($m, "DataTableName", "%prefix%_billing")."_details";
	}

	/**
	 * Return the schema migration info describing the billing details table structure.
	 *
	 * @return IModelDefinitionInfo|null
	 */
	public function getDataTableInfo():?IModelDefinitionInfo
	{
	return Activator::CreateNewInstance(SchemaMigrationInfo::class,  array(
		new DbColumnInfo(array(IGK_FD_NAME=>IGK_FD_ID, IGK_FD_TYPE=>"Int","clAutoIncrement"=>true,IGK_FD_TYPELEN=>10, "clIsUnique"=>true, "clIsPrimary"=>true)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clBillId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clUId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clRefId", IGK_FD_TYPE=>"VarChar", IGK_FD_TYPELEN=>30)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clQte", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clAmount", IGK_FD_TYPE=>"Float", IGK_FD_TYPELEN=>10)),
	));
	}

}
