<?php
// @author: C.A.D. BONDJE DOUE
// @file: MySQLDbExpression.php
// @date: 20260629 17:26:33
namespace IGK\System\Database\MySQL;

use IGK\Database\DbExpression;

/**
* 
* @package IGK\System\Database\MySQL
* @author C.A.D. BONDJE DOUE
*/
class MySQLDbExpression extends DbExpression{
    public function __construct($value = null)
    {
        return parent::__construct($value);
    }
    /**
     * 
     * @param mixed $driver 
     * @return bool 
     */
    public function isAvailable($driver):bool{
        return $driver->getName() == IGK_MYSQL_DATAADAPTER;
    }
}