<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramFormActionVisitor.php
// @date: 20220531 13:34:45
// @desc: build data schema visitor.
namespace IGK\Database\SchemaBuilder;
use IGK\Database\DbSchemas;
use IGK\System\Html\XML\XmlNode;
use IGK\System\IO\File\PHPScriptBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
* auto generate doc.
* @package igk\db\schemaBuilder
*/
class DiagramFormActionVisitor extends DiagramVisitor{

    /**
    * Collection of visitor items.
    * @var mixed
    */
    private $visitor_items = [];

    /**
    * Property: builder.
    * @var mixed
    */
    var $builder;

    /**
    * Property: ctrl.
    * @var mixed
    */
    var $ctrl;

    /**
    * .ctr
    * @param mixed $ctrl
    */
    public function __construct($ctrl)
    {
        $this->ctrl = $ctrl;
    }

    /**
    * Starts.
    * @return ?string
    */
    public function start():?string{
        $this->visitor_items = [];
        $this->builder = new PHPScriptBuilder();
        $this->builder->type("function");
        return null;
    }

    /**
    * Complete.
    * @return ?string
    */
    public function complete():?string{
        return  $this->builder->render();
    }

    /**
    * Visit diagram entity.
    * @param mixed $entity
    */
    public function visitDiagramEntity($entity){
        $n = $this->ctrl::db_getTableName($entity->getName());
        $o  = "// | ---------------------------------". PHP_EOL;
        $o .= "// | FORM : ". $this->ctrl::db_getTableName($entity->getName()) . PHP_EOL;
        $model = "";
        $_to = [
            "\$forms['{$n}'] =  new FormStorageAction({$model}::formFields(), ",     
            'function(Request $request){',
            ];       
        if($p = $entity->getProperties()){
            foreach($p as $l){
                // $ul = $n->add(DbSchemas::COLUMN_TAG);
                // $r = (array)$l;
                // if (!DiagramEntityColumnInfo::SupportTypeLength($r["clType"])){
                //     unset($r["clTypeLength"]);
                // }
                // $ul->setAttributes($r); 
            }
        }
$_to[] = '});';
        $o .= implode("\n", $_to).PHP_EOL; 
        $this->builder->defs = $this->builder->defs.$o;
    }
}