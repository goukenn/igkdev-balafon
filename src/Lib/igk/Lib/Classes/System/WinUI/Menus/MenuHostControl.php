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

/**
* auto generate doc.
* @package IGK\System\WinUI\Menus
*/
final class MenuHostControl extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_active, $m_diseable;

    /**
    * .ctr
    */
    public function __construct(){    }

    /**
    * auto generate doc.
    */

    public function getActive(){
        return $this->m_active;
    }

    /**
    * auto generate doc.
    */

    public function getDiseable(){
        return $this->m_diseable;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setActive($v){
        $this->m_active=$v;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setDiseable($v){
        $this->m_diseable=$v;
    }
}