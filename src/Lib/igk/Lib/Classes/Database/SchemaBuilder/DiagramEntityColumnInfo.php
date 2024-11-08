<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramEntityColumnInfo.php
// @date: 20220531 16:31:03
// @desc: 


namespace IGK\Database\SchemaBuilder;

use IGK\Database\IDbColumnInfo;
use IGK\Database\Traits\DbColumnInfoMethodTrait;
use IGK\Database\Traits\DbColumnInfoTrait;
use IGK\System\Database\DbUtils;

/**
 * 
 * @package 
 */
class DiagramEntityColumnInfo implements IDbColumnInfo
{
     use DbColumnInfoTrait;
     use DbColumnInfoMethodTrait;

     public function __construct()
     {
          $this->clType = "Int";
          $this->clTypeLength = 9;
     }

     /**
      * 
      * @return bool 
      */
     public function getIsDumpField(): bool
     {
          return DbUtils::GetIsDumpField($this);
     }
}
