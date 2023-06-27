<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonFileConfigurationTrait.php
// @date: 20230420 12:43:57
namespace IGK\System\Traits;


///<summary></summary>
/**
* for json file configuration 
* @package IGK\System\Traits
*/
trait JSonFileConfigurationTrait{
    /**
     * 
     * @param string $file 
     * @return null|static 
     */
    public static function Load(string $file){
        $data = json_decode(file_get_contents($file));
        if ($data){
            return static::CreateFromConfigData($data);
        }
        return null;
    }
    /**
     * @param mixed $data 
     * create from configuration data
     * @return static 
     */
    public abstract static function CreateFromConfigData($data);

}