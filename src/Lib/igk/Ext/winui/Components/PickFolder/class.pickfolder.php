<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.pickfolder.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\Controllers\NonVisibleControllerBase;
use IGK\System\Html\Dom\HtmlNode;

/**
* auto generate doc.
*/
class IGKPickFolderCtrl extends NonVisibleControllerBase
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

//define a pick folder item

/**
* auto generate doc.
*/
class IGKHtmlPickFolderItem extends HtmlNode
{
	public $m_folder;
	/**
	 * Get the folder associated with this item.
	 *
	 * @return mixed The folder value.
	 */
	public function getFolder(){return $this->m_folder;}

	/**
	 * Set the folder associated with this item.
	 *
	 * @param mixed $value The folder value to set.
	 * @return void
	 */
	public function setFolder($value){ $this->m_folder = $value;}

	/**
	 * Constructor.
	 */
	public function __construct(){
		parent::__construct("div");
	} 
} 