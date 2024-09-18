<?php
// @author: C.A.D. BONDJE DOUE
// @file: IToJSon.php
// @date: 20240906 17:24:07
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
interface IToJSon{ 
    /**
     * 
     * @param mixed $option encoding option
     * @param int $flag json_encode flag
     * @return mixed 
     */
    function to_json($option=null, int $flag=0); 
}