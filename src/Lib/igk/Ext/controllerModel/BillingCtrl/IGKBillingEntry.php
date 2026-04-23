<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingEntry.php
// @date: 20220803 13:48:59
// @desc:
use IGK\Controllers\NonAtomicTypeBase;

/**
* Igkbilling entry.
*/
final class IGKBillingEntry
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
    * Property: cl date.
    * @var mixed
    */
    var $clDate;
    /**
    * Count: cl total amount.
    * @var mixed
    */
    var $clTotalAmount;
	/**
	 * Return the string representation of this billing entry.
	 *
	 * @return string
	 */
    public function __toString(){
		return __CLASS__;
	}
}