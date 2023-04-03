<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGrammar.php
// @date: 20230305 21:56:20
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
*/
interface IGrammar{
    /**
     * create random query 
     * @param string $column 
     * @return string 
     */
    function createRandomQueryTableOnColumn(string $table, string $column, ?array $columns=null, int $limit=1): ?string;
   
}