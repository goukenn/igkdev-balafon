<?php


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