<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AccordeonCookiePanel.php
// @date: 20220803 13:48:58
// @desc: 


final class AccordeonCookiePanel extends IGKObject{
	private $m_pindex;
	private $m_o;

	/**
	 * Constructor.
	 *
	 * @param mixed $o     The parent accordeon object providing the base cookie ID.
	 * @param int   $index The panel index used to qualify the cookie ID.
	 */
	public function __construct($o, $index){
		$this->m_o = $o;
		$this->m_pindex = $index;

	}
	/**
	 * Get the cookie identifier for this panel, qualified by its index.
	 *
	 * @return string|null The qualified cookie ID, or null if no base ID is set.
	 */
	public function getCookieId(){
	$m = $this->m_o->getCookieId();
	return  $m ? $m."#".$this->m_pindex : null; }
}