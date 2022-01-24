<?php
namespace IGK\System\Html\Forms;

interface IFormValidator{
    function validate($value, $default=null, $fieldinfo=null, & $error=[]);
}