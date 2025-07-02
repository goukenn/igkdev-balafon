<?php
// @file: IGKRectanglef.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Drawing;

use Exception;
use IGKObject;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Number;

final class Rectanglef extends IGKObject{
    private $m_h, $m_w, $m_x, $m_y;
    public function __construct($x=0, $y=0, $width=0, $height=0){
        $this->m_x=$x;
        $this->m_y=$y;
        $this->m_w=$width;
        $this->m_h=$height;
    }
    public function __toString(){
        return "Rectanglef [x:".$this->X." y:".$this->Y."; width: ".$this->Width." ;height: ".$this->Height."]";
    }
    public function getHeight(){
        return $this->m_h;
    }
    public function getWidth(){
        return $this->m_w;
    }
    public function getX(){
        return $this->m_x;
    }
    public function getY(){
        return $this->m_y;
    }
    public function setHeight($value){
        $this->m_h=$value;
    }
    public function setWidth($value){
        $this->m_w=$value;
    }
    public function setX($value){
        $this->m_x=$value;
    }
    public function setY($value){
        $this->m_y=$value;
    }
}
