<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionFormHandlerTrait.php
// @date: 20221109 18:53:25
namespace IGK\System\Actions\Traits;

use Closure;
use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\System\Actions\Traits
*/
trait ActionFormHandlerTrait{
    public static function form($name, ?BaseController $controller=null){
        $controller = $controller ?? igk_get_current_base_ctrl();
        if ($controller  && method_exists(static::class, $fc = 'form_'.$name)){
            $action = new static;
            $action->ctrl = $controller;
            return Closure::fromCallable(function($a)use($fc){
                return $this->$fc($a);
            })->bindTo($action);
        }
    }
}