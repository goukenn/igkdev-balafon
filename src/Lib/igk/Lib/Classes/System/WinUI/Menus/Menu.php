<?php
// @file: IGKMenu.php
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
* Menu.
* @package IGK\System\WinUI\Menus
*/
final class Menu extends IGKObject{

    /**
    * Property: menus.
    * @var mixed
    */
    public static $sm_menus;

    /**
    * Properties: name, menus.
    * @var mixed
    */
    var $Name, $m_menus;

    /**
    * .ctr
    * @param mixed $name
    */
    public function __construct($name){
        $this->Name=$name;
        $this->m_menus=array();
    }

    /**
    * Adds Menu.
    * @param mixed $name
    */

    public function addMenu($name){
        $n=new MenuItem($name, null, null);
        $this->m_menus[$name]=$n;
        return $n;
    }

    /**
    * Returns Menu File.
    */

    public function getMenuFile(){
        return igk_io_basedir(IGK_DATA_FOLDER. "/menu".$this->Name."conf.csv");
    }

    /**
    * Returns Menus.
    */

    public static function GetMenus(){
        return array();
    }
}