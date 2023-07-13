<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleInfo.php
// @date: 20230703 10:02:41
namespace IGK\System\Modules;


///<summary></summary>
/**
* 
* @package IGK\System\Modules
*/
class ModuleInfo{
    var $name;
    var $author;
    var $desc;
    var $version;
    var $email;

    var $release;

    /**
     * 
     * @var ?array required modules
     */
    private $m_require;

    /**
     * set require
     * @param null|array $require 
     * @return void 
     */
    public function setRequire(?array $require ){
        $this->m_require = $require;
    }
    /**
     * get require
     * @return mixed 
     */
    public function getRequire(){
        return $this->m_require;
    }
}