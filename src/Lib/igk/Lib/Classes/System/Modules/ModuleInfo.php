<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleInfo.php
// @date: 20230703 10:02:41
namespace IGK\System\Modules;
/**
* 
* @package IGK\System\Modules
*/

/**
* auto generate doc.
* @package IGK\System\Modules
*/
class ModuleInfo{

    /**
    * Name of name.
    * @var mixed
    */
    var $name;

    /**
    * Property: author.
    * @var mixed
    */
    var $author;

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc;

    /**
    * Property: version.
    * @var mixed
    */
    var $version;

    /**
    * Property: email.
    * @var mixed
    */
    var $email;

    /**
    * Property: release.
    * @var mixed
    */
    var $release;

    /**
    * auto generate doc.
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