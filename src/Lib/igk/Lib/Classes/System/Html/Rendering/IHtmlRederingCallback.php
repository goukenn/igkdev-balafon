<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlRederingCallback.php
// @date: 20241016 13:28:58
namespace IGK\System\Html\Rendering;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Rendering
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlRederingCallback{
    function beforeRender(?callable $callable);
    function afterRender(?callable $callable);
    /**
     * invoke 
     * @param mixed $options 
     * @param mixed $setting ['output'=>]
     * @return mixed 
     */
    function beforeRenderCallback($options, $setting);
    /**
     * invoke before render callback
     * @param mixed $options 
     * @param mixed $setting 
     * @return mixed 
     */
    function afterRenderCallback($options, $setting);
}