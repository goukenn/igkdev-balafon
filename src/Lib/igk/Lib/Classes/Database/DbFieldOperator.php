<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbFieldOperator.php
// @date: 20250509 10:24:08
namespace IGK\Database;


/**
* 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class DbFieldOperator{
    const LIKE='@@';
    const IN_BASE = '<>';
    const NOT_IN_BASE = '!<>';
    const IN = '!!';
    const GT = '>';
    const LT = '<';
    const NOT = '!'; 
}