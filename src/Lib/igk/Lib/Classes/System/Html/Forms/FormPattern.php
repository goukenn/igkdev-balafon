<?php

namespace IGK\System\Html\Forms;

class FormPattern{
    const number = "^[0-9]+(\.([0-9]+)?)?$";
    const integer = "^[0-9]+$";
    const url = "^(((http(s){0,1}):)?\/\/([\w\.0-9]+)|(\?))";
    const identifier = "(\w|[_]+[\w0-9])([\w0-9_]*)";
    const version = "^[0-9]+(\.[0-9]+){0,3}";
}