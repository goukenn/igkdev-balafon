<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MatchPattern.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Regex;


class MatchPattern{
    const Int = "[0-9]+";
    const Float = "(([0-9]+)\.[0-9]+)|([0-9]+)(\.[0-9]+)?";
    const DateSearch = "[0-9]{4}((-|\/)([0-9]{2}))?";
}