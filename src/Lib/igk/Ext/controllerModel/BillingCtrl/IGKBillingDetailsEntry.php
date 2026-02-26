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
    var $clBillId;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clRefId;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clQte;

    /**
    * auto generate doc.
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
