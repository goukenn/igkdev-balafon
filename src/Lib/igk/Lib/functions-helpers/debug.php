<?php

// @author: C.A.D. BONDJE DOUE
// @filename: debug.php
// @date: 20220831 14:30:44
// @desc: debug functin helper


///<summary>get if APP DEBUG is active</summary>
/**
 * get if APP DEBUG is active
 */
function igk_is_debug()
{
    return igk_environment()->isDebug();
}
///<summary>get if APP allow debugging</summary>
/**
 * get if APP allow debugging
 */
function igk_is_debuging(){
   $cnf = igk_configs();
    return (\Server::IsLocal() && ($cnf->allow_debugging ? $cnf->allow_debugging : false));
}



