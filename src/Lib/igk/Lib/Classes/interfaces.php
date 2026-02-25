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
  * 
  * @package 
  * @property array $objs object list
  * @property array $ids id list
  * @property array $uris uri list 
  * @property array $srcs src list
  */
 interface IComponentInfo{
 }

 interface IDbGetTableReferenceHandler{
    public function getDataTablesReference(& $table);
    public function resolvTableDefinition(string $table);
}


/**
* represent IIAction Result interface
*/
interface IActionResult{
    /**
    * 
    */
    function index();
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
interface INodeController extends IController{
    /**
    * retriev e the target node  
    */
    function getTargetNode() : HtmlNode;
    /**
    * 
    */
    function getTargetNodeId();
}
interface IViewController{    
    function View();
}
/**
* RepresentIControllerInitListener interface
*/
interface IControllerInitListener{
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
* RepresentICssCtrlHost interface
*/
interface ICssCtrlHost{
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
* RepresentICtrlDirManagement interface
*/
interface ICtrlDirManagement{
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
* base data adapter operation
*/
interface IDataAdapter{
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
* RepresentIDataTable interface
*/
interface IDataTable{}
/**
* RepresentIDbUtility interface
*/
interface IDbUtility{
    /**
    * 
    * @param string $table table name
    * @param mixed $obj
    * @param mixed $leaveopen the default value is false
    */
    function insertIfNotExists(string $table, $obj, $leaveopen=false);
}
interface IDbModel{
	function getTable();
}
///<note>all id are mixed of string or array properties</summary>
/**
* engine form builder interface
*/
interface IFormBuilderEngine{
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
* RepresentIFrameController interface
*/
interface IFrameController{
    /**
    * 
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
interface IHtmlCookieItem{
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
* RepresentIHtmlLoadContent interface
*/
interface IHtmlLoadContent {
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
* RepresentIHtmlUriItem interface
*/
interface IHtmlUriItem{
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
interface IListener{
    function register($name, $callback);
}
/**
* RepresentIMailAttachmentContainer interface
*/
interface IMailAttachmentContainer{
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
interface INotifyMessage {
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
* RepresentIParamHostService interface
*/
interface IParamHostService{
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
* RepresentIParentDocumentHost interface
*/
interface IParentDocumentHost{
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
interface IQueryResult{
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
* RepresentISystemUser interface
*/
interface ISystemUser {
    /**
    * 
    */
    function getLogin();
}
/**
* RepresentIUriActionListener interface
*/
interface IUriActionListener{
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
* RepresentIUriActionRegistrableController interface
*/
interface IUriActionRegistrableController{
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
* RepresentIUserController interface
*/
interface IUserController{
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
* RepresentIWebAdministrativeCtrl interface
*/
interface IWebAdministrativeCtrl {
    /**
    * 
    */
    function getConfigNode();
}
/**
* RepresentIWebPageChildCtrontroller interface
*/
interface IWebPageChildCtrontroller{
    /**
    * 
    */
    function getWebParentCtrl();
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
interface IDbSQLManager extends IDbManager{
    /**
    * 
    * @param mixed $tableName
    */
    function dropTable(string $tableName);
}
/**
* represent a module listener interface
*/
// interface IAppModuleListener extends IConfigController{
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
* RepresentIDataController interface
*/
interface IDataController extends IController {
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
* RepresentIWebController interface
*/
interface IWebController extends IController {
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
* RepresentIWebPageController interface
*/
interface IWebPageController{
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