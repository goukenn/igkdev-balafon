<?php
namespace IGK\System\Html\Forms;

interface IFormPatternValidator{
    function setPattern(string $pattern);

    function matchPattern($value);
}