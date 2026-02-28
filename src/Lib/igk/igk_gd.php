<?php
// @author: C.A.D. BONDJE DOUE
// licence: IGKDEV - Balafon @ 2019
// desc: gd utility class

use IGK\System\Drawing\Color;
use IGK\System\Drawing\Colorf;
use IGK\System\Drawing\Rectanglef;
use Safe\Exceptions\ImageException;

use function igk_clamp as clamp; 
 
if (!extension_loaded("gd")) {
    return;
}
define("IGK_GD_SUPPORT", 1);



/**
 * resize proportional 
 * @param string $src image data to get from
 * @param int $w
 * @param int $h
 * @param mixed $type the default value is 1. 1 = png, other value is for jpeg
 * @param mixed $compression from 0-100 the default value is 0= no compression
 * @param bool $antialias activate or not antialize on image
 * @param bool $cover activate or not antialize on image
 */
function igk_gd_resize_proportional($src, $w, $h, $type = 1, $compression = 0, bool $antialias = false, $cover=false, $notransparent=null)
{
    $notransparent = $notransparent ?? (($type==1) && $cover);
    $ih = imagecreatefromstring($src);
    $W = imagesx($ih);
    $H = imagesy($ih);  
    $ex = $w / $W;
    $ey = $h / $H;
    $x = 0; $y=0;
    $dx = $dy = 0;
    if (!$cover){
        $ex = min($ex, $ey);
        $x = intval(ceil(((-$W * $ex) + $w) / 2.0));
        $y = intval(ceil(((-$H * $ex) + $h) / 2.0));
    } else {
        $ex = max($ex, $ey); 
        $x = intval(ceil(((-$W * $ex) / 2.0 ) + ($w/2.0)));   
        $y = intval(ceil(((-$H * $ex) / 2.0 ) + ($h/2.0)));   
        $dx =   -$x;
        $dy =  0 -$y;
        // $x = intval(ceil(((-$W ) + ($w* $ex)) / 2.0));
        // $y = intval(ceil(((-$H ) + ($h* $ex)) / 2.0));
    }

    $img = imagecreatetruecolor($w, $h);
    $black = imagecolorallocate($img, 0, 0, 0);
    if (!$notransparent){
        imagecolortransparent($img, $black);
    }
    imageantialias($img, $antialias);

    $sh = imagescale($ih, ceil($ex * $W), ceil($ex * $H));
    imagecopy($img, $sh, $x, $y, 0, 0, $w+$dx, $h+$dy);
    $g = igk_ob_get_func(function ($t) use (&$img, $compression) {
        if ($t == 1) {
            $clevel = 9 - ($compression * 9 / 100);
            echo imagepng($img, null, $clevel);
        } else {
            echo imagejpeg($img, null, $compression);
        }
    }, $type);
    imagedestroy($sh);
    imagedestroy($ih);
    imagedestroy($img);
    return $g;
}
/**
 * Represent IGKGD class
 */
class IGKGD
{

    /**
    * Property: height.
    * @var mixed
    */
    private $m_height;

    /**
    * Property: himg.
    * @var mixed
    */
    private $m_himg;

    /**
    * Property: width.
    * @var mixed
    */
    private $m_width;
    /**
     * transparent color 
     * @var ?int
     */
    private $m_transparentColor;

    /**
    * auto generate doc.
    * @param mixed $himg
    */
    private function __construct($w, $h, $himg)
    {
        $this->m_width = $w;
        $this->m_height = $h;
        $this->m_himg = $himg;
    }

    /**
    * auto generate doc.
    * @return
    */
    public function getWidth()
    {
        if ($this->m_width == -1)
            $this->m_width = imagesx($this->m_himg);
        return $this->m_width;
    }

    /**
    * Returns Height.
    */
    public function getHeight()
    {
        if ($this->m_height == -1)
            $this->m_height = imagesy($this->m_himg);
        return $this->m_height;
    }
    /**
     * enable antialias
     * @param mixed $b 
     * @return bool 
     */

    public function setAntialias(bool $b)
    {
        $r = imageantialias($this->m_himg, $b);
        return $r;
    }
    /**
     * set alpha blending
     * @param bool $b 
     * @return void 
     */

    public function setAlphaBlending($b)
    {
        imagealphablending($this->m_himg, $b);
    }
    /**
     * clear with color byte object
     * @param mixed $color object R,G,B byte
     */

    public function clear($color)
    {
        $hcl = imagecolorallocate($this->m_himg, $color->R, $color->G, $color->B);
        imagefill($this->m_himg, 0, 0, $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }
    /**
     * clear with float color value 
     * @param mixed|float|array|string $color
     */

    public function clearf($color)
    {
        if (is_string($color) && !empty($color)) {
            $color = Colorf::FromString($color);
        } else if (is_array($color)) {
            $color = (object)array_combine(['R', 'G', 'B'], array_values($color));
        } else if (is_numeric($color)) {
            $color = (object)array_fill_keys(['R', 'G', 'B'], $color);
        }
        $this->clear((object)array(
            "R" => $color->R * 255,
            "G" => $color->G * 255,
            "B" => $color->B * 255
        ));
    }
    /**
     * create a color
     * @param mixed|string|array $color color name | float array of color
     * @return int
     */

    protected function _createColorf($color)
    {
        if (is_string($color))
            $color = Color::FromString($color);
        else if (is_array($color)) {
            $color = (object)array_map(function ($a) {
                return round($a * 255);
            }, array_combine(['R', 'G', 'B'], array_values($color)));
        }
        $hcl = imagecolorallocate($this->m_himg, $color->R, $color->G, $color->B);
        return $hcl;
    }
    /**
     * clear with web color
     * @param mixed $webcolor
     */

    public function Clearw($webcolor)
    {
        $this->clearf(Colorf::FromString($webcolor));
    }
    /**
     * create color Object
     * @param mixed $R byte red color component
     * @param mixed $G byte green color component
     * @param mixed $B byte yellow color component
     * @return object 
     */

    public static function CreateColorRGB($R, $G, $B)
    {
        return (object)compact('R', 'G', 'B');
    }
    /**
     * create color Object
     * @param mixed $R byte red color component
     * @param mixed $G byte green color component
     * @param mixed $B byte yellow color component
     * @return object 
     */

    public static function CreateColorfRGB($R, $G, $B)
    {
        $R = clamp($R * 255.0, 255);
        $G = clamp($G * 255.0, 255);
        $B = clamp($B * 255.0, 255);
        return (object)compact('R', 'G', 'B');
    }
    /**
     * create a IGKGD instance 
     * @param mixed $imgwidth
     * @param mixed $imgheight
     * @return static
     */

    public static function Create($imgwidth, $imgheight)
    {
        if (function_exists("imagecreatetruecolor")) {
            $v_img = imagecreatetruecolor($imgwidth, $imgheight);
            if (is_object($v_img) || is_resource($v_img)) {
                return new self($imgwidth, $imgheight, $v_img);
            }
        } else
            igk_ilog("no imagecreateturecolor  function found");
        return null;
    }

    /**
    * Creates From File.
    * @param mixed $filename
    */
    public static function CreateFromFile($filename)
    {
        if (igk_io_file_exists($filename) && function_exists("imagecreatefromstring")) {
            $g = imagecreatefromstring(file_get_contents($filename));
            if ($g) {
                $w = -1;
                $h = -1;
                return new IGKGD($w, $h, $g);
            }
        }
        return null;
    }
    /**
     * render inline gd
     * @return string 
     */

    public function renderURL()
    {
        return "data:image/png;base64," . base64_encode(igk_ob_get_func(function () {
            $this->render();
        }));
    }

    /**
    * Creates Buffer.
    */
    public function CreateBuffer()
    {
        $c = self::Create($this->getWidth(), $this->getHeight());
        $c->clear((object)["R" => 255, "G" => 255, "B" => 255]);
        // $cl = imagecolorallocatealpha ($c->m_himg,0,255,0, 100);
        $tcl = imagecolorallocate($c->m_himg, 255, 255, 255);
        // imagecolordeallocate($c->m_himg, $cl);
        imagealphablending($c->m_himg, true);
        imagecolortransparent($c->m_himg, $tcl);
        // imagecolordeallocate($c->m_himg, $tcl);
        return $c;
    }
    /**
     * set transparent color
     * @param mixed $color 
     * @return void 
     */

    public function setTransparentColor($color)
    {
        if (!is_null($this->m_transparentColor)) {
            imagecolordeallocate($this->m_himg, $this->m_transparentColor);
            $this->m_transparentColor = null;
        }
        $hcl = $this->_createColorf($color);
        imagecolortransparent($this->m_himg, $hcl);
        $this->m_transparentColor = $hcl;
    }

    /**
    * auto generate doc.
    */
    public function Dispose()
    {
        imagedestroy($this->m_himg);
    }

    /**
    * Draws Line.
    * @param mixed $color
    * @param mixed $x1
    * @param mixed $y1
    * @param mixed $x2
    * @param mixed $y2
    */
    public function DrawLine($color, $x1, $y1, $x2, $y2)
    {
        $hcl = $this->_createColorf($color);
        imageline($this->m_himg, $x1, $y1, $x2, $y2, $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }

    /**
    * auto generate doc.
    * @param mixed $radius
    */

    public function DrawEllipse($color, $center, $radius)
    {
        $hcl = $this->_createColorf($color);
        imageellipse($this->m_himg, $center->X, $center->Y, abs($radius->X * 2.0), abs($radius->Y * 2.0), $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }

    /**
    * auto generate doc.
    * @param mixed $h the default value is -1
    */

    public function DrawImage($himg, $x, $y, $w = -1, $h = -1)
    {
        if (get_class($himg) === __CLASS__) {
            $himg = $himg->m_himg;
        }

        $rs = (($w == -1) && ($h == -1));
        $w = ($w == -1) ? imagesx($himg) : $w;
        $h = ($h == -1) ? imagesy($himg) : $h;
        if (!$rs) {
            //$img=imagecreatetruecolor($w, $h);
            $sh = imagescale($himg, $w, $h);
            imagecopy($this->m_himg, $sh, $x, $y, 0, 0, $w, $h);
            //imagedestroy($img);
            imagedestroy($sh);
        } else {

            imagealphablending($himg, false);
            imagealphablending($this->m_himg, false);
            imagecopy($this->m_himg, $himg, $x, $y, 0, 0, $w, $h);
        }
    }

    /**
    * Fill image.
    * @param mixed $himg
    * @param mixed $x
    * @param mixed $y
    * @param mixed $w
    * @param mixed $h
    */
    public function FillImage($himg, $x, $y, $w = -1, $h = -1)
    {
        $this->DrawImage($himg, $x, $y, $w, $h);
        return;

        if (get_class($himg) === __CLASS__) {
            $himg = $himg->m_himg;
        }

        $rs = (($w == -1) && ($h == -1));
        $w = ($w == -1) ? imagesx($himg) : $w;
        $h = ($h == -1) ? imagesy($himg) : $h;
        if (!$rs) {
            //$img=imagecreatetruecolor($w, $h);
            $sh = imagescale($himg, $w, $h);
            imagecopymerge($this->m_himg, $sh, $x, $y, 0, 0, $w, $h, 50);
            //imagedestroy($img);
            imagedestroy($sh);
        } else
            imagecopymerge($this->m_himg, $himg, $x, $y, 0, 0, $w, $h, 10);
    }

    /**
    * auto generate doc.
    * @param mixed $height the default value is null
    */

    public function DrawRectangle($color, $rect, $y = null, $width = null, $height = null)
    {
        if (is_string($color))
            $color = Color::FromString($color);
        if (!is_object($rect)) {
            $rect = new Rectanglef($rect, $y, $width, $height);
        }
        $hcl = imagecolorallocate($this->m_himg, $color->R, $color->G, $color->B);
        imagerectangle($this->m_himg, $rect->X, $rect->Y, $rect->X + $rect->Width, $rect->y + $rect->Height, $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }

    /**
    * Scale.
    * @param mixed $scalex
    * @param mixed $scaley
    */
    public function Scale($scalex, $scaley) {}

    /**
    * auto generate doc.
    * @param mixed $color
    */

    public function DrawString($string, $font, $size, $x, $y, $color)
    {
        $hcl = imagecolorallocate($this->m_himg, $color->R, $color->G, $color->B);
        $r = imagefttext($this->m_himg, $size, 0, $x, $y, $hcl, $font, $string);
        imagecolordeallocate($this->m_himg, $hcl);
        return (object)array(
            "x" => $r[0],
            "y" => $r[7],
            "width" => abs($r[0] - $r[4]),
            "height" => abs($r[5] - $r[1])
        );
    }

    /**
    * auto generate doc.
    * @param mixed $radius
    */

    public function fillEllipse($color, $center, $radius)
    {
        $hcl = $this->_createColorf($color);
        imagefilledellipse($this->m_himg, $center->X, $center->Y, abs($radius->X * 2.0), abs($radius->Y * 2.0), $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }

    /**
    * auto generate doc.
    * @param mixed $height the default value is null
    */

    public function fillRectangle($color, $rectx, $y = null, $width = null, $height = null)
    {
        $hcl = $this->_createColorf($color);
        if (!is_object($rectx)) {
            $rectx = new Rectanglef($rectx, $y, $width, $height);
        }
        $x = intval(ceil($rectx->X));
        $y = intval(ceil($rectx->Y));
        $w = intval(ceil($rectx->X + $rectx->Width));
        $h = intval(ceil($rectx->Y + $rectx->Height));
        imagefilledrectangle($this->m_himg, $x, $y, $w, $h, $hcl);
        imagecolordeallocate($this->m_himg, $hcl);
    }

    /**
    * auto generate doc.
    * @param mixed $himg
    */

    public static function FromGd($himg)
    {
        return new IGKGD(imagesx($himg), imagesy($himg), $himg);
    }
    /**
     * output the image
     * @param string|null|1| $type null
     */

    public function render($type = null, $quality = null)
    {
        if (($type === null) || preg_match('/png/i', $type))
            return imagepng($this->m_himg);
        return imagejpeg($this->m_himg, null, $quality);
    }

    /**
    * auto generate doc.
    */
    public function renderText()
    {
        ob_start();
        $this->render();
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    }

    /**
    * Sets Line Width.
    * @param mixed $size
    */
    public function setLineWidth($size)
    {
        imagesetthickness($this->m_himg, $size);
    }

    /**
    * Returns Clip.
    */
    public function getClip()
    {
        return imagegetclip($this->m_himg); //, $x, $y, $x+ $w, $y+$h);
    }

    /**
    * Clip.
    * @param mixed $x
    * @param mixed $y
    * @param mixed $w
    * @param mixed $h
    */
    public function clip($x, $y, $w, $h)
    {
        imagesetclip($this->m_himg, $x, $y, $x + $w, $y + $h);
    }

    /**
    * Resetclip.
    */
    public function resetclip()
    {
        imagesetclip($this->m_himg, 0, 0, imagesx($this->m_himg), imagesy($this->m_himg));
    }

    /**
    * Fill polygon.
    * @param mixed $points
    * @param mixed $color
    */
    public function FillPolygon($points, $color)
    {
        $pt = count($points) / 2;
        if (!($pt >= 3)) {
            return;
        }
        $allocColor = $this->_allocColor($color);
        imagefilledpolygon($this->m_himg, $points, $pt, $allocColor);
        imagecolordeallocate($this->m_himg, $allocColor);
    }

    /**
    * Draws Polygon.
    * @param mixed $points
    * @param mixed $color
    */
    public function DrawPolygon($points, $color)
    {
        $pt = count($points) / 2;
        if (!($pt >= 3)) {
            return;
        }
        $allocColor = $this->_allocColor($color);
        imagepolygon($this->m_himg, $points, $pt, $allocColor);
        imagecolordeallocate($this->m_himg, $allocColor);
    }

    /**
    * auto generate doc.
    * @param mixed $color
    * @return
    */
    private function _allocColor($color)
    {
        if (is_string($color))
            $color = Color::FromString($color);
        else if (is_array($color)) {
            $color = (object)[
                'R' => clamp($color[0] * 255.0, 255),
                'G' => clamp($color[1] * 255.0, 255),
                'B' => clamp($color[2] * 255.0, 255)
            ];
        }
        if (is_null($color)) {
            return null;
        }
        $hcl = imagecolorallocate($this->m_himg,  $color->R, $color->G, $color->B);
        return $hcl;
    }

    /**
    * Alloc color.
    * @param mixed $color
    */
    public function allocColor($color)
    {
        return $this->_allocColor($color);
    }

    /**
     * draw rectangle
     * @param mixed $x 
     * @param mixed $y 
     * @param mixed $width 
     * @param mixed $height 
     * @param mixed $color_id 
     * @return void 
     */

    public function rect($x, $y, $width, $height, $color_id, $fill = 0)
    {
        if (is_object($color_id)){
            $color_id = $this->_allocColor($color_id);
        }
        if ($fill) {
            imagefilledrectangle($this->m_himg, $x, $y, $x + $width, $y + $height, $color_id);
        } else
            imagerectangle($this->m_himg, $x, $y, $x + $width, $y + $height, $color_id);
    }
    /**
     * draw polygon
     * @param mixed $points 
     * @param mixed $color_or_brush_res 
     * @param int $fill 
     * @return void 
     */

    public function polygon($points, $color_or_brush_res, $fill = 0)
    {
        // + | --------------------------------------------------------------------
        // + | deprecated drawing do not set color
        // + | 
        if ($fill) {
            imagefilledpolygon($this->m_himg, $points, $color_or_brush_res);
        } else
            imagepolygon($this->m_himg, $points, $color_or_brush_res);
    }

    /**
    * Text.
    * @param mixed $text
    * @param int $x
    * @param int $y
    * @param mixed $color_or_brush_res
    * @param mixed $font
    */
    public function text($text, int $x, int $y, $color_or_brush_res, $font = 1)
    {
        if (is_int($font)) {
            imagestring($this->m_himg, $font, $x, $y, $text, $color_or_brush_res);
        }
        if (is_string($font)) {
            list($font_name, $font_size) = explode(',', $font . ',', 2);
            $font_size = $font_size > 0 ? $font_size : 48;
            imagettftext($this->m_himg, $font_size, 0, $x, $y, $color_or_brush_res, $font_name, $text);
        }
    }


    /**
     * store layer gd
     * @param string $effect 
     * @return void 
     * @throws Exception 
     */

    public function setLayerEffect(string $effect)
    {
        $t = igk_getv(
            [
                'normal' => IMG_EFFECT_NORMAL,
                'overlay' => IMG_EFFECT_OVERLAY,
                'replace' => IMG_EFFECT_REPLACE,
                'multiply' => IMG_EFFECT_MULTIPLY,
                'blend' => IMG_EFFECT_ALPHABLEND
            ],
            strtolower($effect),
            IMG_EFFECT_NORMAL
        );
        imagelayereffect($this->m_himg, $t);
    }
    /**
     * draw curve 
     * @param array $points 
     * @param mixed $color_or_brush_res 
     * @return void 
     */

    public function drawCurve(array $points, $color_or_brush_res, $fill = 0, $closed = false)
    {
        if ($fill)
            imagefilledpolygon($this->m_himg, $points, $color_or_brush_res);
        else {
            if ($closed)
                imagepolygon($this->m_himg, $points, $color_or_brush_res);
            else
                imageopenpolygon($this->m_himg, $points, $color_or_brush_res);
        }
    }
    /**
     * draw circle
     * @param mixed $cx 
     * @param mixed $cy 
     * @param mixed $r 
     * @param mixed $color_or_brush_res 
     * @param int $fill 
     * @return void 
     * @throws ImageException 
     */

    public function circle($cx, $cy, $r, $color_or_brush_res, $fill = 0)
    {
        $w = $h = $r * 2;
        // $cx -= $r;
        // $cy -= $r;
        if ($fill)
            imagefilledellipse($this->m_himg, $cx, $cy, $w, $h, $color_or_brush_res);
        else
            imageellipse($this->m_himg, $cx, $cy, $w, $h, $color_or_brush_res);
    }

    /**
    * Ellipse.
    * @param mixed $cx
    * @param mixed $cy
    * @param mixed $rx
    * @param mixed $ry
    * @param mixed $color_or_brush_res
    * @param mixed $fill
    */
    public function ellipse($cx, $cy, $rx, $ry, $color_or_brush_res, $fill = 0)
    {

        if ($fill)
            imagefilledellipse($this->m_himg, $cx, $cy, $rx*2, $ry*2, $color_or_brush_res);
        else
            imageellipse($this->m_himg, $cx, $cy, $rx*2, $ry*2, $color_or_brush_res);
    }
}
