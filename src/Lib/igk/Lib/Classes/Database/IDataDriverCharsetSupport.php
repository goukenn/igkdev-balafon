<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDataDriverCharsetSupport.php
// @date: 20250123 06:46:32
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
interface IDataDriverCharsetSupport{
    /**
     * get the current driver charset
     * @return ?string 
     */
    function get_charset();
    /**
     * set the current charset
     * @param string $charset 
     * @return mixed 
     */
    function set_charset(string $charset);
}   