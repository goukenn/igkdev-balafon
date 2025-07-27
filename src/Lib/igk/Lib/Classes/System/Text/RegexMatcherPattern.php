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
class RegexMatcherPattern extends IGKObject implements ArrayAccess, IRegexMatcherContainer, JsonSerializable{
    use ArrayAccessSelfTrait;
    const MATCH_TYPE = 'match';
    const BEGIN_END_TYPE = 'begin/end';
    const BEGIN_WHILE_TYPE = 'begin/while';
    // var $type;
    var $tokenID;
    var $begin;
    var $end;
    var $match;
    var $refid;
    var $while;
    var $name;
    var $description;
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
     * 
     * @var null|Array|RegexMacherPattern[]
     */
    var $patterns;
    /**
     * 
     * @var mixed
     */
    private $m_matcher;
    private $m_type;

    public function jsonSerialize(): mixed {
        $l = (object)(array)$this;
        unset($l->type);
        return $l;
     }
    public function getType(){
        return $this->m_type;
    }
    public function setType($v){
        $this->m_type = $v;
    }
   
    protected function _access_OffsetGet($n){
        return $this->{$n};
    }
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