<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReplacementObject.php
// @date: 20221206 07:34:12
namespace IGK\System\Regex;


///<summary></summary>
/**
* 
* @package IGK\System\Regex
*/
class ReplacementObject{
    /**
     * replacement pattern
     * @var string
     */
    var $pattern;
    /**
     * replace with data
     * @var ?string|callable
     */
    var $replace;

    /**
     * add a callable 
     * @var ?string
     */
    var $type;
}