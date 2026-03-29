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
/**
* Rectanglef.
* @package IGK\System\Drawing
*/
final class Rectanglef extends IGKObject{
    /**
    * Properties: h, w, x, y.
    * @var mixed
    */
    private $m_h, $m_w, $m_x, $m_y;
    /**
     * Constructor.
     *
     * @param float $x      The x-coordinate of the rectangle.
     * @param float $y      The y-coordinate of the rectangle.
     * @param float $width  The width of the rectangle.
     * @param float $height The height of the rectangle.
     */
    public function __construct($x=0, $y=0, $width=0, $height=0){
        $this->m_x=$x;
        $this->m_y=$y;
        $this->m_w=$width;
        $this->m_h=$height;
    }
    /**
     * Returns a string representation of the rectangle.
     *
     * @return string
     */
    public function __toString(){
        return "Rectanglef [x:".$this->X." y:".$this->Y."; width: ".$this->Width." ;height: ".$this->Height."]";
    }
    /**
     * Gets the height of the rectangle.
     *
     * @return float
     */
    public function getHeight(){
        return $this->m_h;
    }
    /**
     * Gets the width of the rectangle.
     *
     * @return float
     */
    public function getWidth(){
        return $this->m_w;
    }
    /**
     * Gets the x-coordinate of the rectangle.
     *
     * @return float
     */
    public function getX(){
        return $this->m_x;
    }
    /**
     * Gets the y-coordinate of the rectangle.
     *
     * @return float
     */
    public function getY(){
        return $this->m_y;
    }
    /**
     * Sets the height of the rectangle.
     *
     * @param float $value The new height value.
     */
    public function setHeight($value){
        $this->m_h=$value;
    }
    /**
     * Sets the width of the rectangle.
     *
     * @param float $value The new width value.
     */
    public function setWidth($value){
        $this->m_w=$value;
    }
    /**
     * Sets the x-coordinate of the rectangle.
     *
     * @param float $value The new x-coordinate value.
     */
    public function setX($value){
        $this->m_x=$value;
    }
    /**
     * Sets the y-coordinate of the rectangle.
     *
     * @param float $value The new y-coordinate value.
     */
    public function setY($value){
        $this->m_y=$value;
    }
}