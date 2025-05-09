<?php
// @author: C.A.D. BONDJE DOUE
// @file: EmailConverter.php
// @date: 20250506 14:32:02
namespace IGK\Database\PhoneBooks;

use IGKValidator;

///<summary></summary>
/**
 * 
 * @package IGK\Database\PhoneBooks
 * @author C.A.D. BONDJE DOUE
 */
class EmailConverter extends PhoneBookConverterBase
{
    /**
     * treat is email data
     * @param mixed $v 
     * @return mixed 
     */
    public function treat($v)
    {
        if (is_array($v)){
           return array_filter(array_map(function($v){
                if (IGKValidator::IsEmail($v)) {
                    return $v;
                }  
            }, $v));
        }
        if (IGKValidator::IsEmail($v)) {
            return $v;
        }
        return null;
    }
}
