<?php
// @file: IGKVector2f.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Drawing;
use IGKObject;

/**
* Vector2f.
* @package IGK\System\Drawing
*/
final class Vector2f extends IGKObject{

    /**
    * Properties: x, y.
    * @var mixed
    */
    private $m_x, $m_y;

    /**
    * .ctr
    * @param mixed $x
    * @param mixed $y
    */
    public function __construct($x=0, $y=0){
        $this->m_x=$x;
        $this->m_y=$y;
    }

    /**
    * Returns string representation.
    */

    public function __toString(){
        return "IGKVector2f [x:".$this->X." y:".$this->Y."]";
    }

    /**
    * From string.
    * @param mixed $data
    */

    public static function FromString($data){
        $b=explode(";", $data);
        list($X, $Y)
        =count($b) == 2 ? $b: array($data, $data);
        return new Vector2f($X, $Y);
    }

    /**
    * Returns X.
    */

    public function getX(){
        return $this->m_x;
    }

    /**
    * Returns Y.
    */

    public function getY(){
        return $this->m_y;
    }

    /**
    * Sets X.
    * @param mixed $value
    */

    public function setX($value){
        $this->m_x=$value;
    }

    /**
    * Sets Y.
    * @param mixed $value
    */

    public function setY($value){
        $this->m_y=$value;
    }
}