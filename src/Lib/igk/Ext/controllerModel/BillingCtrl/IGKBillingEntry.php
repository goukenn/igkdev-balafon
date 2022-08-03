<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingEntry.php
// @date: 20220803 13:48:59
// @desc: 


use IGK\Controllers\NonAtomicTypeBase;

final class IGKBillingEntry
{
	var $clId;
	var $clUId;
	var $clDate;
	var $clTotalAmount;

	public function __toString(){
		return __CLASS__;
	}
}
