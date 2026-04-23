<?php
// @author: C.A.D. BONDJE DOUE
// @file: EvalBinding.php
// @date: 20251229 13:49:11
namespace IGK\Core;

/**
* 
* @package IGK\Core
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Core
*/
class EvalBinding{
    /**
    * Property: evals.
    * @var mixed
    */
    private static $sm_evals;
    /**
    * auto generate doc.
    * @return mixed
    */
    public static function getLastEval(){
        return self::$sm_evals;
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public static function EvalContentOnContext(){
        extract(func_get_arg(1));
        self::$sm_evals = func_get_arg(0);
        return eval(sprintf('return %s;', self::$sm_evals));
    }
}