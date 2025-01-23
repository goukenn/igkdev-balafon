<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherCapture.php
// @date: 20241031 12:04:46
namespace IGK\System\Text;


///<summary></summary>
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
///<summary></summary>
/**
* use to capture result
* @package IGK\System\Test
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherCapture implements IRegexCaptureInfo{
    /**
     * the value
     * @var mixed
     */
    var $value;

    /**
     * from offset
     * @var mixed
     */
    var $from;

    /**
     * to offset
     * @var ?int
     */
    var $to;

    /**
     * the token id
     * @var mixed
     */
    var $tokenID;

    /**
     * capture list at the begin
     * @var mixed
     */
    var $beginCaptures;

    /**
     * get the end capture list in case of begin/end/while
     * @var mixed
     */
    var $endCaptures;

    /**
     * 
     * @var merge captures / begin / match
     */
    var $captures;

    /**
     * get the current parent information 
     * @var mixed
     */
    var $parentInfo;

    /**
     * last segment not detected trailing capture.
     * @var ?bool
     */
    var $trailingEnd;

    /**
     * real captured value
     * @var ?string
     */
    var $sourceValue;

    /**
     * extra option when match - and treat capture 
     * @var ?object
     */
    var $option;
    /**
     * parent info is null
     * @return bool 
     */
    public function getisRoot():bool{
        return is_null($this->parentInfo);
    }
    public function getisRootCaptured():bool{
        return $this->getisRoot() && !isset($this->trailingEnd);
    }
}