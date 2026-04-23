<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCaddyController.php
// @date: 20220803 13:48:59
// @desc:
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlUtils;

/**
* Igkcaddy info.
*/
final class IGKCaddyInfo
{
    /**
    * Identifier: cl id.
    * @var mixed
    */
    var $clId; 
    /**
    * Property: cl ref.
    * @var mixed
    */
    var $clRef;	
    /**
    * Identifier: cl cadd id.
    * @var mixed
    */
    var $clCaddId; 
    /**
    * Identifier: cl uid.
    * @var mixed
    */
    var $clUId; 
    /**
    * Property: cl title.
    * @var mixed
    */
    var $clTitle; 
    /**
    * Property: cl description.
    * @var mixed
    */
    var $clDescription; 
    /**
    * Property: cl unit price.
    * @var mixed
    */
    var $clUnitPrice; 
    /**
    * Property: cl tva.
    * @var mixed
    */
    var $clTva;
    /**
    * Property: cl qte.
    * @var mixed
    */
    var $clQte;
	/**
	 * Calculate the total amount including VAT for this caddy item.
	 *
	 * @return float
	 */
    public function getAmount(){
		return ($this->clUnitPrice * $this->clQte ) * (1 + $this->clTva/100.0);
	}
	/**
	 * Calculate the VAT-inclusive amount for this caddy item.
	 *
	 * @return float
	 */
    public function getTvaAmount(){
		return ($this->clUnitPrice * $this->clQte ) * (1 + $this->clTva/100.0);
	}
	/**
	 * Copy properties from the given object into this instance.
	 *
	 * @param object|array $e Source object or array of key-value pairs to copy.
	 * @return void
	 */
    public function Copy($e)
	{
		foreach($e as $k=>$v)
		{
			$this->$k = $v;
		}
	}
	/**
	 * Return the string representation of this caddy info.
	 *
	 * @return string
	 */
    public function __toString(){
		return "caddy_info";
	}
}
/**
* Igkcaddy ctrl.
*/
abstract class IGKCaddyCtrl extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Identifier: cadid.
    * @var mixed
    */
    private $m_cadid; 
    /**
    * Property: caddyinfo.
    * @var mixed
    */
    private $m_caddyinfo;
	/**
	 * Return the current caddy info collection.
	 *
	 * @return array|null
	 */
    public function getCaddyInfo(){
		return $this->m_caddyinfo;
	}
	/**
	 * Return additional configuration info (none for this controller).
	 *
	 * @return null
	 */
    public static function GetAdditionalConfigInfo()
	{
		return null;
	}
	/**
	 * Return the data adapter name used by this controller.
	 *
	 * @return string
	 */
    public function getDataAdapterName():string{
		return IGK_MYSQL_DATAADAPTER;
	}
	/**
	 * Retrieve all database entries belonging to the current caddy.
	 *
	 * @return mixed
	 */
    public function getDBEntries()
	{
		return $this->selectAndWhere( array(
			"clCaddId"=>$this->m_cadid,
		));
	}
	/**
	 * Load and refresh caddy info from the database for the current caddy ID.
	 *
	 * @return void
	 */
    public function __updateCaddyInfo()
	{
		$s = $this->getDBEntries();
		$this->m_caddyinfo = array();
		if ($s!=null){
			foreach($s->Rows as  $v)
			{
				$e = new IGKCaddyInfo();
				$e->Copy($v);
				$this->m_caddyinfo[] = $e;
			}
		}
	}
	/**
	 * Complete controller initialization and register this caddy controller.
	 *
	 * @param mixed $context Optional initialization context.
	 * @return void
	 */
    protected function initComplete($context=null)
	{
		parent::initComplete();
		$this->m_cadid =($this->m_cadid) ?  $this->m_cadid : igk_new_id();
		$this->app->ControllerManager->register("Caddy", $this);
	}
	/**
	 * Reload caddy entries from the database for the current user.
	 *
	 * @return void
	 */
	private function __reloadCaddy()
	{
		$u = $this->getUser();
		$e =  $this->selectAndWhere( array(
			"clUId"=>$u->clId
			));
		$this->m_caddyinfo = array();
		foreach($e->Rows as  $v)
		{
			$r = new IGKCaddyInfo();
			$r->Copy($v);
			$this->m_caddyinfo[] = $r;
		}
	}
	/**
	 * Handle user change by associating or merging the caddy with the connected user.
	 *
	 * @return void
	 */
    public function __userChanged()
	{
		$u = $this->app->Session->User;
		if ($u !=null)
		{
			$e =  $this->selectAndWhere( array(
			"clUId"=>$u->clId
			));
				if (($e==null) || ($e->RowCount == 0))
				{
					$this->__attachCaddyInfoToUser($u);
				}
				else{
					$this->m_cadid = igk_new_id();
					$this->__attachCaddyInfoToUser($u, false);
					foreach($e->Rows as $v)
					{
						$r = new IGKCaddyInfo();
						$r->Copy($v);
						$r->clCaddId = $this->m_cadid ;
						$this->m_caddyinfo[] = $r;
					}
					$this->update($this->m_caddyinfo);
				}
		}
	}
	/**
	 * Attach all current caddy items to the given user and optionally persist.
	 *
	 * @param object $u      The user object to attach the caddy to.
	 * @param bool   $update Whether to persist the update to the database.
	 * @return void
	 */
	private function __attachCaddyInfoToUser($u, $update = true)
	{
		foreach($this->m_caddyinfo as  $v)
		{
			$v->clCaddId = $this->m_cadid;
			$v->clUId = $u->clId;
		}
		if ($update)
		$this->update($this->m_caddyinfo);
	}
	/**
	 * Return whether this controller is visible.
	 *
	 * @return bool
	 */
    public function getIsVisible():bool{
		return true;
	}
	/**
	 * Return whether this controller can accept child controllers.
	 *
	 * @return bool
	 */
    public function getCanAddChild() {
		return false;
	}
	/**
	 * Initialize or retrieve the user's caddy session.
	 *
	 * @return void
	 */
    public function cadd_initusercaddy()
	{
	}
	/**
	 * Add a product to the caddy, or increment quantity if it already exists.
	 *
	 * @param object|null $obj Product data object; uses request object if null.
	 * @return bool
	 */
    public function caddy_addproduct($obj=null){
		$obj = ($obj==null)? igk_get_robj() :$obj;
		$s =  $this->selectAndWhere( array(
			"clRef"=>$obj->clRef
			));
		if ($s && $s->RowCount>0)
		{
			foreach($s->Rows as  $v)
			{
				$v->clQte += igk_getv($obj, "clQte",0);
				$this->update($v);
			}
		}
		else{
			$e = new  IGKCaddyInfo();
			$e->clId = null;
			$e->clCaddId = $this->m_cadid;
			$e->clUId  = ($this->app->Session->User !=null) ? $this->app->Session->User->clId: 0;
			$e->clQte = igk_getv($obj, "clQte",12);
			$e->clTva = igk_getv($obj, "clTva",21);
			$e->clUnitPrice = igk_getv($obj, "clUnitPrice",12);
			$e->clTitle = igk_getv($obj, "clTitle", "Produit #");
			$e->clRef = igk_getv($obj, "clRef", "XXXREF");
			$e->clDescription = igk_getv($obj, "clDescription", "Descriptioin du produit");
			$this->insert(
				$e
			);
		}
		$this->__updateCaddyInfo();
		return true;
	}
	/**
	 * Clear the caddy via an AJAX request and re-render it.
	 *
	 * @return void
	 */
    public function caddy_clear_ajx(){
		$this->caddy_clear();
		igk_wl($this->caddy_render());
	}
	/**
	 * Delete all items from the caddy and reset the in-memory collection.
	 *
	 * @return void
	 */
    public function caddy_clear()
	{
		$this->delete($this->m_caddyinfo);
		$this->m_caddyinfo = array();
	}
	/**
	 * Calculate and return the total amount for all items in the caddy.
	 *
	 * @return float
	 */
    public function caddy_totalamout()
	{
		$amount = 0.0;
			if ($this->m_caddyinfo)
			{
		foreach($this->m_caddyinfo as  $v)
		{
			$amount += $v->getAmount();
		}}
		return $amount;
	}
	/**
	 * Validate the caddy by sending it to the billing controller and clearing it.
	 *
	 * @return void
	 */
    public function caddy_validate()
	{
		if (igk_count($this->m_caddyinfo )> 0)
		{
			$billing = $this->app->ControllerManager->getRegCtrl("Billing");
			if ($billing)
			{
				$billing->store($this->m_caddyinfo);
				$this->caddy_clear();
				$this->caddy_render();
				igk_notifyctrl()->addMsg("Caddy validated");
			}
			else {
				igk_notifyctrl()->addErrorr("no billing in this system");
			}
		}
		igk_notifyctrl()->NotifyHost = $this->TargetNode;
		igk_navtocurrent($this->CurrentPage);
	}
	/**
	 * Validate the caddy via AJAX and update the target node response.
	 *
	 * @return void
	 */
    public function caddy_validate_ajx()
	{
		$this->caddy_validate();
		$t = $this->TargetNode;
		$this->caddy_render();
		$script =  HtmlNode::CreateWebNode("script");
		$script->Content = "console.log(igk.getParentScript().innerHTML); ";
		$t->add($script);
		$t->renderAJX();
	}
	/**
	 * Remove a single caddy item identified by request parameter "n".
	 *
	 * @return void
	 */
    public function caddy_remove()
	{
		$n = igk_getr("n");
		$this->delete((object)array("clId"=>$n ));
	}
	/**
	 * Persist the current in-memory caddy info to the database.
	 *
	 * @return void
	 */
    public function caddy_store()
	{
		$this->update($this->m_caddyinfo);
	}
	/**
	 * Render the caddy table into the target node and return the HTML output.
	 *
	 * @return string
	 */
    public function caddy_render(){
		$t = $this->TargetNode;
		$t->clearChilds();
		$t = $t->div();
		$tab = $t->add("table");
		$tab["class"] = "fitw caddy_table";
		$tr = $tab->add("tr");
		HtmlUtils::AddToggleAllCheckboxTh($tr);
		$tr->add("th")->Content = R::ngets("title.caddy.ref");
		$tr->add("th")->Content = R::ngets("title.caddy.title");
		$tr->add("th")->Content = R::ngets("title.caddy.desc");
		$tr->add("th")->Content = R::ngets("title.caddy.unitprice");
		$tr->add("th")->Content = R::ngets("title.caddy.qte");
		$tr->add("th")->Content = R::ngets("title.caddy.tva");
		$tr->add("th")->Content = R::ngets("title.caddy.totalamount");
		$tr->add("th")->Content = IGK_HTML_SPACE;
		$qte = 0;
		if ($this->m_caddyinfo)
		foreach($this->m_caddyinfo as $v)
		{
			$tr = $tab->add("tr");
			$tr->add("td")->addInput("", "checkbox");
			$tr->add("td")->Content = $v->clRef;
			$tr->add("td")->Content = $v->clTitle;
			$tr->add("td")->Content = $v->clDescription;
			$tr->add("td", array("class"=>"alignr"))->Content = $v->clUnitPrice;
			$tr->add("td", array("class"=>"alignr"))->Content = $v->clQte;
			$tr->add("td", array("class"=>"alignr"))->Content = $v->clTva;
			$tr->add("td", array("class"=>"alignr") )->Content = $v->getAmount();
			HtmlUtils::AddImgLnk($tr->add("td"), $this->getUri("caddy_remove&n=".$v->clId), "drop");
			$qte += $v->clQte;
		}
		$tr = $tab->add("tr");
		$tr->add("td", array("colspan"=>5))->Content = R::ngets("lb.TotalAmount");
		$tr->add("td", array("class"=>"alignr"))->Content = $qte;
		$tr->add("td")->Content = IGK_HTML_SPACE;
		$tr->add("td", array("class"=>"alignr"))->Content = $this->caddy_totalamout();
		$tr->add("td")->Content = IGK_HTML_SPACE;
		igk_html_toggle_class($tab);
		HtmlUtils::AddBtnLnk($t,R::ngets("btn.clear"), igk_js_ajx_aposturi( $this->getUri("caddy_clear_ajx"), $t["id"]));
		HtmlUtils::AddBtnLnk($t,R::ngets("btn.validate"),  $this->getUri("caddy_validate"));
		return $this->TargetNode->render();
	}
}