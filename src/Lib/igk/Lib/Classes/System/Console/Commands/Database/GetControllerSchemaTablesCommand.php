<?php
// @author: C.A.D. BONDJE DOUE
// @file: GetControllerSchemaTablesCommand.php
// @date: 20231225 19:32:25
namespace IGK\System\Console\Commands\Database;

use IGK\Helper\JSon;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use Symfony\Component\Serializer\Encoder\JsonEncode;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
*/
class GetControllerSchemaTablesCommand extends AppExecCommand{
	var $command='--db:schema-tables';
	var $desc='get controller schema table';
	var $options=[
		'-o:xml|json'=>'get output type'
	]; 
	var $category = 'db';
	var $usage = 'controller [options]';
	public function exec($command, ?string $controller =null) {
		$ctrl = self::GetController($controller);
		$info = $ctrl->getDataTableDefinition();
		$option = igk_getv($command->options, '-o');
		if ($info){
			$info = array_keys($info->tables);
		}
		if ($option){
			switch($option){
				case 'json':
					Logger::print(JSon::Encode($info));
					igk_exit(0);
				case 'xml':
					$xml = igk_create_xmlnode('schemas');
					$xml->renderAJX();
					igk_exit(0);
			}
		}
		igk_wln_e($info);
	}
}