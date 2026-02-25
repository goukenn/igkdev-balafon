<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 


require_once __DIR__."/Lib/Classes/Html/Node/CalcNode.php";


/**
 * Create and return a new CalcNode instance.
 *
 * @return CalcNode The newly created calculator node.
 */
function igk_html_node_calcnode(){
    return new CalcNode();
}