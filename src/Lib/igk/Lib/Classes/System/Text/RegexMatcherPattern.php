<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherPattern.php
// @date: 20241031 10:36:35
namespace IGK\System\Text;
use ArrayAccess;
use IGK\Helper\Activator;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;
use IGKObject;
use JsonSerializable;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
class RegexMatcherPattern extends IGKObject implements ArrayAccess, IRegexMatcherContainer, JsonSerializable{
    use ArrayAccessSelfTrait;
    /**
    * Constant: match type.
    * @var mixed
    */
    const MATCH_TYPE = 'match';
    /**
    * Constant: begin end type.
    * @var mixed
    */
    const BEGIN_END_TYPE = 'begin/end';
    /**
    * Constant: begin while type.
    * @var mixed
    */
    const BEGIN_WHILE_TYPE = 'begin/while';
    // var $type;
    /**
     * identification tokenID reference 
     * @var mixed
     */
    var $tokenID;
    /**
     * begin pattern of both begin/end begin/while logic
     * @var ?string
     */
    var $begin;
    /**
     * end pattern of begin/end logic
     * @var mixed
     */
    var $end;
    /**
     * match pattern of match logic
     * @var mixed
     */
    var $match;
    /**
     * internal reference id
     * @var mixed
     */
    var $refid;
    /**
     * while pattern of begin/while logic
     * @var mixed
     */
    var $while;
    /**
     * provided name
     * @var ?string
     */
    var $name;
    /**
     * matcher description 
     * @var ?string
     */
    var $description;
    /**
     * the name that will be attached to the content 
     * @var ?string
     */
    var $contentName;
    /**
     * assoc captures array object to match against begin result 
     * @var ?array
     */
    var $beginCaptures;
    /**
     * assoc capture array object to match against end result 
     * @var ?array
     */
    var $endCaptures;
    /**
     * assoc capture array object to match agains both begin/end - match result 
     * @var ?array
     */
    var $captures;
    /**
    * auto generate doc.
    */    var $patterns;
    /**
     * get/set to ask container detection to move forward if stopped on non empty block 
     * @var ?bool
     */
    var $scopedBoundary;
    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_matcher;
    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_type;
    /**
    * Json serialize.
    * @return mixed
    */
    public function jsonSerialize(): mixed {
        $l = (object)(array)$this;
        unset($l->type);
        return $l;
     }
    /**
    * Returns Type.
    */
    public function getType(){
        return $this->m_type;
    }
    /**
    * Sets Type.
    * @param mixed $v
    */
    public function setType($v){
        $this->m_type = $v;
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    protected function _access_OffsetGet($n){
        return $this->{$n};
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    * @return
    */
    protected function _access_OffsetSet($n, $v){
        // not allowed
    }
    /**
    * .ctr
    * @param RegexMatcherContainer $matcher
    */
    public function __construct(RegexMatcherContainer $matcher)
    {
        $this->m_matcher = $matcher;
    }
    /**
     * retrieve the matcher 
     * @return mixed 
     */
    public function getMatcher(){
        return $this->m_matcher;
    }
    /**
     * create a new matcher page 
     * @return static 
     */
    public function match(string $pattern, ?string $tokenId = null, $refid=null, ?array $patterns=null){
        return Activator::CreateNewInstance(static::class, [
            $this->m_matcher,
            'type'=>self::MATCH_TYPE,
            'match'=>$pattern,
            'tokenID'=>$tokenId,
            'refid'=>$refid
        ]);
    }
    /**
     * create a new begin definition 
     * @param string $begin 
     * @param string $end 
     * @param null|string $tokenId 
     * @param null|string $refid 
     * @return static 
     * @throws IGKException 
     */
    public function begin(string $begin, ?string $end=null, ?string $tokenId = null, ?string $refid=null, ?array $patterns=null){
        return Activator::CreateNewInstance(static::class, [
            $this->m_matcher,
            'type'=>self::BEGIN_END_TYPE,
            'begin'=>$begin,
            'end'=>$end,
            'tokenID'=>$tokenId,
            'refid'=>$refid
        ]);
    }
    /**
     * create a while definitions
     * @param string $begin 
     * @param null|string $end 
     * @param null|string $tokenId 
     * @param null|string $refid 
     * @param null|array $pattern 
     * @return mixed 
     * @throws IGKException 
     */
    public function while(string $begin, ?string $end=null, ?string $tokenId = null, ?string $refid=null, ?array $pattern=null){
        return Activator::CreateNewInstance(static::class, [
            $this->m_matcher,
            'type'=>self::BEGIN_WHILE_TYPE,
            'begin'=>$begin,
            'end'=>$end,
            'tokenID'=>$tokenId,
            'refid'=>$refid
        ]);
    }
    /**
     * create escaped litteral 
     * @param null|string $tokenID 
     * @param null|string $refid 
     * @return static 
     * @throws IGKException 
     */
    public function createEscapedString(?string $tokenID='string-escaped-litteral',?string $refid=null){
        $g = $this->begin("('|\")", "\\1", $tokenID, $refid);
        $escaped = self::Match("\\\\.");
        $g->patterns = [
            $escaped
        ];
        return $g;
    }
    /**
     * append
     * @param RegexMatcherPattern $c 
     * @return void 
     */
    public function append(RegexMatcherPattern $c){
        if($c->m_matcher === $this->m_matcher){
            if (is_null($this->patterns))
                $this->patterns = [];
            if (array_search($c, $this->patterns)===false)
            $this->patterns[] = $c;
        }
    }
    /**
     * get matching type depeing on value 
     * @return string 
     */
    public static function GetMatcherType(RegexMatcherPattern $matcher){
        list($begin, $while, $end, $match) = igk_extract($matcher, 'begin|while|end|match');
        if ($match){
            return self::MATCH_TYPE;
        }
        if ($begin){
            if ($while){
                return self::BEGIN_WHILE_TYPE;
            }
            return self::BEGIN_END_TYPE;
        }
        if ($end){
            return self::BEGIN_END_TYPE; 
        }
    }
}