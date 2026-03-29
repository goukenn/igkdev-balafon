<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDisplayExpression.php
// @date: 20240921 09:24:48
namespace IGK\Database;
/**
* 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Database
*/
class DbDisplayExpression{
    /**
    * Constant: exp regex.
    * @var mixed
    */
    const EXP_REGEX = "/\{(?P<name>[^\}\W]*)\}/";
    /**
    * Returns true if Display Expression.
    * @param string $subject
    * @return bool
    */
    public static function IsDisplayExpression(string $subject):bool{
        return preg_match(self::EXP_REGEX,$subject);
    }
    /**
    * auto generate doc.
    * @param mixed $row
    */
    public static function RenderDisplayExpression(string $exp, $row):string{
        return preg_replace_callback(self::EXP_REGEX, function($m)use($row){
            return igk_getv($row, trim($m['name']));
        }, $exp);
    }
}