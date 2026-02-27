<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGKEnvironmentServices.php
// @date: 20240929 13:45:10
namespace IGK;

/**
* auto generate doc.
* @package IGK
* @author C.A.D. BONDJE DOUE
*/
class IGKEnvironmentServices{

    /**
    * Property: creator.
    * @var mixed
    */
    private $m_creator;

    /**
    * Property: services.
    * @var mixed
    */
    private $m_services;

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_services = [];
    }

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        return igk_getv($this->m_services , $n);
    }
    /**
     * register a service
     * @param string $n 
     * @param mixed $cl 
     * @return void 
     */

    public function register(string $n, $cl){
        if ($cl === null){
            unset($this->m_services[$n]);
        } else 
            $this->m_services[$n] = $cl;
        return $this;
    }
}