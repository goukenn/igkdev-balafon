<?php
// @file: MenuItemObject.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\WinUI\Menus;
use IGKObject;

/**
* Menu item object.
* @package IGK\System\WinUI\Menus
*/
class MenuItemObject extends IGKObject{

    /**
    * Properties: sub menus, uri.
    * @var mixed
    */
    private $m_subMenus, $m_uri;

    /**
    * .ctr
    * @param mixed $uri
    * @param null|mixed $submenu
    */
    public function __construct($uri, $submenu=null){
        $this->m_uri=$uri;
        $this->m_subMenus=$submenu;
    }

    /**
    * Returns Submenu.
    */

    public function getSubmenu(){
        return $this->m_subMenus;
    }

    /**
    * Returns Uri.
    */

    public function getUri(){
        return $this->m_uri;
    }
}