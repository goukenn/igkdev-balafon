<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AccordeonCookiePanel.php
// @date: 20220803 13:48:58
// @desc: 


final class AccordeonCookiePanel extends IGKObject{
	var $m_pindex;
	var $m_o;

	public function __construct($o, $index){
		$this->m_o = $o;
		$this->m_pindex = $index;

	}
	public function getCookieId(){
	$m = $this->m_o->getCookieId();
	return  $m ? $m."#".$this->m_pindex : null; }
}