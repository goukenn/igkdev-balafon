<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionGroupItemOptions.php
// @date: 20221123 18:30:31
namespace IGK\System\Html\Dom\Component;
use IGK\System\Traits\ActivableTrait;
/**
* 
* @package IGK\System\Html\Dom\Component
*/
class ActionGroupItemOptions{
    use ActivableTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $text;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $id;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $auth;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $type;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $value;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $defaultclass = 'igk-action-item';
}