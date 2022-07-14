<?php
namespace IGK\System\Console;

class CommandEnvironmentArgLoader{
    var $separator = "=";

    public function load(object $obj, string $args){
        $tab = explode($this->separator, $args);
        $key = $tab[0];
        $data = implode($this->separator, array_slice($tab, 1));
        $obj->$key = $data;
    }
}