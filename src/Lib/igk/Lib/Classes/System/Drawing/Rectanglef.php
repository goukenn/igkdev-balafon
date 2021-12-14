<?php
// @file: IGKRectanglef.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Drawing;

use Exception;
use IGKObject;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Number;

final class Rectanglef extends IGKObject{
    private $m_h, $m_w, $m_x, $m_y;
    ///<summary></summary>
    ///<param name="x"></param>
    ///<param name="y"></param>
    ///<param name="width"></param>
    ///<param name="height"></param>
    public function __construct($x=0, $y=0, $width=0, $height=0){
        $this->m_x=$x;
        $this->m_y=$y;
        $this->m_w=$width;
        $this->m_h=$height;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return "Rectanglef [x:".$this->X." y:".$this->Y."; width: ".$this->Width." ;height: ".$this->Height."]";
    }
    ///<summary></summary>
    public function getHeight(){
        return $this->m_h;
    }
    ///<summary></summary>
    public function getWidth(){
        return $this->m_w;
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
    public function setHeight($value){
        $this->m_h=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setWidth($value){
        $this->m_w=$value;
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
