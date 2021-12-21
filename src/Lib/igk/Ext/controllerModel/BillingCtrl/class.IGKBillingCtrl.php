<?php

use IGK\Controllers\NonAtomicTypeBase;
abstract class IGKBillingCtrl extends \IGK\Controllers\ControllerTypeBase
{
	private $m_billingDetails;

	protected function getDBConfigFile()
	{
		return igk_io_getdbconf_file(dirname(__FILE__));
	}
	protected function getConfigFile()
	{
		return igk_io_getconf_file(dirname(__FILE__));
	}
	protected function InitComplete()
	{
		parent::InitComplete();
		//register a billing
		$this->app->getControllerManager()->register("Billing", $this);
		$this->app->getControllerManager()->register("BillingDetails", new IGKBillingDetailsCtrl());
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
	public function getBillingPDF(){
		$u = $this->app->Session->User;
		if ($u == null)return;

		$pdf = new IGKPDF();

		$pdf->render();
		igk_exit();
	}
}

//mark the class as a system controller
igk_sys_regSysController(IGKBillingDetailsCtrl::class); 