<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 

/**
 * Parse a raw email address string into an object with email and display name.
 *
 * @param string $i The raw email address string (e.g. "Name <email@example.com>").
 * @return object Object with clEmail and clDisplayName properties.
 */
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
