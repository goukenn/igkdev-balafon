<?php
// @author: C.A.D. BONDJE DOUE
// @file: IPhoneBookDetailVisitor.php
// @date: 20251219 16:15:47
namespace IGK\System\Database;

use IGK\System\IInjectable;

/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
interface IPhoneBookDetailVisitor extends IInjectable{
    function visit(string $propertyName, $value, $oldvalue, $cardinality=null);
}