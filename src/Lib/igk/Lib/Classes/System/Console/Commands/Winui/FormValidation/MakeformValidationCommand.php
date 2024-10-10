<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeformValidationCommand.php
// @date: 20240923 09:26:30
namespace IGK\System\Console\Commands\Winui\FormValidation;

use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\CommandCategories;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger as ConsoleLogger;
use IGK\System\Console\Utils;
use IGK\System\EntryClassResolution;
use IGK\System\Html\Forms\Validations\InspectorFormFieldValidationBase;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\WinUI\Forms\FormValidationData;
use Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Winui\FormValidation
* @author C.A.D. BONDJE DOUE
*/
class MakeformValidationCommand extends AppExecCommand{
	var $command='--make:form-validation';
	var $desc='make a form validation. Contextual command*.'; 
	/* var $options=[]; */
	var $category = CommandCategories::MAKE; 
	var $usage = '[controller] name [options]'; 
	public function exec($command, ?string $controller=null, ?string $name=null) {
		self::ContextController($command, $controller, $name);
		
		empty($name) && igk_die("name required");

		$ctrl = self::GetController($controller);
		$dir = Path::Combine($ctrl->getClassesDir(), EntryClassResolution::WinUI_Form_Validation);
		$bind = $this->_bindingList($dir, $ctrl, $name);
		Utility::MakeBindFiles($command, $bind, false);

		ConsoleLogger::success('done');
	 }
	 protected function _bindingList(string $dir, BaseController $ctrl, $name){
		$bind = [];
		$name = igk_str_add_suffix(ucfirst(igk_ns_name($name)), 'FormData');


		$bind[$dir."/".$name.".php"] = function($file)use($ctrl, $name){
			$ens = igk_ns_name(Path::Combine($ctrl->getEntryNamespace(),EntryClassResolution::WinUI_Form_Validation));
			$sb = new PHPScriptBuilder;
			$sb->type('class')
			->namespace($ens)
			->uses([])
			->extends(
				InspectorFormFieldValidationBase::class
			)
			->name(
				$name
			);
			igk_io_w2file($file, $sb->render());
		};
		return $bind;
	 }
	 /**
	  * handle contextual command
	  * @param mixed $command 
	  * @param mixed &$controller 
	  * @param mixed &$name 
	  * @return void 
	  * @throws Exception 
	  */
	 static function ContextController($command, & $controller, & $name){
		if (is_null($name)){
			if (property_exists($command->options, '--controller')){
				$ctrl = self::ResolveController($command, null, false) ?? igk_die('missing controller');
				$name = $controller;
				$controller = $ctrl->getName();
			}
		}
	 }
}