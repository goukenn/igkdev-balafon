<?php

///<summary>used to configure sytem application folder</summary>

use IGK\Controllers\NonVisibleControllerBase;
use IGK\System\Html\Dom\HtmlNode;

class IGKPickFolderCtrl extends NonVisibleControllerBase
{
	public function getcanModify(){return false;}
	public function getcanDelete(){return false;} 
}

//define a pick folder item
class IGKHtmlPickFolderItem extends HtmlNode
{
	public $m_folder;
	public function getFolder(){return $this->m_folder;}
	public function setFolder($value){ $this->m_folder = $value;}
	public function __construct(){
		parent::__construct("div");
	} 
} 