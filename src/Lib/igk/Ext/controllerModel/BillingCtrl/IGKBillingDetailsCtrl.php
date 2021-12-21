<?php

use IGK\Controllers\NonAtomicTypeBase;
use IGK\Database\DbColumnInfo;

final class IGKBillingDetailsCtrl extends NonAtomicTypeBase //first non atomic data
{
	
	public function getDataTableName()
	{
		return null; //igk_getv($this->getBilling(), "DataTableName")."_details";
	}
	public function getDataTableInfo()
	{
	return array(
		new DbColumnInfo(array(IGK_FD_NAME=>IGK_FD_ID, IGK_FD_TYPE=>"Int","clAutoIncrement"=>true,IGK_FD_TYPELEN=>10, "clIsUnique"=>true, "clIsPrimary"=>true)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clBillId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clUId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clRefId", IGK_FD_TYPE=>"VarChar", IGK_FD_TYPELEN=>30)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clQte", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>10)),
		new DbColumnInfo(array(IGK_FD_NAME=>"clAmount", IGK_FD_TYPE=>"Float", IGK_FD_TYPELEN=>10)),
		);
	}

}