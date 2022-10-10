<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssSession.php
// @date: 20221005 10:30:40
namespace IGK\System\Html\Css;

use IGKException;
use IGKObject;
use stdClass;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssSession extends IGKObject{
	private $_data;
	private static $sm_instance;
	public static function getInstance(){
		if (self::$sm_instance === null){
			self::$sm_instance = new self;
		} 
		return self::$sm_instance;
	}	
	private function _session_data(){        
		if ($data = igk_app()->session->css_data){
			if ($this->_data !== $data){
				$this->_data = $data;
			}
		} else {
			$data = new stdClass;
			$this->_data = $data;
            igk_app()->session->css_data = $data;
		}
		return $data;
	}
	public function __get($n){
		return $this->get($n);
	}
    public function __set($n, $v){
        if ($data = $this->_session_data()){
            $data->$n = $v;
        }
    }
    public function getTheme($default='light'):?string{
        return $this->get('theme', $default);
    }
    /**
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTheme(string $value){
        $this->__set("theme", $value);
        return $this;
    }
    /**
     * get the default store data for css info
     * @param mixed $n 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public function get($n, $default=null){
        return igk_getv($this->_session_data(), $n, $default);
    }
}
