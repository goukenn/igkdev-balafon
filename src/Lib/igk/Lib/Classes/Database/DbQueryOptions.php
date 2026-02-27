<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbQueryOptions.php
// @date: 20220531 09:36:43
// @desc: 
namespace IGK\Database;
/**
 * query database option
 * @package IGK\Database
 */
class DbQueryOptions implements IDbQueryOptions{

    /**
    * Constant: prop columns.
    * @var mixed
    */
    const PROP_COLUMNS = 'Columns';
    /**
     * selected columns 
     * @var ?array 
     */
    var $Columns;
    /**
     * limit option
     * @var mixed
     */
    var $Limit;
    /**
     * order by options
     * @var mixed
     */
    var $OrderBy;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $GroupBy; 
}