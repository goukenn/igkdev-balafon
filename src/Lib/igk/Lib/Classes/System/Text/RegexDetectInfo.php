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
     * current position position 
     * @var int
     */
    var $pos;

    /**
     * 
     * @var mixed
     */
    var $basePosition;

    /**
     * 
     * @var RegexMatcherPattern
     */
    var $match;

    /**
     * 
     * @var mixed
     */
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
     * flag mark that the detect position is started
     * @var ?bool
     */
    var $start;

    /**
     * mark end type flag
     * @var mixed
     */
    var $endType;

    /**
     * is empty line detected
     * @var mixed
     */
    var $emptyLine;

    /**
    * Id.
    */
    public function id(){
        return $this->match->name ?? ($s =$this->match->tokenID)?explode(' ', $s)[0] : null;
    }
}