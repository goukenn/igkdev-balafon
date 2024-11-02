<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherPattern.php
// @date: 20241031 10:36:35
namespace IGK\System\Text;

use ArrayAccess;
use IGK\Helper\Activator;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherPattern implements ArrayAccess{
    use ArrayAccessSelfTrait;
    const MATCH_TYPE = 'match';
    const BEGIN_END_TYPE = 'begin/end';
    var $type;
    var $tokenID;
    var $begin;
    var $end;
    var $match;
    var $refid;
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

    protected function _access_OffsetGet($n){
        return $this->{$n};
    }
    public function __construct(RegexMatcherContainer $matcher)
    {
        $this->m_matcher = $matcher;
    }

    /**
     * create a new matcher page 
     * @return static 
     */
    public function match(string $pattern, ?string $tokenId = null, $refid=null){
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
    public function begin(string $begin, string $end, ?string $tokenId = null, ?string $refid=null){
        return Activator::CreateNewInstance(static::class, [
            $this->m_matcher,
            'type'=>self::MATCH_TYPE,
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
}