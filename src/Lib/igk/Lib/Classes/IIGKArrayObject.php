<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IIGKArrayObject.php
// @date: 20220803 13:48:54
// @desc: 

///<summary>an element that must implement to_array method</summary>

use IGK\System\IToArray;

interface IIGKArrayObject extends ArrayAccess, Countable, IToArray{
     
}