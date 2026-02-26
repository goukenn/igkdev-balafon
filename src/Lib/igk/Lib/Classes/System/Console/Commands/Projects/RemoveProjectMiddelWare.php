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
    * auto generate doc.
    * @var mixed
    */
    private $m_chain;

    /**
    * auto generate doc.
    */
    public function add(){
	}

    /**
    * auto generate doc.
    */
    public function next(){
	}

    /**
    * auto generate doc.
    */
    public function run(){
		$q = $this->m_chain; 
		while($q){
			$q = $q->next(); 
		}
	}
}