<?php
// @author: C.A.D. BONDJE DOUE
// @file: CreateUserProfileClassCommand.php
// @date: 20240922 06:54:01
namespace IGK\System\Console\Commands\Projects;

use IGK\Controllers\ApplicationController;
use IGK\Models\ModelBase;
use IGK\System\Applications\ApplicationUserProfile;
use IGK\System\Console\AppExecCommand;
use igk\System\Console\Commands\Utility;
use IGK\System\Console\Logger;
use IGK\System\EntryClassResolution;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\SystemUserProfile;
use IGK\System\Traits\EnumeratesConstants;

///<summary></summary>
/**
* 
* @package IGK\\System\Console\Commands\Projects
* @author C.A.D. BONDJE DOUE
*/
class CreateUserProfileClassCommand extends AppExecCommand{
	var $command='--make:userprofile-class';
	var $desc='scaffold file to enable project\'s controller user profile management'; 
	/* var $options=[]; */
	var $category="make";
	var $usage = 'controller [options]';
	public function exec($command, ?string $controller=null) {
		is_null($controller) && igk_die('require controller');
		$ctrl = self::GetController($controller);
		$v_pname = EntryClassResolution::UserProfile;
		$f = $ctrl::resolveClass($v_pname);

		if ($f && class_exists($f)){
			Logger::warn("profile class already exists");
			return -1;
		}
		$file = Path::Combine($ctrl->getClassesDir(), $v_pname.'.php');
		$bind = [];
		$bind[$file]= function($file)use($ctrl, $v_pname){
		$code = implode("\n",[ 
			'protected function registerProfile() {',
			'	/* Authorization::BindUserToGroup($this->getController(), $this->model(), Profiles::getDefaultProfile(); );*/',
			'}'
		]);
		$parent = SystemUserProfile::class;
		if ($ctrl instanceof ApplicationController){
			$parent = ApplicationUserProfile::class;
		}

		$src = new PHPScriptBuilder;
		$src->name(igk_io_basenamewithoutext($v_pname))
		->comment("support - specific user connection to service")
		->type('class')
		->uses([
			ModelBase::class
		])
		->namespace($ctrl->getEntryNamespace())
		->extends($parent)
		->defs($code);

		igk_io_w2file($file, $src->render());
		};

		$bind[Path::Combine($ctrl->getDeclaredDir(), 'Configs/profile.php')] = function($file){
			$c = new PHPScriptBuilder;
			$c->type('function')
			->comment('profile definition settings')
			->defs('return [];');
			igk_io_w2file($file, $c->render());
		};
		
		$c = Path::Combine($ctrl->getClassesDir(), EntryClassResolution::ProjectProfilesClass.'.php');
		if (!file_exists($c)){
			$bind[$c] = function($file)use($ctrl){
				$c = new PHPScriptBuilder;
				$c->type('class')
				->name(EntryClassResolution::ProjectProfilesClass)
				->class_modifier("abstract")
				->namespace($ctrl->getEntryNamespace())
				->comment('Expected constant in profiles')
				->defs('/* const ProfileRole="ProfileRoleValue" */'); 
				igk_io_w2file($file, $c->render()); 
			};
		}
		$c = Path::Combine($ctrl->getClassesDir(), EntryClassResolution::AuthorizationClass.'.php');
		
		if (!file_exists($c)){
			$bind[$c] = function($file)use($ctrl){
				$c = new PHPScriptBuilder;
				$c->type('class')
				->name(igk_io_basenamewithoutext(EntryClassResolution::AuthorizationClass))
				->class_modifier("abstract")
				->uses([
					EnumeratesConstants::class
				])
				->namespace($ctrl->getEntryNamespace())
				->comment('define all project enumeration ')
				->defs(implode("\n", [
					'/* const AuthorizationName="AuthorizationValue"; */',
					'use EnumeratesConstants;'
				])); 
				igk_io_w2file($file, $c->render()); 
			};
		}


		Utility::MakeBindFiles($command, $bind, true);
		Logger::success("output: ".$file);
		Logger::success('done');

	}
}