<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbRelation.php
// @date: 20220803 13:48:58
// @desc: 

 namespace IGK\Database;
 ///<summary>represent database relation info</summary>
 /**
  * represent database relation info
  * @package 
  */
class DbRelation{
    /**
     * name of this relation
     * @var mixed
     */
    var $name;
    /**
     * source's table of this relation
     */
    var $source;
    /**
     * destination's table of this relation
     * @var mixed
     */
    var $destination;
    /**
     * relation type
     */
    var $type;

    /**
     * .ctr 
     */
    private function __construct(){

    }
    /**
     * create a relation
     * @param mixed $table 
     * @param mixed $controller 
     * @return static 
     */
    public static function Create($table, $controller){
        $cl = new static();
        foreach($table as $k=>$v){
            if (property_exists(static::class, $k)){
                switch($k){
                    case "source":
                    case "destination":
                        $v = $controller::resolveTableName($v);
                        break;
                }
                $cl->$k = $v;
            }
        }
        return $cl;
    }
}