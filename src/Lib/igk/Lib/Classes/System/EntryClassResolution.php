<?php
// @author: C.A.D. BONDJE DOUE
// @file: EntryResolution.php
// @date: 20240916 08:44:14
namespace IGK\System;


///<summary></summary>
/**
 * expose class require system entries class resolutions 
 * @package IGK\System
 * @author C.A.D. BONDJE DOUE
 */
abstract class EntryClassResolution
{
    const IGK_TEST_NS = 'IGK\Tests';
    const IGK = 'IGK';
    const DbSchemaBuilder = 'Database\InitDbSchemaBuilder';
    const DbMacrosDisplay = 'Database\Macros\Display';
    const DbClassMapping = 'Database\Mapping';
    const DbClassImport = 'Database\Import';
    const DbMacros = 'Database\Macros';
    const DbInitData = 'Database\InitData';
    const DbInitManager = 'Database\DbInitManager';
    const DbInitMacros = 'Database\InitMacros';
    const DbMigrations = 'Database\Migrations';
    const DbSeederClass = "Database\\Seeds\\DataBaseSeeder";

    const ModelMappingNS = 'Database\Import';

    const CommandEntryNS = '\System\Console\Commands';

    const Models = 'Models';
    const UserProfile = 'UserProfile';
    const Roles = 'Roles';
    const ActionDefaultAction = 'Actions\DefaultAction';
    const Actions = 'Actions';
    const Profiles = 'Profiles';

    const WinUI_ViewLayout = '/WinUI/ViewLayout';
    const WinUI_Form_Validation = '/WinUI/FormValidations';
    const WinUI_ViewLayoutFormat = '/WinUI/Views/%sLayoutLoader';

    const SysSyncProject = 'System\Console\Commands\SyncProject';
    const ProjectProfilesClass = 'Profiles';
    const AuthorizationClass = 'Authorizations';


    const ResponseHandler = 'ResponseHandler';
    const ActionBase = 'IGKActionBase';
    const ActionClassSuffix = 'Action';


    const MailAttachement = '\IGK\System\Net\MailAttachement';
    const CreateValidatorInstance = 'CreateValidatorInstance';

    // + | --------------------------------------------------------------------
    // + | suffix
    // + |
    const ImportMappingSuffix = 'ImportMapping';

    /**
     * reference injector method 
     */
    const ControllerReferenceInjectorMethod = 'didReferenceInjector';


    // + | --------------------------------------------------------------------
    // + | controller's method 
    // + |
    const CTRL_METHOD_INIT_USER_FROM_SYSUSER = 'initUserFromSysUser';
}
