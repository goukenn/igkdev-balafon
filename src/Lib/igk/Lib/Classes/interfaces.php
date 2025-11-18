<?php
// @author: C.A.D. BONDJE DOUE
// @filename: interfaces.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Database\IIGKDatabaseCreator;
use IGK\System\Configuration\Controllers\IConfigController;
/**
* represent IIAction Result interface
*/
interface IIGKActionResult{
    /**
    * 
    */
    function index();
} 
/**
* Represent IIGKController interface
*/
interface IIGKController{
    /**
    * 
    */
    function getName();
}
interface IIGKNodeController extends IIGKController{
    /**
    * 
    */
    function getTargetNode();
    /**
    * 
    */
    function getTargetNodeId();
}
interface IIGKViewController{    
    function View();
}
/**
* Represent IIGKControllerInitListener interface
*/
interface IIGKControllerInitListener{
    /**
    * 
    * @param mixed $name
    */
    function addDir($name);
    /**
    * 
    * @param mixed $name
    * @param mixed $source
    */
    function addSource($name, $source, $override=true);
}
/**
* Represent IIGKCssCtrlHost interface
*/
interface IIGKCssCtrlHost{
    /**
    * 
    */
    function bindCss();
    /**
    * 
    * @param mixed $doc the default value is null
    */
    function getIsCssActive($doc=null);
}
/**
* Represent IIGKCtrlDirManagement interface
*/
interface IIGKCtrlDirManagement{
    /**
    * 
    */
    function getDataDir();
    /**
    * 
    */
    function getDeclaredDir() : string;
    /**
    * 
    */
    function getName();
    /**
    * 
    */
    function getResourcesDir();
    /**
    * 
    */
    function getStylesDir();
    /**
    * 
    */
    function getViewDir();
}
/**
* Represent IIGKDataAdapter interface
*/
interface IIGKDataAdapter{
    /**
    * 
    */
    function selectCount(string $tbname, ?array $where = null, ?array $options = null);
    /**
    * 
    */
    function setForeignKeyCheck($check);
}
/**
* Represent IIGKDataTable interface
*/
interface IIGKDataTable{}
/**
* Represent IIGKDbUtility interface
*/
interface IIGKDbUtility{
    /**
    * 
    * @param string $table table name
    * @param mixed $obj
    * @param mixed $leaveopen the default value is false
    */
    function insertIfNotExists(string $table, $obj, $leaveopen=false);
}
interface IIGKDbModel{
	function getTable();
}
///<note>all id are mixed of string or array properties</summary>
/**
* engine form builder interface
*/
interface IIGKFormBuilderEngine{
    /**
    * 
    * @param mixed $id
    * @param mixed $type the default value is 'submit'
    * @param mixed $text the default value is null
    */
    function addButton($id, $type='submit', $text=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    * @param mixed $attribs the default value is null
    */
    function addCheckbox($id, $value=null, $attribs=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $type the default value is 'text'
    * @param mixed $style the default value is null
    */
    function addControl($id, $type='text', $style=null);
    /**
    * 
    */
    function addGroup();
    /**
    * 
    * @param mixed $id
    * @param mixed $class the default value is null
    */
    function addLabel($id, $class=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    * @param mixed $type the default value is 'text'
    * @param mixed $style the default value is null
    */
    function addLabelControl($id, $value=null, $type='text', $style=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $entries
    * @param mixed $filter the default value is null
    */
    function addLabelSelect($id, $entries, $filter=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    */
    function addLabelTextarea($id, $value=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    * @param mixed $attribs the default value is null
    */
    function addRadioButton($id, $value=null, $attribs=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    */
    function addTextarea($id, $value=null);
    /**
    * 
    * @param mixed $id
    * @param mixed $value the default value is null
    * @param mixed $attribs the default value is null
    */
    function addTextfield($id, $value=null, $attribs=null);
    /**
    * 
    */
    function getView();
    /**
    * 
    * @param mixed $host
    */
    function setView($host);
}
/**
* Represent IIGKFrameController interface
*/
interface IIGKFrameController{
    /**
    * 
    * @param mixed $id
    * @param mixed $frame
    * @param mixed $remove the default value is true
    */
    function ContainFrame($id, $frame, $remove=true);
}
/**
* Represent IIGKHtmlComponent interface
*/
interface IIGKHtmlComponent{
    /**
    * 
    */
    function getComponentId();
    /**
    * 
    * @param mixed $uri
    */
    function getComponentUri($uri);
    /**
    * 
    */
    function getController();
    /**
    * 
    * @param mixed $listener
    * @param mixed $param the default value is null
    */
    function setComponentListener($listener, $param=null);
}
/**
* use to indicate that an element can store a cookie to client size
*/
interface IIGKHtmlCookieItem{
    /**
    * 
    */
    function getCookieId();
    /**
    * 
    * @param mixed $v
    */
    function setCookieId($v);
}
/**
* Represent IIGKHtmlLoadContent interface
*/
interface IIGKHtmlLoadContent {
    /**
    * 
    * @param mixed $data
    * @param mixed $context the default value is null
    */
    function LoadExpression($data, $context=null);
    /**
    * 
    * @param mixed $file
    */
    function LoadFile($file);
    /**
    * 
    * @param mixed $ctr
    * @param mixed $article
    */
    function LoadView($ctr, $article);
}
/**
* Represent IIGKHtmlUriItem interface
*/
interface IIGKHtmlUriItem{
    /**
    * 
    */
    function getUri();
    /**
    * 
    * @param mixed $v
    */
    function setUri($v);
}
interface IIGKListener{
    function register($name, $callback);
}
/**
* Represent IIGKMailAttachmentContainer interface
*/
interface IIGKMailAttachmentContainer{
    /**
    * 
    * @param mixed $content
    * @param mixed $type the default value is IGK_CT_PLAIN_TEXT
    * @param mixed $cid the default value is null
    */
    function attachContent($content, $type=IGK_CT_PLAIN_TEXT, $cid=null);
    /**
    * 
    * @param mixed $file
    * @param mixed $type the default value is IGK_CT_PLAIN_TEXT
    * @param mixed $cid the default value is null
    */
    function attachFile($file, $type=IGK_CT_PLAIN_TEXT, $cid=null);
}
/**
* notification message
*/
interface IIGKNotifyMessage {
    /**
    * 
    * @param mixed $message
    */
    function addError($message);
    /**
    * 
    * @param mixed $keymessage
    */
    function addErrorr($keymessage);
    /**
    * 
    * @param mixed $message
    */
    function addInfo($message);
    /**
    * 
    * @param mixed $keymessage
    */
    function addInfor($keymessage);
    /**
    * 
    * @param mixed $message
    */
    function addMsg($message);
    /**
    * 
    * @param mixed $keymessage
    */
    function addMsgr($keymessage);
    /**
    * 
    * @param mixed $message
    */
    function addSuccess($message);
    /**
    * 
    * @param mixed $keymessage
    */
    function addSuccessr($keymessage);
    /**
    * 
    * @param mixed $message
    */
    function addWarning($message);
    /**
    * 
    * @param mixed $keymessage
    */
    function addWarningr($keymessage);
}
/**
* Represent IIGKParamHostService interface
*/
interface IIGKParamHostService{
    /**
    * 
    * @param mixed $name
    * @param mixed $default the default value is null
    */
    function getParam($name, $default=null);
    /**
    * 
    */
    function getParamKeys();
    /**
    * 
    */
    function resetParam();
    /**
    * 
    * @param mixed $name
    * @param mixed $value
    */
    function setParam($name, $value);
}
/**
* Represent IIGKParentDocumentHost interface
*/
interface IIGKParentDocumentHost{
    /**
    * 
    * @param mixed $document
    */
    function BindScriptTo($document);
    /**
    * 
    */
    function getDoc();
}
/**
*  represent query result interface
*/
interface IIGKQueryResult{
    /**
    * 
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
* Represent IIGKSystemUser interface
*/
interface IIGKSystemUser {
    /**
    * 
    */
    function getLogin();
}
/**
* Represent IIGKUriActionListener interface
*/
interface IIGKUriActionListener{
    /**
    * 
    * @param mixed $e
    * @param mixed $render the default value is 1
    */
    function invokeUriPattern($e, $render=1);
    /**
    * 
    * @param mixed $uri
    */
    function matche($uri);
}
/**
* Represent IIGKUriActionRegistrableController interface
*/
interface IIGKUriActionRegistrableController{
    /**
    * 
    */
    function getBasicUriPattern();
    /**
    * registrated invocation uri
    */
    function getRegInvokeUri();
    /**
    * 
    */
    function getRegUriAction();
}
/**
* Represent IIGKUserController interface
*/
interface IIGKUserController{
    /**
    * 
    */
    function connect();
    /**
    * 
    */
    function signup();
}
/**
* Represent IIGKWebAdministrativeCtrl interface
*/
interface IIGKWebAdministrativeCtrl {
    /**
    * 
    */
    function getConfigNode();
}
/**
* Represent IIGKWebPageChildCtrontroller interface
*/
interface IIGKWebPageChildCtrontroller{
    /**
    * 
    */
    function getWebParentCtrl();
}
/**
* db manager interface
*/
interface IIGKdbManager {
    /**
    * 
    * @param mixed $leaveopen the default value is false
    */
    function close($leaveopen=false);
    /**
    * 
    */
    function connect();
    /**
    * 
    * @param mixed $tableName
    */
    function dropTable($tableName);
}
/**
* represent a module listener interface
*/
// interface IIGKAppModuleListener extends IConfigController{
//     const DATA=1;
//     const DATA2=self::DATA + 5;
//     const DATA3=self::DATA2;
//     //     /**
//     * 
//     */
//     function getBaseUri();
//     //     /**
//     * 
//     */
//     function getConfigs();
//     //     //     /**
//     * 
//     * @param mixed $n
//     */
//     function getTable($n);
// }
/**
* Represent IIGKDataController interface
*/
interface IIGKDataController extends IIGKController {
    /**
    * 
    */
    function getDataAdapterName();
    /**
    * return primary data table info or mixed array of table info
    */
    function getDataTableInfo();
    /**
    * 
    */
    function getDataTableName();
}
/**
* Represent IIGKWebController interface
*/
interface IIGKWebController extends IIGKController {
    /**
    * 
    */
    function getChilds();
    /**
    * 
    * @param mixed $ctrl
    */
    function regChildController($ctrl);
    /**
    * 
    * @param mixed $ctrl
    */
    function unregChildController($ctrl);
}
interface IIGKGetValue{
    /**
     * return a value
     * @return mixed 
     */
    function getValue();
}
/**
* Represent IIGKQueryConditionalExpression interface
*/
interface IIGKQueryConditionalExpression extends IIGKGetValue {
    /**
    * 
    * @param mixed $expression
    * @param mixed $operator the default value is "AND"
    */
    function add($expression, $operator="AND");
    /**
    * 
    */
    function getCount();
    /**
    * 
    * @param mixed $expression
    */
    function remove($expression);
}
/**
* Represent IIGKWebPageController interface
*/
interface IIGKWebPageController{
    /**
    * 
    * @param mixed $file
    */
    function loadWebTheme($file);
    /**
    * 
    * @param mixed $uri
    */
    function manageErrorUriRequest($uri);
}