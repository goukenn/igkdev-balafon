<?php
// @author: C.A.D. BONDJE DOUE
// @file: CallableConstants.php
// @date: 20250312 13:04:44
namespace IGK\System\Html;
/**
* auto generate doc.
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
abstract class CallableConstants{
    /**
     * (node, options):boolean
     */
    const CALLABLE_ACCEPT_RENDER = 'AcceptRender';
    /**
    * Constant: is visible method.
    * @var mixed
    */
    const IS_VISIBLE_METHOD = 'getIsVisible';
    /**
    * Constant: set uri method.
    * @var mixed
    */
    const SET_URI_METHOD ='setUri';
    /**
    * Constant: can render tag method.
    * @var mixed
    */
    const CAN_RENDER_TAG_METHOD = 'getCanRenderTag';
}