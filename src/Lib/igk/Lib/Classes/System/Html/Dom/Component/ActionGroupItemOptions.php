<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupItemOptions.php
// @date: 20221123 18:30:31
namespace IGK\System\Html\Dom\Component;

use IGK\System\Traits\ActivableTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupItemOptions{
    use ActivableTrait;
    var $text;
    var $id;
    var $name;
    var $auth;
    var $type;
    var $value;
    var $defaultclass = 'igk-action-item';
}