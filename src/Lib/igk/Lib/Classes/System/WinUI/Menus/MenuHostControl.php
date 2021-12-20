<?php
// @file: IGKMenuHostControl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\WinUI\Menus;

use IGKObject;

final class MenuHostControl extends IGKObject{
    private $m_active, $m_diseable;
    ///<summary></summary>
    public function __construct(){    }
    ///<summary></summary>
    public function getActive(){
        return $this->m_active;
    }
    ///<summary></summary>
    public function getDiseable(){
        return $this->m_diseable;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setActive($v){
        $this->m_active=$v;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function setDiseable($v){
        $this->m_diseable=$v;
    }
}
