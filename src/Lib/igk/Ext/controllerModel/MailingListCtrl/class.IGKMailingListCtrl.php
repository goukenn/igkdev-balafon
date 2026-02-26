<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKMailingListCtrl.php
// @date: 20220803 13:48:58
// @desc:

/**
* auto generate doc.
*/
abstract class IGKMailingListCtrl  extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * auto generate doc.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
		parent::initComplete();
		igk_getctrl("IGKMailCtrl")->addMailSendEvent($this, "mailinglist_send_mail");

	}

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getcanAddChild(){
		return false;
	}

    /**
    * auto generate doc.
    * @return bool
    */
    public function getisVisible(): bool{
		return false;
	}
} 