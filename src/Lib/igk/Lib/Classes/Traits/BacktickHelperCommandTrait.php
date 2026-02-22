<?php
// @author: C.A.D. BONDJE DOUE
// @file: BacktickHelperCommandTrait.php
// @date: 20250215 11:41:53
namespace IGK\Traits;
/**
* 
* @package IGK\Traits
* @author C.A.D. BONDJE DOUE
*/
trait BacktickHelperCommandTrait{
    /**
     * 
     * @param mixed $back_tick_command_result 
     * @return null|array 
     */
    public static function HandleBacktickCommand($back_tick_command_result){
        if (is_null($back_tick_command_result)){
            return null;
        }
        $v = explode("\n", trim($back_tick_command_result));
        $exit_code = trim(array_pop($v));
        $output = rtrim(implode("\n", $v));
        return compact('output', 'exit_code');
    }
}