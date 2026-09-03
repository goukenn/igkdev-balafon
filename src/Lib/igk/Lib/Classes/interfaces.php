<?php
// @author: C.A.D. BONDJE DOUE
// @filename: interfaces.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK;
use IGK\Database\IDatabaseCreator;
use IGK\System\Configuration\Controllers\IConfigController;
use IGK\System\Html\Dom\HtmlNode;

/**
* auto generate doc.
* @package 1
* @property array $srcs src list
*/
 interface IComponentInfo{
 }
/**
* Interface for db get table reference handler.
* @package IGK
*/
interface IDbGetTableReferenceHandler{
    /**
    * Returns Data Tables Reference.
    * @param mixed & $table
    */
    public function getDataTablesReference(& $table);
    /**
    * Resolv table definition.
    * @param string $table
    */
    public function resolvTableDefinition(string $table);
}
/**
* represent IIAction Result interface
*/
interface IActionResult{
    /**
    * auto generate doc.
    */    function index();
} 
/**
* RepresentIController interface
*/
interface IController{
    /**
     * return the controller identifier 
     * @return string
    */
    function getName():string;
}
/**
* Interface for node controller.
* @package IGK
*/
interface INodeController extends IController{
    /**
    * retriev e the target node  
    */
    function getTargetNode() : HtmlNode;
    /**
    * auto generate doc.
    */    function getTargetNodeId();
}
/**
* Interface for view controller.
* @package IGK
*/
interface IViewController{
    /**
    * View.
    */
    function View();
}
/**
* RepresentIControllerInitListener interface
*/
interface IControllerInitListener{
    /**
    * auto generate doc.
    * @param mixed $name
    */
    function addDir($name);
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $source
    * @param mixed $override
    */
    function addSource($name, $source, $override=true);
}
/**
* RepresentICssCtrlHost interface
*/
interface ICssCtrlHost{
    /**
    * auto generate doc.
    */    function bindCss();
    /**
    * auto generate doc.
    * @param mixed $doc the default value is null
    */
    function getIsCssActive($doc=null);
}
/**
* RepresentICtrlDirManagement interface
*/
interface ICtrlDirManagement{
    /**
    * auto generate doc.
    */    function getDataDir();
    /**
    * 
    */
    function getDeclaredDir() : string;
    /**
    * auto generate doc.
    */    function getName();
    /**
    * auto generate doc.
    */    function getResourcesDir();
    /**
    * auto generate doc.
    */    function getStylesDir();
    /**
    * auto generate doc.
    */    function getViewDir();
}
/**
* base data adapter operation
*/
interface IDataAdapter{
    /**
    * auto generate doc.
    * @param string $tbname
    * @param ?array $where
    * @param ?array $options
    */    function selectCount(string $tbname, ?array $where = null, ?array $options = null);
    /**
    * auto generate doc.
    * @param bool $check
    */    
    function setForeignKeyCheck($check);
}
/**
* RepresentIDataTable interface
*/
interface IDataTable{}
/**
* RepresentIDbUtility interface
*/
interface IDbUtility{
    /**
    * auto generate doc.
    * @param string $table
    * @param mixed $obj
    * @param mixed $leaveopen the default value is false
    */
    function insertIfNotExists(string $table, $obj, $leaveopen=false);
}
/**
* Interface for db model.
* @package IGK
*/
interface IDbModel{
    /**
    * Returns Table.
    */
    function getTable();
}
/**
* engine form builder interface
*/
interface IFormBuilderEngine{
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $type
    * @param mixed $text the default value is null
    */
    function addButton($id, $type='submit', $text=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value
    * @param mixed $attribs the default value is null
    */
    function addCheckbox($id, $value=null, $attribs=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $type
    * @param mixed $style the default value is null
    */
    function addControl($id, $type='text', $style=null);
    /**
    * auto generate doc.
    */    function addGroup();
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $class the default value is null
    */
    function addLabel($id, $class=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value
    * @param mixed $type
    * @param mixed $style the default value is null
    */
    function addLabelControl($id, $value=null, $type='text', $style=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $entries
    * @param mixed $filter the default value is null
    */
    function addLabelSelect($id, $entries, $filter=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value the default value is null
    */
    function addLabelTextarea($id, $value=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value
    * @param mixed $attribs the default value is null
    */
    function addRadioButton($id, $value=null, $attribs=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value the default value is null
    */
    function addTextarea($id, $value=null);
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $value
    * @param mixed $attribs the default value is null
    */
    function addTextfield($id, $value=null, $attribs=null);
    /**
    * auto generate doc.
    */    function getView();
    /**
    * auto generate doc.
    * @param mixed $host
    */
    function setView($host);
}
/**
* RepresentIFrameController interface
*/
interface IFrameController{
    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $frame
    * @param mixed $remove the default value is true
    */
    function ContainFrame($id, $frame, $remove=true);
}
/**
* Represent a web component interface
*/
interface IHtmlComponent{
    /**
    * auto generate doc.
    */    function getComponentId();
    /**
    * auto generate doc.
    * @param mixed $uri
    */
    function getComponentUri($uri);
    /**
    * auto generate doc.
    */    function getController();
    /**
    * auto generate doc.
    * @param mixed $listener
    * @param mixed $param the default value is null
    */
    function setComponentListener($listener, $param=null);
}
/**
* use to indicate that an element can store a cookie to client size
*/
interface IHtmlCookieItem{
    /**
    * auto generate doc.
    */    function getCookieId();
    /**
    * auto generate doc.
    * @param mixed $v
    */
    function setCookieId($v);
}
/**
* RepresentIHtmlLoadContent interface
*/
interface IHtmlLoadContent {
    /**
    * auto generate doc.
    * @param mixed $data
    * @param mixed $context the default value is null
    */
    function LoadExpression($data, $context=null);
    /**
    * auto generate doc.
    * @param mixed $file
    */
    function LoadFile($file);
    /**
    * auto generate doc.
    * @param mixed $ctr
    * @param mixed $article
    */
    function LoadView($ctr, $article);
}
/**
* RepresentIHtmlUriItem interface
*/
interface IHtmlUriItem{
    /**
    * auto generate doc.
    */    function getUri();
    /**
    * auto generate doc.
    * @param mixed $v
    */
    function setUri($v);
}
/**
* Interface for listener.
* @package IGK
*/
interface IListener{
    /**
    * Registers.
    * @param mixed $name
    * @param mixed $callback
    */
    function register($name, $callback);
}
/**
* RepresentIMailAttachmentContainer interface
*/
interface IMailAttachmentContainer{
    /**
    * auto generate doc.
    * @param mixed $content
    * @param mixed $type
    * @param mixed $cid the default value is null
    */
    function attachContent($content, $type=IGK_CT_PLAIN_TEXT, $cid=null);
    /**
    * auto generate doc.
    * @param mixed $file
    * @param mixed $type
    * @param mixed $cid the default value is null
    */
    function attachFile($file, $type=IGK_CT_PLAIN_TEXT, $cid=null);
}
/**
* notification message
*/
interface INotifyMessage {
    /**
    * auto generate doc.
    * @param mixed $message
    */
    function addError($message);
    /**
    * auto generate doc.
    * @param mixed $keymessage
    */
    function addErrorr($keymessage);
    /**
    * auto generate doc.
    * @param mixed $message
    */
    function addInfo($message);
    /**
    * auto generate doc.
    * @param mixed $keymessage
    */
    function addInfor($keymessage);
    /**
    * auto generate doc.
    * @param mixed $message
    */
    function addMsg($message);
    /**
    * auto generate doc.
    * @param mixed $keymessage
    */
    function addMsgr($keymessage);
    /**
    * auto generate doc.
    * @param mixed $message
    */
    function addSuccess($message);
    /**
    * auto generate doc.
    * @param mixed $keymessage
    */
    function addSuccessr($keymessage);
    /**
    * auto generate doc.
    * @param mixed $message
    */
    function addWarning($message);
    /**
    * auto generate doc.
    * @param mixed $keymessage
    */
    function addWarningr($keymessage);
}
/**
* RepresentIParamHostService interface
*/
interface IParamHostService{
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $default the default value is null
    */
    function getParam($name, $default=null);
    /**
    * auto generate doc.
    */    function getParamKeys();
    /**
    * auto generate doc.
    */    function resetParam();
    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $value
    */
    function setParam($name, $value);
}
/**
* RepresentIParentDocumentHost interface
*/
interface IParentDocumentHost{
    /**
    * auto generate doc.
    * @param mixed $document
    */
    function BindScriptTo($document);
    /**
    * auto generate doc.
    */    function getDoc();
}
/**
*  represent query result interface
*/
interface IQueryResult{
    /**
    * auto generate doc.
    * @param mixed $index
    */
    function getRowAtIndex($index);
    /**
    * get rows
    * @return array 
    */
    function getRows();
    /**
     * get a column list
     * @return array 
     */
    function getColumns();
}
/**
* RepresentISystemUser interface
*/
interface ISystemUser {
    /**
    * auto generate doc.
    */    function getLogin();
}
/**
* RepresentIUriActionListener interface
*/
interface IUriActionListener{
    /**
    * auto generate doc.
    * @param mixed $e
    * @param mixed $render the default value is 1
    */
    function invokeUriPattern($e, $render=1);
    /**
    * auto generate doc.
    * @param mixed $uri
    */
    function matche($uri);
}
/**
* RepresentIUriActionRegistrableController interface
*/
interface IUriActionRegistrableController{
    /**
    * auto generate doc.
    */    function getBasicUriPattern();
    /**
    * registrated invocation uri
    */
    function getRegInvokeUri();
    /**
    * auto generate doc.
    */    function getRegUriAction();
}
/**
* RepresentIUserController interface
*/
interface IUserController{
    /**
    * auto generate doc.
    */    function connect();
    /**
    * auto generate doc.
    */    function signup();
}
/**
* RepresentIWebAdministrativeCtrl interface
*/
interface IWebAdministrativeCtrl {
    /**
    * auto generate doc.
    */    function getConfigNode();
}
/**
* RepresentIWebPageChildCtrontroller interface
*/
interface IWebPageChildCtrontroller{
    /**
    * auto generate doc.
    */    function getWebParentCtrl();
}
/**
* db manager interface
*/
interface IDbManager {
    /**
    * close database 
    * @param ?bool $leaveopen default value is false
    */
    function close($leaveopen=false);
    /**
    * open/connect to data base 
    * @return mixed
    */
    function connect();
}
/**
* Interface for db sqlmanager.
* @package IGK
*/
interface IDbSQLManager extends IDbManager{
    /**
    * auto generate doc.
    * @param mixed $tableName
    */
    function dropTable(string $tableName);
} 
/**
* RepresentIDataController interface
*/
interface IDataController extends IController {
    /**
    * auto generate doc.
    */    function getDataAdapterName();
    /**
    * return primary data table info or mixed array of table info
    */
    function getDataTableInfo();
    /**
    * auto generate doc.
    */    function getDataTableName();
}
/**
* RepresentIWebController interface
*/
interface IWebController extends IController {
    /**
    * auto generate doc.
    */    function getChilds();
    /**
    * auto generate doc.
    * @param mixed $ctrl
    */
    function regChildController($ctrl);
    /**
    * auto generate doc.
    * @param mixed $ctrl
    */
    function unregChildController($ctrl);
}
/**
* Interface for get value.
* @package IGK
*/
interface IGetValue{
    /**
     * return a value
     * @return mixed 
     */
    function getValue();
}
/**
* RepresentIQueryConditionalExpression interface
*/
interface IQueryConditionalExpression extends IGetValue {
    /**
    * auto generate doc.
    * @param mixed $expression
    * @param mixed $operator
    */
    function add($expression, $operator="AND");
    /**
    * auto generate doc.
    */    function getCount();
    /**
    * auto generate doc.
    * @param mixed $expression
    */
    function remove($expression);
}
/**
* RepresentIWebPageController interface
*/
interface IWebPageController{
    /**
    * auto generate doc.
    * @param mixed $file
    */
    function loadWebTheme($file);
    /**
    * 
    * @param mixed $uri
    */
    function manageErrorUriRequest($uri);
}