<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieManager.php
// @date: 20221009 04:10:08
namespace IGK\System\Http;

use IGKException;

///<summary></summary>
/**
* with session start, used to manage application manage cookies
* @package IGK\System\Http
*/
class CookieManager{
    private static $sm_instance;
    /**
     * supported properties
     */
    const agree = "agree";
    /**
     * name stored
     * @var mixed
     */
    private $m_name;
    /**
     * need to save on application exit 
     * @var false
     */ 
    private $m_saved = false;
    private $m_data;
    public static function getInstance(){
        if (!self::$sm_instance){
            self::$sm_instance = new self;
        }
        return self::$sm_instance;
    }
    private function _getdata(){
        $v_c = igk_environment()->getCookieName();
        if (is_null($this->m_data) || ($v_c != $this->m_name)){
            if (!($d = json_decode(igk_getv($_COOKIE, $v_c)))){
                $d = [];
            } 
            $this->m_data = $d;
            $this->m_name = $v_c; 
        } 
        return $this->m_data;
    }
    /**
     * get value stored 
     * @param mixed $n 
     * @return mixed 
     * @throws IGKException 
     */
    public function get($n){
        
        return igk_getv($this->_getdata(), $n);
    }
    /**
     * set application cookie value
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */
    public function set($n, $v){
        $d = $this->_getdata();
        $d->$n = $v;
        $this->m_saved = true;
    }
    private function __construct()
    {
        register_shutdown_function(function(){
            if ($this->m_saved){
                $this->m_saved = false;
                $_COOKIE[$this->m_name] = json_encode($_COOKIE);
            }
        });
    }
}