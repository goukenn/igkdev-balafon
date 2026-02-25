<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_html_utils.php
// @date: 20220803 13:48:54
// @desc: 

/**
 * Returns an HTML attribute value wrapped in double quotes.
 *
 * @param mixed $n The attribute value to wrap.
 * @return string
 */
function igk_html_attribvalue($n){
	if (!$n){
		if (is_numeric($n)){
			$n = "0";
		}
	}
	return "\"".$n."\"";
}