<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IXsdReference.php
// @date: 20220803 13:48:54
// @desc: 




namespace IGK\XSD;


interface IXsdReference{
    function getRefType();
    /** @return mixed  */
    function getRef();
}