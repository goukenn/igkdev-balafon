<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbTableViewFilter.php
// @date: 20220703 10:41:26
namespace IGK\System\Views;


///<summary></summary>
/**
* 
* @package IGK\System\Views
*/
interface IDbTableViewFilter{
    public function getHeaderList($firstRow);
    public function filter($key, $value, $node);
}