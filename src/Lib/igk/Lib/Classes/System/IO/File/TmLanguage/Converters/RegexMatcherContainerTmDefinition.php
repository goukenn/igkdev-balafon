<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTmDefinition.php
// @date: 20250704 13:50:33
namespace IGK\System\IO\File\TmLanguage\Converters;
use Exception;
use IGK\System\Polyfill\JsonSerializableTrait;
use JsonSerializable;
/**
* auto generate doc.
* @package IGK\System\IO\File\TmLanguage\Converters
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\IO\File\TmLanguage\Converters
*/
class RegexMatcherContainerTmDefinition implements JsonSerializable
{
    use JsonSerializableTrait;
    /**
    * Name of scope name.
    * @var mixed
    */
    var $scopeName;
    /**
    * Property: version.
    * @var mixed
    */
    var $version;
    /**
    * Property: repository.
    * @var mixed
    */
    var $repository;
    /**
    * Property: patterns.
    * @var mixed
    */
    var $patterns;
    /**
     * private member definition
     * @var array
     */
    private $m_d = [];
    /**
    * auto generate doc.
    * @param mixed $value
    * @return void
    */
    public function __set($name, $value)
    {
        if (preg_match("/^\\$/", $name)) {
            $this->m_d[$name] = $value;
        }
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed|void
    */
    public function __get($name)
    {
        if (preg_match("/^\\$/", $name)) {
            return igk_getv($this->m_d, $name);
        }
    }
    /**
     * serialize object 
     * @return mixed 
     */
    public function _json_serialize()
    {
        $ref = (array)$this;
        foreach (array_keys($ref) as $k) {
            if ($k[0] === "\0") {
                unset($ref[$k]);
            }
        }
        return array_merge($ref, $this->m_d);
    }
}