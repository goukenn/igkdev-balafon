<?php
// @author: C.A.D. BONDJE DOUE
// @file: RemoveProjectMiddelWare.php
// @date: 20260226 19:20:35
namespace IGK\System\Console\Commands\Projects;


/**
* 
* @package IGK\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
class RemoveProjectMiddleWare{

    /**
    * Property: chain.
    * @var mixed
    */
    private $m_chain;

    /**
    * Adds.
    */
    public function add(){
	}

    /**
    * Next.
    */
    public function next(){
	}

    /**
    * Runs.
    */
    public function run(){
		$q = $this->m_chain; 
		while($q){
			$q = $q->next(); 
		}
	}
}