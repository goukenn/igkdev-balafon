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
* Menu host control.
* @package IGK\System\WinUI\Menus
*/
final class MenuHostControl extends IGKObject{

    /**
    * Properties: active, diseable.
    * @var mixed
    */
    private $m_active, $m_diseable;

    /**
    * .ctr
    */
    public function __construct(){    }

    /**
    * Returns Active.
    */

    public function getActive(){
        return $this->m_active;
    }

    /**
    * Returns Diseable.
    */

    public function getDiseable(){
        return $this->m_diseable;
    }

    /**
    * Sets Active.
    * @param mixed $v
    */

    public function setActive($v){
        $this->m_active=$v;
    }

    /**
    * Sets Diseable.
    * @param mixed $v
    */

    public function setDiseable($v){
        $this->m_diseable=$v;
    }
}