<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbColumnInfoTrait.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database\Traits;

/**
 * 
 */
trait DbColumnInfoTrait
{

  /**
   * store link name
   * @var mixed
   */
  var $clLinkName;
  /**
   * reference key column
   * @var mixed
   */
  // protected $clRefId;
  /**
   * set if the column is auto increment
   * @var mixed
   */
  var $clAutoIncrement;
  /**
   * default increment start index
   * @var mixed
   */
  var $clAutoIncrementStartIndex;
  /**
   * column member index
   * @var mixed
   */
  var $clColumnMemberIndex;
  /**
   * set the default value
   * @var int
   */
  var $clDefault;
  /**
   * set the column description
   * @var mixed
   */
  var $clDescription;
  /**
   * from reference keys
   * @var ?string
   */
  var $clFormRefKey;
  /**
   * form input type
   * @var string
   */
  var $clInputType;
  /**
   * string comma separated values of available enum values if type is Enum
   * @var mixed
   */
  var $clEnumValues;
  /**
   * set method to invoke in insert function
   * @var mixed
   */
  var $clInsertFunction;
  /**
   * set if the column is index
   * @var mixed
   */
  var $clIsIndex;
  /**
   * filter this column in filter query
   * @var mixed
   */
  var $clIsNotInQueryInsert;
  /**
   * set if this column is primary key
   * @var mixed
   */
  var $clIsPrimary;
  /**
   * set if column is unique
   * @var mixed
   */
  var $clIsUnique;
  /**
   * unique column member list
   * @var mixed
   */
  var $clIsUniqueColumnMember;
  /**
   * link column name
   * @var mixed
   */
  var $clLinkColumn;
  /**
   * the link table default display
   * @var mixed
   */
  var $clLinkTableDisplay;
  /**
   * link to table for foreign key
   * @var mixed
   */
  var $clLinkType;

  /**
   * link relation name
   * @var mixed
   */
  var $clLinkRelationName;

  /**
   * link constraint name
   * @var mixed
   */
  var $clLinkConstraintName;

  /**
   * link inversed name 
   * @var mixed
   */
  var $clLinkInverseName;
  /**
   * the name of the column
   * @var mixed
   */
  var $clName;
  /**
   * define if the column require a value. default is false
   * @var false
   */
  var $clNotNull;
  /**
   * pattern to validate the column value
   * @var mixed
   */
  var $clPattern;
  /**
   * type of the column
   * @var string
   */
  var $clType;
  /**
   * column type length
   * @var null
   */
  var $clTypeLength;
  /**
   * function to call on update
   * @var mixed
   */
  var $clUpdateFunction;
  /**
   * check constraint expression
   * @var mixed
   */
  var $clCheckConstraint;

  /**
   * the link expression for default value
   */
  var $clDefaultLinkExpression;

  /**
   * this column is require in form input
   * @var clRequire
   */
  var $clRequire;

  /**
   * textarea input max length
   * @var mixed
   */
  var $clInputMaxLength;

  /**
   * not allow empty string. 
   * @var mixed
   */
  var $clNotAllowEmptyString;

  /**
   * indicate that the fields is used by other controller. \
   * system used that field for migrations purpose.
   * @var ?array of used controller other thant the host
   */
  var $clIsUsedBy;

  /**
   * validator type name
   * @var ?string
   */
  var $clValidator;

  /**
   * mark the column as hide for result
   * @var ?bool
   */
  var $clHide;

  /**
   * use to mark a column in display chain 
   * @var mixed
   */
  var $clDisplay;

  /**
   * string linkto relation table[,column,type]
   * @var ?string
   */
  var $clLinkTo;
}
