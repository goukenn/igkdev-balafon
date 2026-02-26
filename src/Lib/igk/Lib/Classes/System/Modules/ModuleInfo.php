<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleInfo.php
// @date: 20230703 10:02:41
namespace IGK\System\Modules;
/**
* 
* @package IGK\System\Modules
*/
class ModuleInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $author;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $version;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $email;

    /**
    * auto generate doc.
    * @var mixed
    */
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