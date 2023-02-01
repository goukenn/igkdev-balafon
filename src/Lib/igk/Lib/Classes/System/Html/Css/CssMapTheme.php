<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMapTheme.php
// @date: 20221230 19:31:10
namespace IGK\System\Html\Css;

use IGKMedia;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssMapTheme{
    var $media;
    var $lk;
    var $is_primaryTheme;

    public function __construct(IGKMedia $media, $is_primaryTheme, $lk)
    {
        $this->media = $media;
        $this->lk = $lk;
        $this->is_primaryTheme = $is_primaryTheme;
    }
    /**
     * map definition 
     * @return void 
     */
    public function map(){
        $g = & $this->media->getDef(); // ->getAttributes();
        if (!$g)return;
        $tab = $g;
        $is_primaryTheme = $this->is_primaryTheme;
        $lk = $this->lk;
        array_map(function($v, $k)use(& $g, $is_primaryTheme, $lk){
            CssUtils::TreatCssDefinition($v, $k, $g, $is_primaryTheme, $lk);
        }, $tab, array_keys($tab));
    }
}