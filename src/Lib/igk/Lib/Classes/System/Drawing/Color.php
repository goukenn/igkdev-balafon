<?php
// @file: IGKColor.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Drawing;

use IGK\System\Number;
use IGKObject;

class Color extends IGKObject{
    private $m_A, $m_B, $m_G, $m_R;
    ///<summary></summary>
    ///<param name="r"></param>
    ///<param name="g"></param>
    ///<param name="b"></param>
    ///<param name="a"></param>
    public function __construct($r, $g, $b, $a){
        $this->m_R=self::trimByte($r);
        $this->m_G=self::trimByte($g);
        $this->m_B=self::trimByte($b);
        $this->m_A=self::trimByte($a);
    }
    ///<summary></summary>
    public static function Black(){
        return self::FromFloat(0.0);
    }
    ///<summary></summary>
    ///<param name="rgb"></param>
    ///<param name="g" default="null"></param>
    ///<param name="b" default="null"></param>
    ///<param name="a" default="null"></param>
    public static function FromFloat($rgb, $g=null, $b=null, $a=null){
        if($g === null)
            return new Color($rgb * 255, $rgb * 255, $rgb * 255, 255);
        return new Color($rgb * 255, $g * 255, $b * 255, $a * 255);
    }
    ///<summary></summary>
    ///<param name="s"></param>
    public static function FromString($s){
        $c=Colorf::FromString($s);
        return self::FromFloat($c->R, $c->G, $c->B, 255);
    }
    ///<summary></summary>
    public function getA(){
        return $this->m_A;
    }
    ///<summary></summary>
    public function getB(){
        return $this->m_B;
    }
    ///<summary></summary>
    public function getG(){
        return $this->m_G;
    }
    ///<summary></summary>
    public function getR(){
        return $this->m_R;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setA($value){
        if(($value>=0) && ($value<=255))
            $this->m_A=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setB($value){
        if(($value>=0) && ($value<=255))
            $this->m_B=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setG($value){
        if(($value>=0) && ($value<=255))
            $this->m_G=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setR($value){
        if(($value>=0) && ($value<=255))
            $this->m_R=$value;
    }
    ///<summary>Represente toWebColor function</summary>
    public function toWebColor(){
        if($this->m_A != 255){
            return "rgba(".$this->m_R.",".$this->m_G.",".$this->m_B.",".(($this->m_A * 100)/255). ")";
        }
        else{
            return "#". Number::ToBase($this->m_R, 16, 2).Number::ToBase($this->m_G, 16, 2).Number::ToBase($this->m_B, 16, 2);
        }
    }
    ///<summary>Represente trimByte function</summary>
    ///<param name="a"></param>
    private static function trimByte($a){
        return max(min($a, 255), 0);
    }
    ///<summary></summary>
    public static function White(){
        return self::FromFloat(1.0);
    }
}
