<?php
abstract class IGKMailingListCtrl  extends \IGK\Controllers\ControllerTypeBase
{
	protected function initComplete($context=null){
		parent::initComplete();
		igk_getctrl("IGKMailCtrl")->addMailSendEvent($this, "mailinglist_send_mail");

	}
	public function mailinglist_send_mail($sender, $args)
	{
		if ($args){
			$s = igk_db_objentries($this, $args);
			$this->insert((array)$s);
		}
	}
 
	public function getcanAddChild(){
		return false;
	}
	public function getisVisible(){
		return false;
	}
} 