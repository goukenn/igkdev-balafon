<?php
// @author: C.A.D. BONDJE DOUE
// @filename: .global.php
// @date: 20220803 13:48:58
// @desc: 


require_once __DIR__."/Lib/Classes/Html/Node/CalcNode.php";


function igk_html_node_calcnode(){
    return new CalcNode();
}