<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitClassBuilder.php
// @date: 20240921 08:32:29
namespace IGK\Database\Models\Helper;

use IGK\Controllers\BaseController;
use IGK\Helper\Database;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Database\DbUtils;
use IGK\System\Database\Helper\DbUtility;
use IGK\System\Database\JoinTableOp;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;
use IGKConstants;

///<summary></summary>
/**
 * 
 * @package IGK\Database\Models\Helper
 * @author C.A.D. BONDJE DOUE
 */
class InitClassBuilder
{
    /**
     * @param string $name
     * @param string $table
     * @param mixed|IDbMigrationInfo $migrationInfo
     * @param BaseController $ctrl
     * @param ?string $comment
     * @param ?string $prefix column prefix of each column
     * @return string 
     */
    public static function BuildInitialModelClass(string $name, string $table, $migrationInfo, BaseController $ctrl, 
        ?string $comment = null, ?string $prefix = null, ?string $display_expression =null,
        ?callable $property_call_info=null,
        ?callable $arg_call_info=null)
    {

        if ($display_expression ){
            // + | --------------------------------------------------------------------
            // + | treat display expression before render 
            // + |
            
            if(strpos($display_expression,',')){
                $fc = function($r)use ($prefix){
                    return DbUtility::TreatColumnName($r, $prefix);
                };
                $r = array_filter(array_map($fc, explode(',', $display_expression)));
                $display_expression = "[\"".implode("\",\"", $r)."\"]";
            } else {
                $display_expression = DbUtility::TreatColumnName($display_expression, $prefix);  
                $display_expression = sprintf('"%s"', $display_expression);
            }
            
        }

        $ns =  $ctrl->getEntryNamespace();
        $uses = [];
        $gc = 0;
        $extends = implode("\\", array_filter([$ns, "Models\\ModelBase"]));
        $c = $ctrl->getClassesDir() . "/Models/";
        if (($name != "ModelBase") && file_exists($c . "/ModelBase.php")) {
            $uses[] =  implode("\\", array_filter([$ns, "Models\\ModelBase"]));
            $gc = 1;
        } else {
            $uses[] = ModelBase::class;
        }
        $o = "/**\n* table's name\n*/\n";
        $o .= "protected \$table = \"{$table}\";" . PHP_EOL;

        if (!$gc && $ctrl) {
            $cl = get_class($ctrl);
            $uses[] = "$cl::class";
            $o .= "/**\n* controller class name\n* @var string*/\n";
            $o .= "protected \$controller = {$cl}::class;" . PHP_EOL;
        }
        if (($migrationInfo instanceof  \IGK\Database\DbColumnInfo)) {
            $migrationInfo = (object)["columnInfo" => [$migrationInfo]];
        }
        $key = "";
        $refkey = "";
        $php_doc = "";
        $const_data = "";
        $hiddens = [];
        $unique_columns = [];
        $displays = [];
        $const_props = [];
        $v_meth_link_canditate = "";
        $v_joinMeth = [];
        $v_uniques_display = [];
        $helper_constant_call = '';
        foreach ($migrationInfo->columnInfo as $cinfo) {
            $v_nn = $cinfo->clName;
            if ($prefix) {
                if (igk_str_startwith($v_nn, $prefix) && !empty($v_gv = substr($v_nn, strlen($prefix)))) {
                    $v_nn =  $v_gv;
                }
            }
            $v_const_name = StringUtility::GetConstantName($v_nn);

            if (DbUtils::IsJoinTableLinkCandidate($cinfo)) {
                $rc = JoinTableOp::class . '::EQUAL';
                //if (!in_array(JoinTableOp::class, $uses)){
                //$uses[] = JoinTableOp::class;
                //}

                $v_js = StringUtility::ConstantToCamelCaseClassName($cinfo->clName);
                $v_joinMeth[] = sprintf("?array joinOn%s(\$call=null, ?string \$type=null, string \$op=\\" . $rc . ") - macros function ", $v_js);
                $v_joinMeth[] = sprintf("?string targetOn%s() - macros function", $v_js);


                $v_meth_link_canditate .= sprintf(
                    "/** join on expression */\npublic function joinOn%s(\$call=null, ?string \$type=null, string \$op=JoinTableOp::EQUAL):array {\n\t%s\n}",
                    $v_js,
                    implode("\n\t", [
                        // '$cl = static::class;',
                        sprintf('return self::joinTableColumnOn(self::FD_%s, $call, $type, $op);', $v_const_name),
                        //'$c = [];',
                        // 'if ($call){',
                        // '    // align condition ',
                        // '    $c[] = $rt."=".$call;',
                        // '    if ($type) $c["type"] = $type;',
                        // '    return [$cl::table()=>$c];',
                        // '}',
                        // 'return [$cl::table()]; '
                    ]),
                ) . "\n";

                $v_meth_link_canditate .=
                    sprintf(
                        "/**\n * @return string\n*/\npublic function targetOn%s(): string{\n%s\n}\n",
                        $v_js,
                        implode("\n\t", [
                            sprintf('return self::column(self::FD_%s);', $v_const_name)
                        ])
                    );
                $v_meth_link_canditate = null;
                // register target method on 
                // JobForemJobs::registerMacro("targetOnJobId", function(){
                //     $cl = static::class;
                //     return $cl::column(JobForemJobs::FD_JOB_ID);
                // });

            }

            if ($cinfo->clIsPrimary || ($cinfo->clAutoIncrement)) {
                if (!empty($key)) {
                    if (!is_array($key)) {
                        $key = [$key => $key];
                    }
                    $key[$cinfo->clName] =  $cinfo->clName;
                } else {
                    $key = $cinfo->clName;
                }
            }
            if ($cinfo->clIsUnique && !$cinfo->clAutoIncrement && $cinfo->clNotNull){
                // + | auto increment value display 
                $v_uniques_display[$cinfo->clName] = $cinfo; 
            }
            if ($cinfo->getIsRefId()) {
                $refkey = $cinfo->clName;
            }
            if ($cinfo->clHide) {
                $hiddens[] = $cinfo->clName;
            }
            if ($cinfo->clIsUniqueColumnMember) {
                if (!($index = $cinfo->clColumnMemberIndex)) {
                    $index = 0;
                }
                $unique_columns[$index][] = $cinfo->clName;
            }

            if ($cinfo->clDisplay) {
                $displays[] = $cinfo->clName;
            }

            // + get property type
            $pr_type = $property_call_info ? $property_call_info($cinfo, $ctrl, $prefix) : 'mixed';

            if ($desc = trim($cinfo->clDescription ?? '')) {
                $desc = ' ' . $desc;
            }
            $php_doc .= sprintf("@property " . $pr_type . "%s\n", $desc);
            $c_p = $v_const_name; //StringUtility::GetConstantName($cinfo->clName);
            if (!isset($const_props[$c_p])) {
                $const_data .=  "const FD_" . $c_p . '="' . $cinfo->clName . '";' . "\n";
                $helper_constant_call .= sprintf("@method static string FD_" . $c_p . "() - `" .
                    $v_nn . "` full column name \n");
                $const_props[$c_p] = 1;
            } else {
                igk_die("constant name already defined " . $table . "::" . $c_p);
            }
        }
        if ($helper_constant_call) {
            $php_doc .= $helper_constant_call;
        }

        $args = $arg_call_info? $arg_call_info($migrationInfo, $ctrl, $prefix) :null;
        array_map(function ($i) use (&$php_doc) {
            $php_doc .= sprintf("@method static %s", $i) . "\n";
        }, $v_joinMeth);
        if ($args) {
            $t_args = implode(", ", $args);
            $php_doc .= "@method static ?self Add(" . $t_args . ") add entry helper\n";
            $php_doc .= "@method static ?self AddIfNotExists(" . $t_args . ") add entry if not exists. check for unique column.\n";
        }
        if ($macros_cl = $ctrl->resolveClass(IGKConstants::NS_MACROS_CLASS . '\\' . $name . 'Macros')) {
            $m = Database::GetPhpDocMacrosDefintionToInjectFromMacroClass($macros_cl);
            $php_doc .= $m;
        }
        if ($key != "clId") {
            if (is_array($key)) {
                $key = "['" . implode("','", array_keys($key)) . "']";
            } else {
                $key = "\"{$key}\"";
            }
            if (!empty($key)) {
                $o .= "/**\n* override primary key \n*/\n";
                $o .= "protected \$primaryKey = $key;" . PHP_EOL;
            }
        }
        if (!empty($refkey) && ($refkey != "clId")) {
            $o .= "/**\n* override refid key \n*/\n";
            $o .= "protected \$refId = \"{$refkey}\";" . PHP_EOL;
        }

        if (!empty($hiddens)) {
            $o .= "/**\n*override hidden key\n*/\n";
            $_hidden = "['" . implode("','", $hiddens) . "']";
            $o .= "protected \$hidden = {$_hidden};" . PHP_EOL;
        }
        $sdisp_play = [];
        if ($display_expression){
            $sdisp_play[] = 'protected $display = '.$display_expression.';'.PHP_EOL;
        } else {
            if (!empty($displays)) {
                $_display = count($displays) == 1 ? "'" . $displays[0] . "'" :  "['" . implode("','", $displays) . "']";
                $sdisp_play[] = "protected \$display = {$_display};" . PHP_EOL;
            } else if (count($v_uniques_display)==1){
                $sdisp_play[] = "protected \$display = \"".key($v_uniques_display)."\";" . PHP_EOL;
            }
        }
        if ($sdisp_play){
            $o .= "/**\n*override display key\n*/\n";
            $o .= implode("", $sdisp_play);

        }

        if (!empty($unique_columns)) {
            $o .= "protected \$unique_columns = " . var_export($unique_columns, true) . ";";
        }


        // + | add method link candidate
        if (!empty($v_meth_link_canditate))
            $o .= "\n" . $v_meth_link_canditate;

        $base_ns = implode("\\", array_filter([$ns, "Models"]));
        $o = $const_data . $o;
        $builder = new PHPScriptBuilder();
        $builder->type("class")
            ->author(IGK_AUTHOR)
            ->extends($extends)
            ->name($name)
            ->namespace($base_ns)
            ->defs($o)
            ->file($name . ".php")
            ->doc($comment)
            ->phpdoc(rtrim($php_doc) . "\n")
            ->uses($uses);
        if (empty($migrationInfo->modelClass)) {
            $migrationInfo->modelClass =    $base_ns . "\\" . $name;
        }
        $cf = $builder->render();
        return $cf;
    }
}
