<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingDetailsEntry.php
// @date: 20220803 13:48:59
// @desc:

/**
* Igkbilling details entry.
*/
final class IGKBillingDetailsEntry
{
    /**
    * Identifier: cl id.
    * @var mixed
    */
    var $clId;
    /**
    * Identifier: cl uid.
    * @var mixed
    */
    var $clUId;
    /**
    * Identifier: cl bill id.
    * @var mixed
    */
    var $clBillId;
    /**
    * Identifier: cl ref id.
    * @var mixed
    */
    var $clRefId;
    /**
    * Property: cl qte.
    * @var mixed
    */
    var $clQte;
    /**
    * Property: cl amount.
    * @var mixed
    */
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