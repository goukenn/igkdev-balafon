<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormPatternValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

interface IFormPatternValidator{
    function setPattern(string $pattern);

    function matchPattern($value);
}