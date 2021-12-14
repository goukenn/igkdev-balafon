<?php
// @file: IGKVector2f.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Drawing;

use IGKObject;

final class Vector2f extends IGKObject{
    private $m_x, $m_y;
    ///<summary></summary>
    ///<param name="x"></param>
    ///<param name="y"></param>
    public function __construct($x=0, $y=0){
        $this->m_x=$x;
        $this->m_y=$y;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return "IGKVector2f [x:".$this->X." y:".$this->Y."]";
    }
    ///<summary></summary>
    ///<param name="data"></param>
    public static function FromString($data){
        $b=explode(";", $data);
        list($X, $Y)
        =count($b) == 2 ? $b: array($data, $data);
        return new Vector2f($X, $Y);
    }
    ///<summary></summary>
    public function getX(){
        return $this->m_x;
    }
    ///<summary></summary>
    public function getY(){
        return $this->m_y;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setX($value){
        $this->m_x=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setY($value){
        $this->m_y=$value;
    }
}
