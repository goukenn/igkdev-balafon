<?php
// @file: IDbQueryFetchResult.php
// @author: C.A.D. BONDJE DOUE
// @desc: interface to declare a fetch return 
// @date: 20220305

namespace IGK\Database;

use Iterator;

/**
 * represent fetch result db
 * @package IGK\Database
 */
interface IDbQueryFetchResult extends Iterator , IDbFetchResult{
    function fetch(): bool;
}