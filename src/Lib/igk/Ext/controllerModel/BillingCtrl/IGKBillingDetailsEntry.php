<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingDetailsEntry.php
// @date: 20220803 13:48:59
// @desc: 



final class IGKBillingDetailsEntry
{
	var $clId;
	var $clUId;
	var $clBillId;
	var $clRefId;
	var $clQte;
	var $clAmount;

	public function __toString(){
		return __CLASS__;
	}
}