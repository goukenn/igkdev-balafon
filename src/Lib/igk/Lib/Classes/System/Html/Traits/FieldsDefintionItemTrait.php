<?php
// @author: C.A.D. BONDJE DOUE
// @file: FieldsDefintionItemTrait.php
// @date: 20221123 18:22:52
namespace IGK\System\Html\Traits;


/**
* provide a fields method actions 
* @package IGK\System\Html\Traits
*/
trait FieldsDefintionItemTrait{
    public abstract function fields(array $items, $options=null);
}