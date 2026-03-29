<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerParams.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
/**
 * controller environment param
 * @package IGK\Controllers
 */
class ControllerEnvParams{
    public
    /**
    * Constant: action view response.
    * @var mixed
    */
    const ActionViewResponse = "@ActionViewResponse";
    public
    /**
    * Constant: menus.
    * @var mixed
    */
    const Menus = "@menu";
    public
    /**
    * Constant: view loader.
    * @var mixed
    */
    const ViewLoader = "@viewLoader";
    /**
     * bool disable action handler 
     */
    public const NoActionHandle = "@noActionHandle";
    /**
     * bool disable view compilation 
     */
    public const NoCompilation = "@noCompilation";
    /**
    * auto generate doc.
    */    public const AllowHiddenView = "@AllowHiddenView";
    /**
     * no do view reponse for request
     */
    public const NoDoViewResponse = "@NoDoViewResponse";
}