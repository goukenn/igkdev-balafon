<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramRelationType.php
// @date: 20220531 16:25:33
// @desc: 
namespace IGK\Database\SchemaBuilder;

/**
 * relation type
 * @package igk\db\schemaBuilder
 */
class DiagramRelationType{
    /**
    * Constant: one2many.
    * @var mixed
    */
    const one2Many = "one2many";
    /**
    * Constant: many2many.
    * @var mixed
    */
    const many2Many = "many2many";
    /**
    * Constant: one2one.
    * @var mixed
    */
    const one2one = "one2one";
    /**
    * Property: min.
    * @var mixed
    */
    public $min;
    /**
    * Property: max.
    * @var mixed
    */
    public $max;
    /**
    * .ctr
    * @param mixed $min
    * @param mixed $max
    */
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        return sprintf("custom[%s,%s]", $this->min, $this->max);
    }
}