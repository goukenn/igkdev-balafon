<?php
// @author: C.A.D. BONDJE DOUE
// @filename: d.php
// @date: 20220531 13:33:13
// @desc: 

namespace IGK\Database\SchemaBuilder;

/**
 * represent a diagram visitor
 * @package igk\db\schemaBuilder
 */
class DiagramVisitor extends DiagramVisitorBase{
    var $diagram;

    public function start():?string{
        return null;
    }
    public function complete():?string{
        return null;
    }
    /**
     * visit item
     * @param mixed $item 
     * @return null 
     */
    public function visit($item){
        if (is_object($item)){
        $fc = "visit".basename(igk_uri(get_class($item)));  
        return call_user_func_array([$this, $fc], func_get_args());
        }
        return null;
    }
    /**
     * accept visit
     * @param mixed $item 
     * @return false 
     */
    public function acceptVisit($item)
    {
        if (is_object($item)){
            $fc = "visit".basename(igk_uri(get_class($item)));  
            return method_exists($this, $fc);
        }
        return false;
    }
}