<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ICssResourceResolver.php
// @date: 20220828 11:25:26
// @desc: resolver interface

namespace IGK\Css;

/**
 * resource resolver interface
 * @package IGK\Css
 */
interface ICssResourceResolver{
    /**
     * resolve resource
     * @param string $path 
     * @return null|string 
     */
    function resolve(string $path): ?string;
}