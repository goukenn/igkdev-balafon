<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CommandEnvironmentArgLoader.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console;

/**
* auto generate doc.
* @package IGK\System\Console
*/
class CommandEnvironmentArgLoader{
    var $separator = "=";

    /**
    * auto generate doc.
    * @param object $obj
    * @param string $args
    */
    public function load(object $obj, string $args){
        $tab = explode($this->separator, $args);
        $key = $tab[0];
        $data = implode($this->separator, array_slice($tab, 1));
        $obj->$key = $data;
    }
}