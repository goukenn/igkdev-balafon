<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BindingExpressionInfo.php
// @date: 20220819 15:33:38
// @desc: 
namespace IGK\System\Html\Templates;

/**
 * use to bind template expression
 * @package IGK\System\Html\Templates
 */
class BindingExpressionInfo {

    var $limiter_start= IGK_EXPRESSION_START_MARKER;
    var $limiter_end = IGK_EXPRESSION_END_MARKER;
    var $limiter_escape = IGK_EXPRESSION_ESCAPE_MARKER;
    public function __construct()
    {        
    }

    /**
     * return regext expression 
     * @return string 
     */
    public function getRegex():string{
        return '/((?P<scope>@*)(?P<escape>[' . $this->limiter_escape . '])?' . $this->limiter_start .
         '(?P<expression>([^\}\{])+)' . $this->limiter_end. ')/';
    }

}
