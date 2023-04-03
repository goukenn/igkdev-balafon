<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaGenerationFieldTrait.php
// @date: 20230202 13:53:03
namespace IGK\System\Database\Traits;

use IGK\Database\DbColumnInfo;

///<summary>Column field generator</summary>
/**
* Column field generator
* @package IGK\System\Database
*/
trait SchemaGenerationFieldTrait{
 /**
     * use in visitor to update time column reference
     * @param object $clinfo 
     * @param null|string $prefix 
     * @return void 
     */
    protected function _Gen_updateTime(object $clinfo, ?string $prefix = null)
    {
        $n = $prefix . "Create_At";
        $clinfo->columns[$n] = new DbColumnInfo([
            "clName" => $n, "clType" => "DateTime", "clInsertFunction" => "Now()",
            "clNotNull" => "1", "clDefault" => "Now()"
        ]);
        $n = $prefix . "Update_At";
        $clinfo->columns[$n] = new DbColumnInfo(
            [
                "clName" => $n, "clType" => "DateTime", "clInsertFunction" => "Now()",
                "clUpdateFunction" => "Now()", "clNotNull" => "1", "clDefault" => "Now()"
            ]
        );
    }
    /**
     * generate adress 
     * @param object $clinfo 
     * @param null|string $prefix 
     * @return void 
     */
    protected function _Gen_address(object $clinfo, ?string $prefix = null)
    {
        foreach([
            ["clName"=>"{$prefix}AddrStreet","clType"=>"Text",],
            ["clName"=>"{$prefix}AddrNumber","clType"=>"VarChar" ,"clTypeLength"=>15],
            ["clName"=>"{$prefix}AddrBox", "clType"=>"VarChar" ,"clTypeLength"=>10],
            ["clName"=>"{$prefix}AddrPostalCode","clType"=>"VarChar","clTypeLength"=>10],
            ["clName"=>"{$prefix}AddrCity", "clType"=>"Text"],
            ["clName"=>"{$prefix}AddrCountry", "clType"=>"VarChar", "clTypeLength"=>"4", "clDescription"=>"country's iso code"],
        ] as $v){
            $clinfo->columns[$v['clName']] = new DbColumnInfo($v);
        }
       
    }

      /**
     * generate adress 
     * @param object $clinfo 
     * @param null|string $prefix 
     * @return void 
     */
    protected function _Gen_contact(object $clinfo, ?string $prefix = null)
    {
        foreach([
            ["clName"=>"{$prefix}Phone","clType"=>"Text",],
            ["clName"=>"{$prefix}Email","clType"=>"VarChar" ,"clTypeLength"=>15],
            ["clName"=>"{$prefix}Fax","clType"=>"VarChar" ,"clTypeLength"=>15],
            ["clName"=>"{$prefix}WebSite","clType"=>"VarChar" ,"clTypeLength"=>255],
        ] as $v){
            $clinfo->columns[$v['clName']] = new DbColumnInfo($v);
        }
       
    }
}