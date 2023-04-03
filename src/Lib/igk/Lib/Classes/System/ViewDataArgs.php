<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDataArgs.php
// @date: 20221113 08:48:43
namespace IGK\System;

use ArrayAccess;
use ArrayIterator;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\System
*/
/**
 * encapsulate data to pass to view
 * @package IGK\System\Console\Commands
 */
class ViewDataArgs extends DataArgs implements ArrayAccess, IteratorAggregate
{
 
    // public static function GetData(ViewDataArgs $arg){
    //     return $arg->p_data;
    // } 
    public function __construct($data)
    {
        parent::__construct($data);
    } 
}
