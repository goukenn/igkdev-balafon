<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMapTheme.php
// @date: 20221230 19:31:10
namespace IGK\System\Html\Css;
use IGKMedia;

/**
* auto generate doc.
* @package IGK\System\Html\Css
*/
class CssMapTheme{

    /**
    * Property: media.
    * @var mixed
    */
    var $media;

    /**
    * Property: lk.
    * @var mixed
    */
    var $lk;

    /**
    * Flag: is primary theme.
    * @var mixed
    */
    var $is_primaryTheme;

    /**
    * Property: skip property.
    * @var mixed
    */
    var $skipProperty;

    /**
    * .ctr
    * @param IGKMedia $media
    * @param mixed $is_primaryTheme
    * @param mixed $lk
    */
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
        $v_source_defs = [];
        array_map(function($v, $k)use(& $g, $is_primaryTheme, $lk,& $v_source_defs){
            if (empty($v)){
                return $v;
            }
            if ($this->skipProperty)
            {
                $v = CssUtils::RemoveNoTransformPropertyStyle($v);
                if (empty($v)){
                    $g[$k] = null;
                    return;
                }
            }
            CssUtils::TreatCssDefinition($v, $k, $g, $is_primaryTheme, $lk, $v_source_defs);
        }, $tab, array_keys($tab));
        $this->media->clear();
        $this->media->load_data(['def'=>$g]);
    }
}