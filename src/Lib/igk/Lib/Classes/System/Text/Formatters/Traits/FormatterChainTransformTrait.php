<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterChainTransformTrait.php
// @date: 20250807 11:27:07
namespace IGK\System\Text\Formatters\Traits;

use IGK\System\Text\RegexMatcherContainer;

/**
* 
* @package IGK\System\Text\Formatters\Traits
* @author C.A.D. BONDJE DOUE
*/
trait FormatterChainTransformTrait{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $m_sub_chain;

    /**
    * auto generate doc.
    * @param RegexMatcherContainer $regex
    */
    protected function saveState(RegexMatcherContainer $regex){
        $state = $regex->saveState(); 
        $state['sb'] = $this->m_sb.'';       
        $state['sub'] = $this->m_sub;       
        $state['chain_logic'] = $this->m_chain_logic;
        $state['depth'] = $this->m_depth;
        $state['marked'] = $this->m_marked;
        $state['bind'] = $this->bind;
        $state['splitting'] = $this->m_split_node;
        $state['format_offset'] = $this->m_offset;
        $state['flags'] = $this->m_flags;
        $state['e_transform'] = $this->m_transform; 
        return $state;
    }

    /**
    * auto generate doc.
    * @param RegexMatcherContainer $regex
    * @param mixed $state
    */
    protected function restoreState(RegexMatcherContainer $regex, $state){
        $regex->restoreState($state); 
        $this->m_chain_logic = $state['chain_logic'];
        $this->m_depth = $state['depth'] ;
        $this->m_marked = $state['marked'];
        $this->m_sub = $state['sub'];
        $this->bind = $state['bind']; 
        $this->m_split_node = $this->m_split_node ?? $state['splitting'];
        $this->m_flags =   $state['flags'];
        $this->m_offset = $state['format_offset'];
        $this->m_transform = $state['e_transform'];
    }
    /**
     * chain transform definition 
     * @param RegexMatcherContainer $regex 
     * @param array $patterns 
     * @param string $v 
     * @return mixed 
     */

    public function chainTransfrom(RegexMatcherContainer $regex, array $patterns, string $v) {  
      //  return $v; 
        $state = $this->saveState($regex);         
        $this->clearFlags(); 
        $this->m_chain_logic = [];
        $this->bind = [];
        // $this->m_depth = 0;
        $this->m_sub  = [];
        $this->m_sb->clear();
        $this->m_split_node = null;
        $regex->resetTreatment();  
        $this->m_sub_chain = true;              
        $rp = $this->exec($regex, $v, true, $patterns);
        $this->m_sub_chain = false;
        $this->m_sb->clear();
        $this->m_sb->append($state['sb']);
        $this->restoreState($regex, $state); 
        return $rp;
    }
}