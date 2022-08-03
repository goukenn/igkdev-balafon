<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_html_utils.php
// @date: 20220803 13:48:54
// @desc: 

function igk_html_attribvalue($n){
	if (!$n){
		if (is_numeric($n)){
			$n = "0";
		}
	}
	return "\"".$n."\"";
}