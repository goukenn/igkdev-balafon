<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupItemOptions.php
// @date: 20221123 18:30:31
namespace IGK\System\Html\Dom\Component;
use IGK\System\Traits\ActivableTrait;
/**
* auto generate doc.
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupItemOptions{
    use ActivableTrait;
    /**
    * Property: text.
    * @var mixed
    */
    var $text;
    /**
    * Identifier: id.
    * @var mixed
    */
    var $id;
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: auth.
    * @var mixed
    */
    var $auth;
    /**
    * Type of type.
    * @var mixed
    */
    var $type;
    /**
    * Property: value.
    * @var mixed
    */
    var $value;
    /**
    * Property: defaultclass.
    * @var mixed
    */
    var $defaultclass = 'igk-action-item';
}