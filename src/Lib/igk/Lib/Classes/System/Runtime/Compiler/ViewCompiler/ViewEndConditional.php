<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewEndConditional.php
// @date: 20221103 10:38:23
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use Closure;
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewEndConditional{
    /**
    * Property: info.
    * @var mixed
    */
    var $info;
    /**
    * Listener: listener.
    * @var mixed
    */
    var $listener;
    /**
    * .ctr
    * @param mixed $info
    * @param callable $listener
    */
    public function __construct($info, callable $listener)
    {
        $this->info = $info;
        $this->listener = Closure::fromCallable($listener)->bindTo($this);
    }
    /**
    * Handles.
    * @param mixed $option
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    public function handle($option, $id, $value):bool{
        return call_user_func_array($this->listener, [$option, $id, $value]);
    }
}