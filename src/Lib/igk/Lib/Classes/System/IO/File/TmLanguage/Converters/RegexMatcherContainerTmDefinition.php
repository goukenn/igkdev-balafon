<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTmDefinition.php
// @date: 20250704 13:50:33
namespace IGK\System\IO\File\TmLanguage\Converters;

use JsonSerializable;

/**
* 
* @package IGK\System\IO\File\TmLanguage\Converters
* @author C.A.D. BONDJE DOUE
*/
class RegexMatcherContainerTmDefinition implements JsonSerializable{
    var $scopeName;
    var $version;
    var $repository;
    var $patterns; 
    /**
     * private member definition
     * @var array
     */
    private $m_d = [];

    public function __set($name, $value){
        if (preg_match("/^\\$/", $name)){
            $this->m_d[$name] = $value;
        }
    }
    public function __get($name){
           if (preg_match("/^\\$/", $name)){
            return igk_getv($this->m_d, $name);

           }
    }
    public function jsonSerialize(): mixed
    {
        $ref = (array)$this;
        // remove private members
        foreach(array_keys($ref) as $k){
            if ($k[0]==="\0"){
                unset($ref[$k]);
            }
        } 
        return array_merge($ref, $this->m_d);
    } 
}