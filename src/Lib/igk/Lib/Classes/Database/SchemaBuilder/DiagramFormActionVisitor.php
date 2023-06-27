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
 * 
 * @package igk\db\schemaBuilder
 */
class DiagramFormActionVisitor extends DiagramVisitor{
    private $visitor_items = [];
    var $builder;
    var $ctrl;

    public function __construct($ctrl)
    {
        $this->ctrl = $ctrl;
    }
    public function start():?string{
        $this->visitor_items = [];
        $this->builder = new PHPScriptBuilder();
        $this->builder->type("function");
        return null;
    }
    public function complete():?string{
        return  $this->builder->render();
    }
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
