<?php
// @file: Colorf.php
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

class Colorf extends IGKObject{
    private $m_A, $m_B, $m_G, $m_R;
    ///<summary></summary>
    ///<param name="cl"></param>
    ///<param name="v"></param>
    private static function __bindStringData($cl, $v){
        if ($v===null)
            return null;
        $v=trim(strtoupper($v));
        if(0===strpos($v, "#") || IGKString::StartWith($v, "0x")){
            $v=str_replace("#", IGK_STR_EMPTY, $v);
            $v=str_replace("0x", IGK_STR_EMPTY, $v);
            $i=0;
            switch(strlen($v)){
                case 8:
                break;
                case 4:
                $v=IGK_STR_EMPTY. $v[0]. $v[0]. $v[1]. $v[1]. $v[2]. $v[2]. $v[3]. $v[3];
                break;
                case 6:
                $v="FF". $v;
                break;
                case 3:
                $v="FF". $v[0]. $v[0]. $v[1]. $v[1]. $v[2]. $v[2];
                break;default: 
                break;
            }
            try {
                $i=Number::FromBase($v, 16);
                $r=(($i >> 16)& 0x00FF);
                $g=(($i >> 8)& 0x00FF);
                $b=(($i)& 0x00FF);
                $cl->m_R=$r / 255.0;
                $cl->m_G=$g / 255.0;
                $cl->m_B=$b / 255.0;
            }
            catch(Exception $ex){            }
        }
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public static function FromString($v){
        $t=igk_css_get_color_value($v);
        if(empty($t)){
            $cl=new Colorf();
            $cl->m_A=1.0;
            self::__bindStringData($cl, $v);
            return $cl;
        }
        $cl=new Colorf();
        $cl->m_A=1.0;
        self::__bindStringData($cl, $t);
        return $cl;
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
    ///<param name="v"></param>
    public function loadw($v){
        self::__bindStringData($this, $v);
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setA($value){
        if(($value>=0) && ($value<=1.0))
            $this->m_A=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setB($value){
        if(($value>=0) && ($value<=1.0))
            $this->m_B=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setG($value){
        if(($value>=0) && ($value<=1.0))
            $this->m_G=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setR($value){
        if(($value>=0) && ($value<=1.0))
            $this->m_R=$value;
    }
    ///<summary></summary>
    public function toByte(){
        return Color::FromFloat($this->R, $this->G, $this->B, $this->A);
    }
}
