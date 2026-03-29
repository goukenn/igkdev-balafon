<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IDbArrayResult.php
// @date: 20220804 21:59:04
// @desc: db array result
namespace IGK\Database;
use IGK\System\IToArrayResolver;
/**
* Interface for db array result.
* @package IGK\Database
*/
interface IDbArrayResult extends IToArrayResolver{
    /**
    * To array.
    * @return array
    */
    public function to_array():array;
}