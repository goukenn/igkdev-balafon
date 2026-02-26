<?php
// @author: C.A.D. BONDJE DOUE
// @file: TmLanguageCaptureRegexContainer.php
// @date: 20241107 05:35:47
namespace IGK\System\IO\File\TmLanguage;
use IGK\Helper\Activator;
use IGK\System\Text\IRegexMatcherContainer;
use IGK\System\Text\RegexMatcherPattern;
/**
* 
* @package IGK\System\IO\File\TmLanguage
* @author C.A.D. BONDJE DOUE
*/
class TmLanguageCaptureRegexContainer implements IRegexMatcherContainer{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_patterns;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_container;

    /**
    * .ctr
    * @param mixed $container
    */
    public function __construct($container)
    {
        $this->m_container = $container;
        $this->m_patterns = [];
    }

    /**
    * auto generate doc.
    * @param string $match
    * @param null|string $tokenID
    * @param null|string $refId
    * @param null|array $patterns
    */
    public function match(string $match, ?string $tokenID = null, ?string $refId = null, ?array $patterns = null) {
        $inf = Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this->m_container,
            'type' => RegexMatcherPattern::MATCH_TYPE,
            'match' => $match,
            'tokenID' => $tokenID,
            'refid' => $refId
        ]);
        $this->m_patterns[]=  $inf;
     }

    /**
    * auto generate doc.
    * @param string $begin
    * @param null|string $end
    * @param null|string $tokenID
    * @param null|string $refId
    * @param null|array $patterns
    */
    public function begin(string $begin, ?string $end = null, ?string $tokenID = null, ?string $refId = null, ?array $patterns = null) {
        $inf = Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this->m_container,
            'type' => RegexMatcherPattern::BEGIN_END_TYPE,
            'begin' => $begin,
            'end' => $end,
            'tokenID' => $tokenID,
            'refid' => $refId
        ]);
        $this->m_patterns[]=  $inf;
     }

    /**
    * auto generate doc.
    * @param string $begin
    * @param null|string $end
    * @param null|string $tokenID
    * @param null|string $refId
    * @param null|array $patterns
    */
    public function while(string $begin, ?string $end = null, ?string $tokenID = null, ?string $refId = null, ?array $patterns = null) { 
        $inf = Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this->m_container,
            'type' => RegexMatcherPattern::BEGIN_WHILE_TYPE,
            'begin' => $begin,
            'end' => $end,
            'tokenID' => $tokenID,
            'refid' => $refId
        ]);
        $this->m_patterns[]=  $inf;
    }
    /**
     * retrieve the pattern
     * @return array 
     */

    public function getPatterns(){
        return $this->m_patterns;
    }
    /**
     * append
     * @param RegexMatcherPattern $c 
     * @return void 
     */

    public function append(RegexMatcherPattern $c){
        if($c->getMatcher() === $this->m_container){
            if (is_null($this->m_patterns))
                $this->m_patterns = [];
            if (array_search($c, $this->m_patterns)===false)
            $this->m_patterns[] = $c;
        }
    }
}