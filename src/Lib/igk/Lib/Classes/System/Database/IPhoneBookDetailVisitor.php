<?php
// @author: C.A.D. BONDJE DOUE
// @file: IPhoneBookDetailVisitor.php
// @date: 20251219 16:15:47
namespace IGK\System\Database;
use IGK\System\IInjectable;

/**
* auto generate doc.
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
interface IPhoneBookDetailVisitor extends IInjectable{
    /**
    * Visit.
    * @param string $propertyName
    * @param mixed $value
    * @param mixed $oldvalue
    * @param null|mixed $cardinality
    */
    function visit(string $propertyName, $value, $oldvalue, $cardinality=null);
}