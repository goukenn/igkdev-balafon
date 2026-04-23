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
    * Name of dbname.
    * @var mixed
    */
    var $dbname;
    /**
    * Property: dbuser.
    * @var mixed
    */
    var $dbuser;
    /**
    * Property: dbpasswd.
    * @var mixed
    */
    var $dbpasswd;
    /**
    * Property: dbserver.
    * @var mixed
    */
    var $dbserver;
    /**
    * Property: dbdriver.
    * @var mixed
    */
    var $dbdriver = "pdo";
    /**
    * Property: dbcharset.
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