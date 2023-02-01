<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLitteralExpression.php
// @date: 20230116 14:19:52
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
*/
class DbLitteralExpression extends DbExpression{
    var $source_model;
    var $target_model;
    var $column_in_source_model;
    var $column_in_target_model;

    public function getValue($options=null){
        return $options->grammar->createExpression($this);
    }
}