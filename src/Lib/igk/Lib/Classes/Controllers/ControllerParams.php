<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerParams.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

class ControllerParams{
    public const ActionViewResponse = "@ActionViewResponse";
    public const Menus = "@menu";
    public const ViewLoader = "@viewLoader";
    /**
     * bool disable action handler 
     */
    public const NoActionHandle = "@noActionHandle";
    /**
     * bool disable view compilation 
     */
    public const NoCompilation = "@noCompilation";
    /**
     * 
     */
    public const AllowHiddenView = "@AllowHiddenView";
    /**
     * no do view reponse for request
     */
    public const NoDoViewResponse = "@NoDoViewResponse";
}