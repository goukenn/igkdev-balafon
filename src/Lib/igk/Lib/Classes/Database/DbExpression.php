<?php
// @file: DbExpression.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
use IGK\Helper\Activator;
use IGK\System\Html\IHtmlGetValue;
use IGKObject;
use ModelBase;

/**
* auto generate doc.
* @package IGK\Database
*/
class DbExpression extends IGKObject implements IHtmlGetValue{
    protected $m_v;
    public function __construct($value=null){
        $this->m_v=$value;
    }
    /**
     * 
     * @return mixed 
     */
    public static function Now(){
        static $now;
        if (is_null($now)){
            $now = new DbExpression('Now()');
        }
        return $now;
    }

    /**
    * auto generate doc.
    * @param mixed $expression
    */
    public static function Create($expression){
        $g=new static($expression); 
        return $g;
    }

    /**
    * auto generate doc.
    * @param null|mixed $o
    */
    public function getValue($o=null){
        if (!is_string($this->m_v)){
            if ($this->m_v instanceof static){
                $gram = ($o ? igk_getv($o, 'grammar') : null ) ?? igk_die("no grammar provided");
                return $gram->createExpression($this->m_v);
            }
        }
        return $this->m_v;
    }

    /**
    * auto generate doc.
    * @param \IGK\System\Models\ModelBase $source_model
    * @param \IGK\System\Models\ModelBase $target_model
    * @param string $column_in_source_model
    * @param string $column_in_target_model
    */
    public static function NotInSelectedField(\IGK\System\Models\ModelBase  $source_model, \IGK\System\Models\ModelBase $target_model, 
        string $column_in_source_model, 
        string $column_in_target_model){
        $g = Activator::CreateNewInstance(DbLitteralExpression::class, get_defined_vars());
        return $g;
    }
}