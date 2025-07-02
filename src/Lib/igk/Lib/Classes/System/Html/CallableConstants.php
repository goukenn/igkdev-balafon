<?php
// @author: C.A.D. BONDJE DOUE
// @file: CallableConstants.php
// @date: 20250312 13:04:44
namespace IGK\System\Html;


/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
abstract class CallableConstants{
    /**
     * (node, options):boolean
     */
    const CALLABLE_ACCEPT_RENDER = 'AcceptRender';
    const IS_VISIBLE_METHOD = 'getIsVisible';
    const SET_URI_METHOD ='setUri';
    const CAN_RENDER_TAG_METHOD = 'getCanRenderTag';
}