<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MenuDefinition.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\WinUI\Menus;

/**
 * menu definition 
 * @package IGK\System\WinUI\Menus
 */
class MenuDefinition{
    /**
     * target uri
     * @var string
     */
    var $uri;
    /**
     * display text
     * @var string
     */
    var $text;

    /**
     * auth for menu
     * @var null|string|bool
     */
    var $auth;
}