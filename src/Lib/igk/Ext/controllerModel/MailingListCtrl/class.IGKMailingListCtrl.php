<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKMailingListCtrl.php
// @date: 20220803 13:48:58
// @desc:

/**
* Igkmailing list ctrl.
*/
abstract class IGKMailingListCtrl  extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
		parent::initComplete();
		igk_getctrl("IGKMailCtrl")->addMailSendEvent($this, "mailinglist_send_mail");
	}
    /**
    * Mailinglist send mail.
    * @param mixed $sender
    * @param mixed $args
    */
    public function mailinglist_send_mail($sender, $args)
	{
		if ($args){
			$s = igk_db_objentries($this, $args);
			$this->insert((array)$s);
		}
	}
    /**
    * Getcan add child.
    */
    public function getcanAddChild(){
		return false;
	}
    /**
    * Getis visible.
    * @return bool
    */
    public function getisVisible(): bool{
		return false;
	}
} 