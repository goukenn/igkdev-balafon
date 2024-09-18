<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbConstantTypeBase.php
// @date: 20240917 17:48:12
namespace IGK\System\Database;

use IGK\Models\Traits\ModelTableConstantTrait;
use IGK\System\Traits\EnumeratesConstants;

///<summary></summary>
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
abstract class DbConstantTypeBase{
    use EnumeratesConstants;
    use ModelTableConstantTrait;
}