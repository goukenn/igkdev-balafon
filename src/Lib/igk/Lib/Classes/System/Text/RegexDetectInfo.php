<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectInfo.php
// @date: 20250713 08:49:09
namespace IGK\System\Text;


/**
* info for matching detection 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexDetectInfo{
    /**
     * detected value 
     * @var string
     */
    var $value;
    /**
     * position 
     * @var int
     */
    var $pos;

    /**
     * 
     * @var RegexMatcherPattern
     */
    var $match;

    var $captures;

    /**
     * 
     * @var mixed
     */
    var $parent;

    /**
     * move to next line
     * @var ?bool
     */
    var $moveToNextLine;

    /**
     * end info treatment
     * @var mixed
     */
    var $endTreat;

    /**
     * start flag.
     * @var mixed
     */
    var $start;

    /**
     * mark end type flag
     * @var mixed
     */
    var $endType;
}