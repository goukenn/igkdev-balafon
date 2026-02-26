<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingDetailsEntry.php
// @date: 20220803 13:48:59
// @desc:

/**
* auto generate doc.
*/
final class IGKBillingDetailsEntry
{
	var $clId;
	var $clUId;
	var $clBillId;
	var $clRefId;
	var $clQte;
	var $clAmount;

	/**
	 * Return the string representation of this billing details entry.
	 *
	 * @return string
	 */
	public function __toString(){
		return __CLASS__;
	}
}
