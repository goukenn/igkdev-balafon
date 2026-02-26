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
    * auto generate doc.
    * @var mixed
    */
    const one2Many = "one2many";

    /**
    * auto generate doc.
    * @var mixed
    */
    const many2Many = "many2many";

    /**
    * auto generate doc.
    * @var mixed
    */
    const one2one = "one2one";

    /**
    * auto generate doc.
    * @var mixed
    */
    public $min;

    /**
    * auto generate doc.
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