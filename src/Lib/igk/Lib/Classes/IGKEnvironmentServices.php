<?php
// @author: C.A.D. BONDJE DOUE
// @file: IGKEnvironmentServices.php
// @date: 20240929 13:45:10
namespace IGK;


///<summary></summary>
/**
* 
* @package IGK
* @author C.A.D. BONDJE DOUE
*/
class IGKEnvironmentServices{
    private $m_creator;
    private $m_services;

    public function __construct()
    {
        $this->m_services = [];
    }
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