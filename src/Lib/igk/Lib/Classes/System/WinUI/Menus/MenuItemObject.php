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
* auto generate doc.
* @package IGK\System\WinUI\Menus
*/
class MenuItemObject extends IGKObject{

    /**
    * auto generate doc.
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
    * auto generate doc.
    */

    public function getSubmenu(){
        return $this->m_subMenus;
    }

    /**
    * auto generate doc.
    */

    public function getUri(){
        return $this->m_uri;
    }
}