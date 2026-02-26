<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneConverter.php
// @date: 20250505 09:25:00
namespace IGK\Database\PhoneBooks;
/**
* 
* @package IGK\Database\PhoneBooks
* @author C.A.D. BONDJE DOUE
*/
class PhoneConverter extends PhoneBookConverterBase{

    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function treat($v) { 
        $v = str_replace(' ', '', trim($v));
        $v = preg_replace('/^00/', '+', $v);
        // for belgium number 
        $v = preg_replace('/^04/', '+324', $v);
        $v = preg_replace('/^0([1-9])/', '+32\\1', $v);
        return $v;
    }
}