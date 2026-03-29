<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestHeader.php
// @date: 20220622 16:29:38
// @desc: 
namespace IGK\System\Http;
/**
 * request header class helper
 * @package IGK\System\Http
 * @var ?string PRAGMA
 * @var ?string CONNECTION
 * @var ?string HOST
 * @var ?string USER_AGENT
 * @var ?string ACCEPT 
 * @var ?string ACCEPT_LANGUAGE
 * @var ?string ACCEPT_ENCODING
 */
class RequestHeader{
    /**
    * Property: prepared.
    * @var mixed
    */
    private $m_prepared;
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * .ctr
    */
    public function __construct(){
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        return $this($name);
    }
    /**
    * Called when an object is used as a function.
    * @param string $name
    */
    public function __invoke(string $name){
        if (!$this->m_prepared){
            $this->m_prepared=  true;
            $this->m_data = igk_get_allheaders();
        }
        return igk_getv($this->m_data , $name);
    }
}