<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ICssStyleContainer.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Css;
/**
* Interface for css style container.
* @package IGK\Css
*/
interface ICssStyleContainer{
    /**
     * Get the CSS definition.
     *
     * @return mixed
     */
    function getdef();
    /**
     * Get the CSS properties.
     *
     * @return mixed
     */
    function getProperties();
}