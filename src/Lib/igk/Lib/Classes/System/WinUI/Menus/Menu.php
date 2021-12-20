<?php
// @file: IGKMenu.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\WinUI\Menus;

use IGKObject;

final class Menu extends IGKObject{
    public static $sm_menus;
    var $Name, $m_menus;
    ///<summary></summary>
    ///<param name="name"></param>
    public function __construct($name){
        $this->Name=$name;
        $this->m_menus=array();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function addMenu($name){
        $n=new MenuItem($name, null, null);
        $this->m_menus[$name]=$n;
        return $n;
    }
    ///<summary></summary>
    public function getMenuFile(){
        return igk_io_basedir(IGK_DATA_FOLDER. "/menu".$this->Name."conf.csv");
    }
    ///<summary></summary>
    public static function GetMenus(){
        return array();
    }
}
