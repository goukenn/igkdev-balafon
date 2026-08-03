<?php
// @author: C.A.D. BONDJE DOUE
// @file: EntryResolution.php
// @date: 20240916 08:44:14
namespace IGK\System;

/**
 * expose class require system entries class resolutions 
 * @package IGK\System
 * @author C.A.D. BONDJE DOUE
 */
abstract class EntryClassResolution
{
    /**
    * Constant: igk test ns.
    * @var mixed
    */
    const IGK_TEST_NS = 'IGK\Tests';
    /**
    * Constant: igk.
    * @var mixed
    */
    const IGK = 'IGK';
    /**
    * Constant: db schema builder.
    * @var mixed
    */
    const DbSchemaBuilder = 'Database\InitDbSchemaBuilder';
    /**
    * Constant: db macros display.
    * @var mixed
    */
    const DbMacrosDisplay = 'Database\Macros\Display';
    /**
    * Constant: db class mapping.
    * @var mixed
    */
    const DbClassMapping = 'Database\Mapping';
    /**
    * Constant: db class import.
    * @var mixed
    */
    const DbClassImport = 'Database\Import';
    /**
    * Constant: db macros.
    * @var mixed
    */
    const DbMacros = 'Database\Macros';
    /**
    * Constant: db init data.
    * @var mixed
    */
    const DbInitData = 'Database\InitData';
    /**
    * Constant: db init manager.
    * @var mixed
    */
    const DbInitManager = 'Database\DbInitManager';
    /**
    * Constant: db init macros.
    * @var mixed
    */
    const DbInitMacros = 'Database\InitMacros';
    /**
    * Constant: db migrations.
    * @var mixed
    */
    const DbMigrations = 'Database\Migrations';
    /**
    * Constant: db seeder class.
    * @var mixed
    */
    const DbSeederClass = "Database\\Seeds\\DataBaseSeeder";
    /**
    * Constant: model mapping ns.
    * @var mixed
    */
    const ModelMappingNS = 'Database\Import';
    /**
    * Constant: command entry ns.
    * @var mixed
    */
    const CommandEntryNS = '\System\Console\Commands';
    /**
    * Constant: models.
    * @var mixed
    */
    const Models = 'Models';
    /**
    * Constant: user profile.
    * @var mixed
    */
    const UserProfile = 'UserProfile';
    /**
    * Constant: roles.
    * @var mixed
    */
    const Roles = 'Roles';
    /**
    * Constant: action default action.
    * @var mixed
    */
    const ActionDefaultAction = 'Actions\DefaultAction';
    /**
    * Constant: actions.
    * @var mixed
    */
    const Actions = 'Actions';
    /**
    * Constant: profiles.
    * @var mixed
    */
    const Profiles = 'Profiles';
    /**
    * Constant: profiles get default method.
    * @var mixed
    */
    const ProfilesGetDefaultMethod = 'GetDefaultProfile';
    /**
    * Constant: win ui view layout.
    * @var mixed
    */
    const WinUI_ViewLayout = '/WinUI/ViewLayout';
    /**
    * Constant: win ui form validation.
    * @var mixed
    */
    const WinUI_Form_Validation = '/WinUI/FormValidations';
    /**
    * Constant: win ui view layout format.
    * @var mixed
    */
    const WinUI_ViewLayoutFormat = '/WinUI/Views/%sLayoutLoader';
    /**
    * Constant: sys sync project.
    * @var mixed
    */
    const SysSyncProject = 'System\Console\Commands\SyncProject';
    /**
    * Constant: project profiles class.
    * @var mixed
    */
    const ProjectProfilesClass = 'Profiles';
    /**
    * Constant: authorization class.
    * @var mixed
    */
    const AuthorizationClass = 'Authorizations';
    /**
    * Constant: response handler.
    * @var mixed
    */
    const ResponseHandler = 'ResponseHandler';
    /**
    * Constant: action base.
    * @var mixed
    */
    const ActionBase = 'IGKActionBase';
    /**
    * Constant: action class suffix.
    * @var mixed
    */
    const ActionClassSuffix = 'Action';
    /**
    * Constant: mail attachement.
    * @var mixed
    */
    const MailAttachement = '\IGK\System\Net\MailAttachement';
    /**
    * Constant: create validator instance.
    * @var mixed
    */
    const CreateValidatorInstance = 'CreateValidatorInstance';
    // + | --------------------------------------------------------------------
    // + | suffix
    // + |
    /**
    * Constant: import mapping suffix.
    * @var mixed
    */
    const ImportMappingSuffix = 'ImportMapping';
    /**
     * reference injector method 
     */
    const ControllerReferenceInjectorMethod = 'didReferenceInjector';
    // + | --------------------------------------------------------------------
    // + | controller's method 
    // + |
    /**
    * Constant: ctrl method init user from sysuser.
    * @var mixed
    */
    const CTRL_METHOD_INIT_USER_FROM_SYSUSER = 'initUserFromSysUser';
    /**
     * classe entry use to define hook command info help if filter hook failed 
     */
    const COMMAND_HELP_INFO_NS = 'System/Console/Help/';

    const ProfileGetDefaultProfileMethod = 'getDefaultProfile';
}