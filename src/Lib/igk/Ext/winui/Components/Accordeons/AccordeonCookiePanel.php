<?php

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