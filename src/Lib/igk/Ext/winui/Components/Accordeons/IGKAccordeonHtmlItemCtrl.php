<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAccordeonHtmlItemCtrl.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\Controllers\NonVisibleControllerBase;

/**
* Igkaccordeon html item ctrl.
*/
final class IGKAccordeonHtmlItemCtrl extends NonVisibleControllerBase
{
	/**
	 * Indicate whether this controller allows modifications.
	 *
	 * @return bool Always returns false.
	 */
	public function getcanModify(){return false;}

	/**
	 * Indicate whether this controller allows deletion.
	 *
	 * @return bool Always returns false.
	 */
	public function getcanDelete(){return false;}
	 
}