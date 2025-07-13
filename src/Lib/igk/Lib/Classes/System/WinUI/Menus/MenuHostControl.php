<?php
// @file: IGKMenuHostControl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\WinUI\Menus;
use IGKObject;
final class MenuHostControl extends IGKObject{
    private $m_active, $m_diseable;
    public function __construct(){    }
    public function getActive(){
        return $this->m_active;
    }
    public function getDiseable(){
        return $this->m_diseable;
    }
    public function setActive($v){
        $this->m_active=$v;
    }
    public function setDiseable($v){
        $this->m_diseable=$v;
    }
}