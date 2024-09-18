<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaBuilderMigration.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Database;
 
use IGKException;

/**
 * update schema migrations
 * @package IGK\System\Database
 */
class SchemaBuilderMigration{
    var $controller;
    var $listener;
    private $items; 
    /**
     * use method to add migrations data 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return object 
     * @throws IGKException 
     */
    public function __call($name, $arguments)
    {
        $cl = __NAMESPACE__."\\Schema".ucfirst($name)."Migration";
        if (class_exists($cl) && is_subclass_of($cl, SchemaMigrationItemBase::class)){
            if(!$this->items){
                $this->items = [];
            }
            $c = new $cl($this);
            $this->items[] = $c;
            return $c;
        }
        throw new IGKException("schema builder not allowed : $cl::".$name);
    } 
    public function upgrade(){
        if (!$this->items)return false;
        foreach($this->items as $c){
            $c->up();
        }
        if ($this->listener){
            $this->listener->up();
        }
        return true;
    }
    public function downgrade(){
        if (!$this->items)
            return false;
        foreach($this->items as $c){
            $c->down();
        }
        if ($this->listener){
            $this->listener->down();
        }
        return true;
    }
    public function __construct(){
    }
     
}