<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLitteralExpression.php
// @date: 20230116 14:19:52
namespace IGK\Database;
/**
* 
* @package IGK\Database
*/
class DbLitteralExpression extends DbExpression{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $source_model;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $target_model;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $column_in_source_model;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $column_in_target_model;

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getValue($options=null){
        return $options->grammar->createExpression($this);
    }
}