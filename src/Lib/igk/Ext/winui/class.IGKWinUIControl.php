<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKWinUIControl.php
// @date: 20220803 13:48:58
// @desc: 

//UTILITY FUNCTION

//----------------------------------------------------------------
//represent a base winui control
//----------------------------------------------------------------

use IGK\System\Html\Dom\HtmlNode;

abstract class IGKWinUIControl extends HtmlNode
{
	private $m_id;

	public function __construct($tagname)
	{
		$this->m_id = igk_new_id();
		parent::__construct($tagname);
	}
} 