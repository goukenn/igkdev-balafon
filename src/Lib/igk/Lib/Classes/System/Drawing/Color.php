<?php
// @file: IGKColor.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Drawing;
use IGK\System\Number;
use IGKObject;

/**
* auto generate doc.
* @package IGK\System\Drawing
*/
class Color extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_A, $m_B, $m_G, $m_R;

    /**
    * .ctr
    * @param mixed $r
    * @param mixed $g
    * @param mixed $b
    * @param mixed $a
    */
    public function __construct($r, $g, $b, $a){
        $this->m_R=self::trimByte($r);
        $this->m_G=self::trimByte($g);
        $this->m_B=self::trimByte($b);
        $this->m_A=self::trimByte($a);
    }

    /**
    * auto generate doc.
    */

    public static function Black(){
        return self::FromFloat(0.0);
    }

    /**
    * auto generate doc.
    * @param mixed $rgb
    * @param null|mixed $g
    * @param null|mixed $b
    * @param null|mixed $a
    */

    public static function FromFloat($rgb, $g=null, $b=null, $a=null){
        if($g === null)
            return new Color($rgb * 255, $rgb * 255, $rgb * 255, 255);
        return new Color($rgb * 255, $g * 255, $b * 255, $a * 255);
    }

    /**
    * auto generate doc.
    * @param mixed $s
    */

    public static function FromString($s){
        $c=Colorf::FromString($s);
        return self::FromFloat($c->R, $c->G, $c->B, 255);
    }

    /**
    * auto generate doc.
    */

    public function getA(){
        return $this->m_A;
    }

    /**
    * auto generate doc.
    */

    public function getB(){
        return $this->m_B;
    }

    /**
    * auto generate doc.
    */

    public function getG(){
        return $this->m_G;
    }

    /**
    * auto generate doc.
    */

    public function getR(){
        return $this->m_R;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setA($value){
        if(($value>=0) && ($value<=255))
            $this->m_A=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setB($value){
        if(($value>=0) && ($value<=255))
            $this->m_B=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setG($value){
        if(($value>=0) && ($value<=255))
            $this->m_G=$value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setR($value){
        if(($value>=0) && ($value<=255))
            $this->m_R=$value;
    }
    /**
     * convert to web color
     * @return string 
     */

    public function toWebColor(){
        if($this->m_A != 255){
            return "rgba(".$this->m_R.",".$this->m_G.",".$this->m_B.",".(($this->m_A * 100)/255). ")";
        }
        else{
            return "#". Number::ToBase($this->m_R, 16, 2).Number::ToBase($this->m_G, 16, 2).Number::ToBase($this->m_B, 16, 2);
        }
    }
    private static function trimByte($a){
        return max(min($a, 255), 0);
    }

    /**
    * auto generate doc.
    */

    public static function White(){
        return self::FromFloat(1.0);
    }
}