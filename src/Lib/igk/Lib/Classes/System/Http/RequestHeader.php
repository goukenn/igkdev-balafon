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
    private $m_prepared;
    private $m_data;
    public function __construct(){

    }
    public function __get($name){
        return $this($name);
    }
    public function __invoke(string $name){
        if (!$this->m_prepared){
            $this->m_prepared=  true;
            $this->m_data = igk_get_allheaders();
        }
        return igk_getv($this->m_data , $name);
    }
}
