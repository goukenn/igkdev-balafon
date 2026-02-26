<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewEndConditional.php
// @date: 20221103 10:38:23
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use Closure;
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewEndConditional{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $info;

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @param mixed $option
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    public function handle($option, $id, $value):bool{
        return call_user_func_array($this->listener, [$option, $id, $value]);
    }
}