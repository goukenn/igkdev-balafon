<?php
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