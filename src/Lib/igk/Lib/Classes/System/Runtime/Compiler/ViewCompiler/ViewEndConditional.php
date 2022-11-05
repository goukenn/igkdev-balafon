<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewEndConditional.php
// @date: 20221103 10:38:23
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use Closure;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewEndConditional{
    var $info;
    var $listener;
    public function __construct($info, callable $listener)
    {
        $this->info = $info;
        $this->listener = Closure::fromCallable($listener)->bindTo($this);
    }
    public function handle($option, $id, $value):bool{
        return call_user_func_array($this->listener, [$option, $id, $value]);
    }
}