<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RegexConstant.php
// @date: 20220322 15:34:54
// @desc: 

namespace IGK\System\Regex;

/**
 * regex constants
 * @package IGK\System\Regex
 */
class RegexConstant{
    const VERSION_CHECK = '(?P<version>[0-9]+(\.[0-9]+){0,3})';
    const INT_REGEX = "/^[0-9]+$/i";

}
