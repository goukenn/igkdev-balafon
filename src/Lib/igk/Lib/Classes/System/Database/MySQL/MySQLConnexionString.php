<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MySQLConnexionString.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Database\MySQL;
use IGK\Helper\Activator;
use IGK\System\Html\Css\CssParser;
/**
 * create sql connection string
 */
class MySQLConnexionString {

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbname;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbuser;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbpasswd;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbserver;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbdriver = "pdo";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dbcharset = 'utf-8';
    /**
     * create a connection string
     * @param string $connection 
     * @return object 
     */

    public static function Create(string $connexion){
        $g = Activator::CreateNewInstance(self::class, CssParser::Parse($connexion)->to_array());
        return $g;
    }
}