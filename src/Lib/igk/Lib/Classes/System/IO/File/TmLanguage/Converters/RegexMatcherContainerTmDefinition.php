<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTmDefinition.php
// @date: 20250704 13:50:33
namespace IGK\System\IO\File\TmLanguage\Converters;

use Exception;
use JsonSerializable;

/**
 * 
 * @package IGK\System\IO\File\TmLanguage\Converters
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainerTmDefinition implements JsonSerializable
{

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
     * 
     * @param mixed $name 
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
     * 
     * @param mixed $name 
     * @return mixed|void 
     * @throws Exception 
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

    public function jsonSerialize(): mixed
    {
        $ref = (array)$this;
        // remove private members
        foreach (array_keys($ref) as $k) {
            if ($k[0] === "\0") {
                unset($ref[$k]);
            }
        }
        return array_merge($ref, $this->m_d);
    }
}
