<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MatchPattern.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Regex;

/**
 * match pattern
 * @package IGK\System\Regex
 */
abstract class MatchPattern{
    /**
     * in number
     */
    const Int = "[0-9]+";
    /**
     * float number
     */
    const Float = "(([0-9]+)\.[0-9]+)|([0-9]+)(\.[0-9]+)?";
    /**
     * single float number
     */
    const Single = "[0-9]+(\.[0-9]+)?";
    /**
     * date search pattern
     */
    const DateSearch = "[0-9]{4}((-|\/)([0-9]{2}))?";
}