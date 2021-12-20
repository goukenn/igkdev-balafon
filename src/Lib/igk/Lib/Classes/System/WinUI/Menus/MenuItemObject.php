<?php
// @file: MenuItemObject.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com


namespace IGK\System\WinUI\Menus;

use IGKObject;

class MenuItemObject extends IGKObject{
    private $m_subMenus, $m_uri;
    ///<summary></summary>
    ///<param name="uri"></param>
    ///<param name="submenu" default="null"></param>
    public function __construct($uri, $submenu=null){
        $this->m_uri=$uri;
        $this->m_subMenus=$submenu;
    }
    ///<summary></summary>
    public function getSubmenu(){
        return $this->m_subMenus;
    }
    ///<summary></summary>
    public function getUri(){
        return $this->m_uri;
    }
}
