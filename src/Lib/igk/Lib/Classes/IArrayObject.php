<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IArrayObject.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK;
use ArrayAccess;
use Countable;
use IGK\System\IToArray;
/**
* Interface for array object.
* @package IGK
*/
interface IArrayObject extends ArrayAccess, Countable, IToArray{
}