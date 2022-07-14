<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IDbFecthResult.php
// @date: 20220628 08:19:20
// @desc: result response

namespace IGK\Database;

interface IDbFetchResult{
    /**
     * fetch result
     * @return bool 
     */
    function fetch():bool;
    /**
     * return current row value
     * @return null|object 
     */
    function row() : ?object;
}