<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormPatternValidator.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Forms\Validations;
/**
* Interface for form pattern validator.
* @package IGK\System\Html\Forms\Validations
*/
interface IFormPatternValidator{
    /**
     * Set the validation pattern.
     * @param string $pattern The regex or validation pattern to apply.
     */
    function setPattern(string $pattern);
    /**
     * Check whether the value matches the configured pattern.
     * @param mixed $value The value to test against the pattern.
     * @return mixed
     */
    function matchPattern($value);
}