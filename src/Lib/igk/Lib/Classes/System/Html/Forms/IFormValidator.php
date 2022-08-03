<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IFormValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;

interface IFormValidator{
    function validate($value, $default=null, $fieldinfo=null, & $error=[]);
}