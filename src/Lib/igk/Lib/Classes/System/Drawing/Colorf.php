<?php
// @file: Colorf.php
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
use function igk_clamp as clamp;

/**
* Colorf.
* @package IGK\System\Drawing
*/
class Colorf extends IGKObject
{
    /**
    * Properties: a, b, g, r.
    * @var mixed
    */
    private $m_A, $m_B, $m_G, $m_R;
    /**
    * auto generate doc.
    * @param mixed $cl
    * @param mixed $v
    * @return
    */
    private static function __bindStringData($cl, $v)
    {
        if ($v === null)
            return null;
        $v = trim(strtoupper($v));
        if (0 === strpos($v, "#") || IGKString::StartWith($v, "0x")) {
            list($r, $g, $b, $a) = self::ConvertStringToRGBA($v);
            $cl->m_A = $a;
            $cl->m_R = $r;
            $cl->m_G = $g;
            $cl->m_B = $b; 
        }
    }
    /**
    * auto generate doc.
    * @param string $v
    * @return void
    */
    public static function ConvertStringToRGBA(string $v)
    {
        $v = str_replace("#", IGK_STR_EMPTY, $v);
        $v = str_replace("0x", IGK_STR_EMPTY, $v);
        $i = 0;
        switch (strlen($v)) {
            case 8:
                break;
            case 4:
                $v = IGK_STR_EMPTY . $v[0] . $v[0] . $v[1] . $v[1] . $v[2] . $v[2] . $v[3] . $v[3];
                break;
            case 6:
                $v = "FF" . $v;
                break;
            case 3:
                $v = "FF" . $v[0] . $v[0] . $v[1] . $v[1] . $v[2] . $v[2];
                break;
            default:
                break;
        }
        $a = 1;
        $r = $g = $b = 0;
        try {
            $i = Number::FromBase($v, 16);
            $a = (($i >> 24) & 0x00FF);
            $r = (($i >> 16) & 0x00FF);
            $g = (($i >> 8) & 0x00FF);
            $b = (($i) & 0x00FF);
            $a = clamp($a / 255.0, 1.0);
            $r = clamp($r / 255.0, 1.0);
            $g = clamp($g / 255.0, 1.0);
            $b = clamp($b / 255.0, 1.0);
        } catch (Exception $ex) {
        }
        return [$r, $g, $b, $a];
    }
    /**
    * From string.
    * @param mixed $v
    */
    public static function FromString($v)
    {
        $t = igk_css_get_color_value($v);
        if (empty($t)) {
            $cl = new Colorf();
            $cl->m_A = 1.0;
            self::__bindStringData($cl, $v);
            return $cl;
        }
        $cl = new Colorf();
        $cl->m_A = 1.0;
        self::__bindStringData($cl, $t);
        return $cl;
    }
    /**
    * Returns A.
    */
    public function getA()
    {
        return $this->m_A;
    }
    /**
    * Returns B.
    */
    public function getB()
    {
        return $this->m_B;
    }
    /**
    * Returns G.
    */
    public function getG()
    {
        return $this->m_G;
    }
    /**
    * Returns R.
    */
    public function getR()
    {
        return $this->m_R;
    }
    /**
    * Loadw.
    * @param mixed $v
    */
    public function loadw($v)
    {
        self::__bindStringData($this, $v);
    }
    /**
    * Sets A.
    * @param mixed $value
    */
    public function setA($value)
    {
        if (($value >= 0) && ($value <= 1.0))
            $this->m_A = $value;
    }
    /**
    * Sets B.
    * @param mixed $value
    */
    public function setB($value)
    {
        if (($value >= 0) && ($value <= 1.0))
            $this->m_B = $value;
    }
    /**
    * Sets G.
    * @param mixed $value
    */
    public function setG($value)
    {
        if (($value >= 0) && ($value <= 1.0))
            $this->m_G = $value;
    }
    /**
    * Sets R.
    * @param mixed $value
    */
    public function setR($value)
    {
        if (($value >= 0) && ($value <= 1.0))
            $this->m_R = $value;
    }
    /**
     * convert to byte data 
     * @return Color
     */
    public function toByte(): Color
    {
        return Color::FromFloat($this->R, $this->G, $this->B, $this->A);
    }
    /**
     * convert to webcolor 
     * @return string 
     */
    public function toWebColor(): string{
        return Color::FromFloat($this->R, $this->G, $this->B, $this->A)->toWebColor();
    }
}