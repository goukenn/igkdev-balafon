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

    /**
    * Constant: like.
    * @var mixed
    */
    const LIKE='@@';

    /**
    * Constant: in base.
    * @var mixed
    */
    const IN_BASE = '<>';

    /**
    * Constant: not in base.
    * @var mixed
    */
    const NOT_IN_BASE = '!<>';

    /**
    * Constant: in.
    * @var mixed
    */
    const IN = '!!';

    /**
    * Constant: gt.
    * @var mixed
    */
    const GT = '>';

    /**
    * Constant: lt.
    * @var mixed
    */
    const LT = '<';

    /**
    * Constant: not.
    * @var mixed
    */
    const NOT = '!'; 
}