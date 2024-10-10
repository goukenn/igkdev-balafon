<?php
// @author: C.A.D. BONDJE DOUE
// @file: SvgTreatTrait.php
// @date: 20230429 21:46:54
namespace IGK\System\SVG\Traits;

use IGK\Helper\StringUtility;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\Html\XML\XmlNode;
use IGK\System\Html\XML\XmlNodeLoader;
use IGK\System\Regex\Replacement;

///<summary></summary>
/**
* 
* @package IGK\System\SVG\Traits
*/
trait SvgTreatTrait{
        /**
     * treat svg symbol - to be exported 
     * @param string $svg 
     * @return string 
     */
    public static function TreatSvg(string $svg){
        if ($c = preg_match_all("/(\s*)fill\s*=\s*\"/i", $svg, $tab, PREG_OFFSET_CAPTURE)){
            switch($c){
                case 1:
                    $start = $tab[0][0][1];
                    $endoffset = strlen($tab[0][0][0]) + $start;
                    igk_str_read_brank($svg, $endoffset, '"', '"');
                    // 
                    // remove only the file attribute
                    // + | ------------------------------------------------------------------
                    // + | monochrone
                    $svg = StringUtility::RmSubString($svg,  $start, $endoffset+1-$start);
                    break;
                case 2: 
                case 3:
                case 4:
                    // + | ------------------------------------------------------------------
                    // + | colours : replace with color class 
                    $r = "";
                    $offset = 0;                 
                    for($i = 0 ; $i < $c; $i++ ){
                        $g = $tab[0][$i];  
                        $start = $g[1];
                        $endoffset = strlen($g[0]) + $start;
                        igk_str_read_brank($svg, $endoffset, '"', '"');
                        $r .= substr($svg, $offset, $g[1] - $offset );
                        $r .= " class=\"color_".($i + 1)."\" ";
                        $offset = $endoffset+1;              
                    }
                    $r .= substr($svg, $offset); 
                    $svg = $r;
                    break;    
            } 
        }
        if ($c = preg_match_all("/(\s*)stroke\s*=\s*\"/i", $svg, $tab, PREG_OFFSET_CAPTURE)){
            switch($c){
                case 1:
                    $start = $tab[0][0][1];
                    $endoffset = strlen($tab[0][0][0]) + $start;
                    igk_str_read_brank($svg, $endoffset, '"', '"');
                    // 
                    // remove only the file attribute
                    // + | ------------------------------------------------------------------
                    // + | monochrone
                    $svg = StringUtility::RmSubString($svg,  $start, $endoffset+1-$start);
                    break;
                case 2: 
                case 3:
                case 4:
                    // + | ------------------------------------------------------------------
                    // + | colours : replace with stroke-color class 
                    $r = "";
                    $offset = 0;                 
                    for($i = 0 ; $i < $c; $i++ ){
                        $g = $tab[0][$i];  
                        $start = $g[1];
                        $endoffset = strlen($g[0]) + $start;
                        igk_str_read_brank($svg, $endoffset, '"', '"');
                        $r .= substr($svg, $offset, $g[1] - $offset );
                        $r .= " class=\"stroke_color_".($i + 1)."\" ";
                        $offset = $endoffset+1;              
                    }
                    $r .= substr($svg, $offset); 
                    $svg = $r;
                    break;    
            } 
        }
        $m = preg_match('/stroke:\s*[^;\"]+(;)?/', $svg);
        //remove style store properties on all element
        $rp = new Replacement;
        $rp->add('/stroke:\s*[^;\"]+(;)?/', '');
        $svg = $rp->replace($svg);

        $n = XmlNodeLoader::CreateFromContent($svg);
        
        if ($t = $n->getElementsByTagName('svg')){
            $t = $t[0];
            $w = $t['width'];
            $h = $t['height'];
            if (!$t['viewBox'] && $w && $h){
                $t['viewBox'] = sprintf('0 0 %s %s', $w, $h);
            }
            if ($m){
                $t['class'] = 'stroke-only';
            }
            $svg = $n->render();            
        }
        return $svg;
    }
}