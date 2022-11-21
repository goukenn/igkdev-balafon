<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionFormHandlerTrait.php
// @date: 20221109 18:53:25
namespace IGK\System\Actions\Traits;

use Closure;
use IGK\Actions\ActionFormOptions;
use IGK\Controllers\BaseController;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Actions\Traits
*/
trait ActionFormHandlerTrait{
    /**
     * create a view handler callable 
     * @param mixed $name 
     * @param null|BaseController $controller 
     * @return ?Closure
     * @throws IGKException 
     */
    public static function Form($name, ?ActionFormOptions $option =null, ?BaseController $controller=null){
        $controller = $controller ?? igk_get_current_base_ctrl();
        if ($controller  && method_exists(static::class, $fc = 'form_'.$name)){
            $action = new static;
            $action->setController($controller);
            return Closure::fromCallable(function($a)use($fc, $option, $name){
                $a->setClass('+'.igk_css_str2class_name($fc));
                return $this->$fc($a, $option);
            })->bindTo($action);
        }
        return function($n)use($name){
            if (igk_environment()->isDev()){
                $n->panelbox()->setClass('igk-danger')->Content = sprintf(__('no form [%s] in action handler'), $name);
            }
        };
    }
}