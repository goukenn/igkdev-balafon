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
    * auto generate doc.
    * @var mixed
    */
    const LIKE='@@';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IN_BASE = '<>';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NOT_IN_BASE = '!<>';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IN = '!!';

    /**
    * auto generate doc.
    * @var mixed
    */
    const GT = '>';

    /**
    * auto generate doc.
    * @var mixed
    */
    const LT = '<';

    /**
    * auto generate doc.
    * @var mixed
    */
    const NOT = '!'; 
}