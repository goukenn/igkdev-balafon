<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherPatternContainer.php
// @date: 20250816 09:36:08
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherPatternContainer extends RegexMatcherPattern implements IRegexMatcherPatternContainer{
    /**
    * Property: regex.
    * @var mixed
    */
    private $m_regex;
    /**
    * .ctr
    * @param mixed $regex
    * @param mixed $matcher
    */
    public function __construct($regex, $matcher){    
        parent::__construct($matcher);
        $this->m_regex = $regex; 
    }
    /**
    * Returns Type.
    */
    public function getType()
    {
        return 'include';
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        if (method_exists($this, $fc = 'get'.$name)){
            return $this->$fc();
        }
        return $this->m_regex->{$name};
    }
    /**
    * destructor
    * @param mixed $name
    * @param mixed $v
    */
    public function __set($name, $v){
        $this->m_regex->{$name} = $v;
    }
    /**
     * matching info container 
     * @param null|RegexDetectInfo &$info 
     * @param string $source 
     * @param int &$offset 
     * @return void 
     */
    public function startMatch(?RegexDetectInfo $parent_info, ?RegexDetectInfo & $info, string $source, int & $offset){
        $this->m_regex->resetTreatment();
        $this->m_regex->setParentInfo($parent_info);
        if ($g = $this->m_regex->detect($source, $offset)){
            $info = $g;
        } 
    }
}