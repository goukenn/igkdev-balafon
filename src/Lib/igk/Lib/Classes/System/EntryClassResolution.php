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
    * auto generate doc.
    * @var mixed
    */
    const IGK_TEST_NS = 'IGK\Tests';

    /**
    * auto generate doc.
    * @var mixed
    */
    const IGK = 'IGK';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbSchemaBuilder = 'Database\InitDbSchemaBuilder';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbMacrosDisplay = 'Database\Macros\Display';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbClassMapping = 'Database\Mapping';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbClassImport = 'Database\Import';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbMacros = 'Database\Macros';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbInitData = 'Database\InitData';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbInitManager = 'Database\DbInitManager';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbInitMacros = 'Database\InitMacros';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbMigrations = 'Database\Migrations';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DbSeederClass = "Database\\Seeds\\DataBaseSeeder";

    /**
    * auto generate doc.
    * @var mixed
    */
    const ModelMappingNS = 'Database\Import';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CommandEntryNS = '\System\Console\Commands';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Models = 'Models';

    /**
    * auto generate doc.
    * @var mixed
    */
    const UserProfile = 'UserProfile';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Roles = 'Roles';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ActionDefaultAction = 'Actions\DefaultAction';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Actions = 'Actions';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Profiles = 'Profiles';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ProfilesGetDefaultMethod = 'GetDefaultProfile';

    /**
    * auto generate doc.
    * @var mixed
    */
    const WinUI_ViewLayout = '/WinUI/ViewLayout';

    /**
    * auto generate doc.
    * @var mixed
    */
    const WinUI_Form_Validation = '/WinUI/FormValidations';

    /**
    * auto generate doc.
    * @var mixed
    */
    const WinUI_ViewLayoutFormat = '/WinUI/Views/%sLayoutLoader';

    /**
    * auto generate doc.
    * @var mixed
    */
    const SysSyncProject = 'System\Console\Commands\SyncProject';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ProjectProfilesClass = 'Profiles';

    /**
    * auto generate doc.
    * @var mixed
    */
    const AuthorizationClass = 'Authorizations';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ResponseHandler = 'ResponseHandler';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ActionBase = 'IGKActionBase';

    /**
    * auto generate doc.
    * @var mixed
    */
    const ActionClassSuffix = 'Action';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MailAttachement = '\IGK\System\Net\MailAttachement';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CreateValidatorInstance = 'CreateValidatorInstance';
    // + | --------------------------------------------------------------------
    // + | suffix
    // + |

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @var mixed
    */
    const CTRL_METHOD_INIT_USER_FROM_SYSUSER = 'initUserFromSysUser';
}