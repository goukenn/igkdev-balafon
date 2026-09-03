<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewLayout.php
// @date: 20260825 19:19:33
namespace IGK\System\WinUI;


/**
* 
* @package IGK\System\WinUI
* @author C.A.D. BONDJE DOUE
*/
interface IViewLayout{
    /**
     * 
     * @param string $path 
     * @param array|null $options
     * @return mixed 
     */
    function use(string $path, ?array $options=null);
}