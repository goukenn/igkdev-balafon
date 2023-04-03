<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKBillingCtrl.php
// @date: 20220803 13:48:59
// @desc: 


use IGK\Controllers\NonAtomicTypeBase;
use IGK\Database\DbColumnInfo;
use igk\PDF as PDFModule;

abstract class IGKBillingCtrl extends \IGK\Controllers\ControllerTypeBase
{
 
	
	protected function initComplete($context=null)
	{ 
		parent::initComplete();
		//register a billing
		$this->app->getControllerManager()->register(IGKBillingConstants::BILL_CTRL, $this);
		$this->app->getControllerManager()->register(IGKBillingConstants::BILL_DETAIL_CTRL, new IGKBillingDetailsCtrl());
	}
	public function store($caddyInfo)
	{
		$u = $this->app->Session->User;
		if ( ($u==null) || ($caddyInfo==null) || igk_count($caddyInfo)==0)
			return;

		$e = new IGKBillingEntry ();

		$e->clUId = $u->clId;
		$e->clDate = igk_mysql_datetime_now();
		$m = 0.0;
		$d = igk_getctrl("igkbillingdetailsctrl");
		$tk = array();
		foreach($caddyInfo as  $v)
		{

			$m += $v->getAmount();
			$h = new IGKBillingDetailsEntry();
			$h->clAmount = $v->getAmount();
			$h->clQte = $v->clQte;
			$h->clUId = $u->clId;
			$h->clRefId = $v->clRef;
			$tk[] = $h;
		}
		$e->clTotalAmount = $m;
		if ($this->insert($e)){
			foreach($tk as $v)
			{
				$v->clBillId = $e->clId;
				$d->insert($v);
			}
		}

		$this->getBillingPDF();
	}
	
}
