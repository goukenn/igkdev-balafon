<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ICssSupport.php
// @date: 20220423 09:18:43
// @desc: css support rule capture
namespace IGK\Css;
interface ICssSupport{
    /**
     * Check whether a given CSS rule is supported.
     *
     * @param string $rule The CSS rule to check.
     * @return mixed
     */
    function supports(string $rule);
}