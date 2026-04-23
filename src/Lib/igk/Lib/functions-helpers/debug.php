<?php
// @author: C.A.D. BONDJE DOUE
// @filename: debug.php
// @date: 20220831 14:30:44
// @desc: debug functin helper
use IGK\Server;

/**
 * get if APP DEBUG is active
 * @param ?string $debugKey the debugger key expression
 * @return bool
 */
function igk_is_debug(?string $debugKey=null): bool
{
    if (!is_null($debugKey)){
        return boolval(igk_environment()->get('debug_'.strtolower($debugKey)));
    }
    return boolval(igk_environment()->isDebug());
}
/**
 * check allow debuggin 
 * @return bool
 */
function igk_is_debuging(): bool{
   $cnf = igk_configs(); 
    return (Server::IsLocal() && ($cnf->allow_debugging ? $cnf->allow_debugging : false));
}