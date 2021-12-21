<?php

use IGK\Controllers\NonAtomicTypeBase;

final class IGKBillingEntry
{
	var $clId;
	var $clUId;
	var $clDate;
	var $clTotalAmount;

	public function __toString(){
		return "BillingEntry";
	}
}
