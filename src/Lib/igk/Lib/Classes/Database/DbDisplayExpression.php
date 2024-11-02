<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbDisplayExpression.php
// @date: 20240921 09:24:48
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
* @author C.A.D. BONDJE DOUE
*/
class DbDisplayExpression{
    const EXP_REGEX = "/\{(?P<name>[^\}\W]*)\}/";
    public static function IsDisplayExpression(string $subject):bool{
        return preg_match(self::EXP_REGEX,$subject);
    }
    /**
     * 
     * @param mixed $exp 
     * @param mixed $row 
     * @return string|string[]|null 
     */
    public static function RenderDisplayExpression(string $exp, $row):string{
        return preg_replace_callback(self::EXP_REGEX, function($m)use($row){
            return igk_getv($row, trim($m['name']));
        }, $exp);
    }
}