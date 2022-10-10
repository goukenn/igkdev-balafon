<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlConvDefinition.php
// @date: 20221006 10:34:07
namespace IGK\System\Html\Converters;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Converters
*/
class HtmlConvDefinition{
    /**
     * attribute definition
     * @var mixed
     */
    var $attr;
    /**
     * defined value
     * @var mixed
     */
    var $value;

    /**
     * create Helper
     */
    public static function CreateFromArray($value, $attr){
        $o = new self;
        $o->attr = $attr;
        $o->value = $value;
        return $o;
    }
}
