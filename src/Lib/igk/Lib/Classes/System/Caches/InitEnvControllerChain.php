<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitEnvControllerChain.php
// @date: 20220906 11:18:32
namespace IGK\System\Caches;


///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class InitEnvControllerChain{
    private $m_chain = [];
    public function add($chain){
        array_push($this->m_chain, $chain);
        return $this;
    }
    public function update($ctrl){
        foreach($this->m_chain as $k){
            $k->update($ctrl);
        }
    }
    public function complete(){
        foreach($this->m_chain as $k){
            $k->complete();
        }
    }
}