<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKTranslator.php
// @date: 20220830 21:17:16
// @desc: the translator
namespace IGK;

/**
 * composer translation implementor
 * @package IGK
 */
abstract class IGKTranslator{
    /**
    * Returns.
    * @param mixed $n
    */
    public function get($n){
        return igk_resources_gets(...func_get_args());
    }
}