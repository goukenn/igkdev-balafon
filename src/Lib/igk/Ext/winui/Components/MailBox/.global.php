<?php
function igk_mail_get_mailinfo($i)
{
	$b = new StdClass();
	$m = $i;
	$n = $i;
	$tab = array();
	if (preg_match_all("/(?P<name>(.)+)\<(?P<mail>([^\<\>]+))\>/i", $i,  $tab))
	{
		$m = $tab["mail"][0];
		$n = $tab["name"][0];
	}
	$b->clEmail = $m;
	$b->clDisplayName = $n;
	return $b;
}
