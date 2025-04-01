<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataConstantTypes.php
// @date: 20250320 12:00:26
namespace IGK\Database;

use IGK\Models\Traits\ModelTableConstantTrait;
use IGK\System\Database\DbConstantTypeBase;
use IGK\System\Traits\EnumeratesConstants;

///<summary></summary>
/**
* 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class DataConstantTypes extends DbConstantTypeBase{
    use EnumeratesConstants;
    use ModelTableConstantTrait;
}