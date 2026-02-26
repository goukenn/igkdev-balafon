<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKBillingEntry.php
// @date: 20220803 13:48:59
// @desc:


use IGK\Controllers\NonAtomicTypeBase;

/**
* auto generate doc.
*/
final class IGKBillingEntry
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clId;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clUId;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clDate;

    /**
    * auto generate doc.
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
