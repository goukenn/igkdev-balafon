<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BoxDimension.php
// @date: 20220531 14:31:06
// @desc: 
namespace IGK\Database\SchemaBuilder\Svg\Html;
use IGK\System\Html\IHtmlGetValue;
/**
 * 
 * @package igk\db\schemaBuilder
 */
class BoxDimension implements IHtmlGetValue{
    /**
     * value to render 
     * @var mixed
     */
    var $value;

    /**
    * .ctr
    */
    public function __construct(){        
    }

    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options=null){ 
        return $this->value;
    }
}