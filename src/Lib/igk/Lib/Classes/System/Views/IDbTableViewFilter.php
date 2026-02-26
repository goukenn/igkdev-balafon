<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbTableViewFilter.php
// @date: 20220703 10:41:26
namespace IGK\System\Views;
/**
* 
* @package IGK\System\Views
*/
interface IDbTableViewFilter{

    /**
    * Returns Header List.
    * @param mixed $firstRow
    */
    public function getHeaderList($firstRow);
    public function filter($key, $value, $node);
}