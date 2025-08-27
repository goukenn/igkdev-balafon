<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherCapture.php
// @date: 20241031 12:04:46
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* use to capture result
* @package IGK\System\Test
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherCapture implements IRegexCaptureInfo{
    /**
     * use internally to get the actual reference
     * @var mixed
     */
    var $tag;
    /**
     * source matching
     * @var mixed
     */
    var $match;
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
     * is empty line detected 
     * @var ?bool
     */
    var $emptyLine;

    /**
     * old current detected info object . use on end treatment
     * @var .null
     */
    var $info;
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
    /**
     * check if end
     * @return bool 
     */
    public function getisEnd(){
        $m = $this->match;
        if ($m && ($m->type == 'match')){
            return true;
        }
        return !is_null($this->endCaptures);
    }
    /**
     * get if is treated value
     * @return bool 
     */
    public function getisTreatedValue(){
        return $this->value != $this->sourceValue;
    }
    public function updateWith($data){
        $this->parentInfo->value .= " ---- ";
        throw new \Exception('not implement');
    }
     
}