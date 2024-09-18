<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormBuilderDataSource.php
// @date: 20240911 11:27:00
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IFormBuilderDataSource{
    /**
     * 
     * @return Closure|array 
     */
    function getDataSource();
    /**
     * retrieve select option items
     * @return mixed 
     */
    function getOptionItems();
}