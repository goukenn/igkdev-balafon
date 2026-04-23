<?php
// @author: C.A.D. BONDJE DOUE
// @file: MenuItemInfo.php
// @date: 20240111 17:07:35
namespace IGK\System\WinUI\Menus;

/**
* 
* @package IGK\System\WinUI\Menus
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\WinUI\Menus
*/
class MenuItemInfo{
    /**
     * identifier of the menu
     * @var ?
     */
    var $id;
    /**
    * auto generate doc.
    * @var ?string text to display
    */
    var $text;
    /**
     * icon to display 
     * @var mixed
     */
    var $icon;
    /**
     * link of the menu item
     * @var mixed
     */
    var $uri;
    /**
     * is ajax link
     * @var ?bool
     */
    var $ajx;
    /**
    * auto generate doc.
    * @var ?bool authorized
    */
    var $auth;
    /**
     * class name definition
     * @var ?string|array
     */
    var $class;
}