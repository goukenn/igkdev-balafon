<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FormHelper.php
// @date: 20220531 11:45:52
// @desc: 
namespace IGK\System\Html\Forms;

use Closure;

/**
 * helper to get basic hml form
 * @package 
 */
class FormHelper{

    /**
     * get submit callable
     * @return Closure 
     */
    public static function submit(){
        return Closure::fromCallable("igk_html_submit");
    }
    public static function FormActionHost($form, $callback){
        return function($a)use($callback, $form){
            return $callback($a, $form);
        };
    }
    public static function __callStatic($name, $args){
        return null;
    }
}
