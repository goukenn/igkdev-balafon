<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvFileConfiguration.php
// @date: 20261001 08:13:21
namespace IGK\System\IO;


/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class DotEnvFileConfiguration extends DotEnvConfiguration{
    /**
     * 
     * @var array
     */
    var $configs;
    /**
     * 
     * @param array $configs 
     * @return mixed 
     */
    public function __construct(array $configs)
    {
       $this->configs = $configs; 
    }
    /**
     * 
     * @param null|string $key 
     * @param mixed $default 
     * @throws \IGKException
     * @return mixed 
     */
    public function getValue(?string $key = null, $default = null)
    {
        empty($key) && igk_die('empty value not allowed');
        $c =  igk_getv($this->configs, $key, $default );
        if ($c instanceof DotEnvVarConfiguration){
            return $c->__toString();
        }
        return $c;
    }
    /**
     * get configs keys
     * @return int[]|string[] 
     */
    public function getKeys(){
        return array_keys($this->configs);
    }
}