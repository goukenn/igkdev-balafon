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
class RegexDetectInfo implements IRegexMatcherDetectInfo{
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
    * auto generate doc.
    * @var mixed
    */
    //var $basePosition;
    /**
    * auto generate doc.
    * @var RegexMatcherPattern
    */
    var $match;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $captures;
    /**
    * auto generate doc.
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
    public function __get(string $n){
        igk_die('missing property ['.$n.']');
    }

   
}