<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormPattern.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Forms;
/**
 * form an route pattern
 * @package IGK\System\Html\Forms
 */
class FormPattern{
    /**
    * Constant: number.
    * @var mixed
    */
    const number = "^[0-9]+(\.([0-9]+)?)?$";
    /**
    * Constant: integer.
    * @var mixed
    */
    const integer = "^[0-9]+$";
    /**
    * Constant: url.
    * @var mixed
    */
    const url = "^(((http(s){0,1}):)?\/\/([\w\.0-9]+)|(\?))";
    /**
    * Constant: identifier.
    * @var mixed
    */
    const identifier = "(\w|[_]+[\w0-9])([\w0-9_]*)";
    /**
    * Constant: version.
    * @var mixed
    */
    const version = "^[0-9]+(\.[0-9]+){0,3}";
}