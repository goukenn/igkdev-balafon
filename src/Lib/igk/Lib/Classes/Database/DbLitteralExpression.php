<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLitteralExpression.php
// @date: 20230116 14:19:52
namespace IGK\Database;

/**
* auto generate doc.
* @package IGK\Database
*/
class DbLitteralExpression extends DbExpression{

    /**
    * Property: source model.
    * @var mixed
    */
    var $source_model;

    /**
    * Property: target model.
    * @var mixed
    */
    var $target_model;

    /**
    * Property: column in source model.
    * @var mixed
    */
    var $column_in_source_model;

    /**
    * Property: column in target model.
    * @var mixed
    */
    var $column_in_target_model;

    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options=null){
        return $options->grammar->createExpression($this);
    }
}