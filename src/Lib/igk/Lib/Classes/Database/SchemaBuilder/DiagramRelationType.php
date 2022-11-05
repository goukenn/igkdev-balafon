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
    const one2Many = "one2many";
    const many2Many = "many2many";
    const one2one = "one2one";

    public $min;
    public $max;
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }
    public function __toString()
    {
        return sprintf("custom[%s,%s]", $this->min, $this->max);
    }
}

