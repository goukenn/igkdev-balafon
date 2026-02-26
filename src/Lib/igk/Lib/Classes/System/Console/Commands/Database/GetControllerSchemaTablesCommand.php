<?php
// @author: C.A.D. BONDJE DOUE
// @file: GetControllerSchemaTablesCommand.php
// @date: 20231225 19:32:25
namespace IGK\System\Console\Commands\Database;
use IGK\Helper\JSon;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use Symfony\Component\Serializer\Encoder\JsonEncode;
/**
* 
* @package IGK\System\Console\Commands\Database
*/
class GetControllerSchemaTablesCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--db:schema-tables';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='get controller schema table';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[
		'-o:xml|json'=>'get output type'
	];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'db';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'controller [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $controller
    */
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