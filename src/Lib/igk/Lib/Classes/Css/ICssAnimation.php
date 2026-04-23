<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICssAnimation.php
// @date: 20250409 11:46:53
namespace IGK\Css;

/**
* auto generate doc.
* @package IGK\Css
* @author C.A.D. BONDJE DOUE
*/
interface ICssAnimation{
    /**
     * register animation 
     * @param string $name 
     * @param mixed $definition 
     * @return mixed 
     */
    function animation(string $name, $definition);
}