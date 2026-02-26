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
* auto generate doc.
* @package IGK\System\Drawing
*/
final class Vector2f extends IGKObject{
    private $m_x, $m_y;
    public function __construct($x=0, $y=0){
        $this->m_x=$x;
        $this->m_y=$y;
    }

    /**
    * auto generate doc.
    */
    public function __toString(){
        return "IGKVector2f [x:".$this->X." y:".$this->Y."]";
    }

    /**
    * auto generate doc.
    * @param mixed $data
    */
    public static function FromString($data){
        $b=explode(";", $data);
        list($X, $Y)
        =count($b) == 2 ? $b: array($data, $data);
        return new Vector2f($X, $Y);
    }

    /**
    * auto generate doc.
    */
    public function getX(){
        return $this->m_x;
    }

    /**
    * auto generate doc.
    */
    public function getY(){
        return $this->m_y;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setX($value){
        $this->m_x=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setY($value){
        $this->m_y=$value;
    }
}