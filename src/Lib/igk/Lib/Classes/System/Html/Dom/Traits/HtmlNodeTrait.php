<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20220901 23:42:51
namespace IGK\System\Html\Dom\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom\Traits
*/
trait HtmlNodeTrait{
	
	// /**
	//  * create winui-a
	//  * @param mixed $href
	//  * @param mixed $attributes
	//  * @param mixed $index
	//  */
	// public function a($href='',$attributes=null,$index=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function a_get($uri,$complete=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function a_post($uri,$complete=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-abbr
	//  * @param mixed $title
	//  */
	// public function abbr($title=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param string $uri target reference
	//  * @param string $type button type
	//  * @param string $role role 
	//  * @return HtmlItemBase<mixed, string> 
	//  * @throws IGKException 
	//  */
	// public function abtn($uri='',$type='',$role=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function accordeon(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $items 
	//  * @param mixed $engine 
	//  * @param string $tag 
	//  * @param string $item 
	//  * @return HtmlItemBase<mixed, string> 
	//  * @throws IGKException 
	//  */
	// public function accordeon_menus($items,$engine=null,$tag='',$item=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-aclearsandreload
	//  */
	// public function aclearsandreload(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-actionbar
	//  * @param array|callable $actions array or 
	//  */
	// public function actionbar($actions=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function actiongroup(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function actions($actionlist){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function address(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajsbutton
	//  * @param mixed $code
	//  * @param mixed $type
	//  */
	// public function ajsbutton($code,$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * @param mixed $optionsJSON Options
	//  */
	// public function ajspickfile($u,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  represent an ajx link
	//  * @param mixed $replacemodethe content mode . value (content|node)
	//  */
	// public function ajxa($lnk=null,$target='',$replacemode='',$method=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxabutton
	//  * @param mixed $link
	//  */
	// public function ajxabutton($link){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * append async content
	//  * @param mixed $cibling
	//  */
	// public function ajxappendto($cibling){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * change the document code in ajx context
	//  */
	// public function ajxdoctitle($title){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * represent ajx form
	//  */
	// public function ajxform($uri=null,$target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxlnkreplace
	//  * @param mixed $target
	//  */
	// public function ajxlnkreplace($target=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxpaginationview
	//  * @param mixed $baseuri url 
	//  * @param mixed $total total number of items
	//  * @param mixed $perpage items per page
	//  * @param mixed $selected selected page
	//  * @param mixed $target cibling
	//  */
	// public function ajxpaginationview($baseuri,$total,$perpage,$selected=1,$target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * ajx div component used to load a file
	//  * @param mixed $paramjson data. {accept:'image/*, text/xml', start:callback, progress:callback}
	//  */
	// public function ajxpickfile($uri,$param=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxreplacecontent
	//  * @param mixed $uri
	//  * @param mixed $method
	//  */
	// public function ajxreplacecontent($uri,$method=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxreplacesource
	//  * @param mixed $selection
	//  */
	// public function ajxreplacesource($selection){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * add tab component
	//  */
	// public function ajxtabcomponent($host,$name){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function ajxtabcontrol(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ajxupdateview
	//  * @param mixed $cibling
	//  */
	// public function ajxupdateview($cibling){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function ajxuriloader($uri,$append=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function angularapp($directive,$script=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * add a link that will do a post request
	//  * @param mixed $uri 
	//  * @return HtmlItemBase<mixed, mixed> 
	//  * @throws IGKException 
	//  */
	// public function apost($uri){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * application header bar
	//  * @param BaseController $controller 
	//  * @return HtmlNode 
	//  */
	// public function app_hearder_bar(\IGK\Controllers\BaseController $controller){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * @param mixed $app
	//  * 
	//  * @param mixed $baduri the default value is null
	//  * @param mixed $goodUri the default value is null
	//  */
	// public function apploginform($app,$baduri=null,$goodUri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to render data
	//  */
	// public function arraydata($tab){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-arraylist
	//  * @param mixed $list
	//  * @param mixed $tag
	//  * @param mixed $closurecallback
	//  */
	// public function arraylist($list,$tag='',$callback=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * bind article
	//  */
	// public function article(?\IGK\Controllers\BaseController $ctrl=null,?string $name=null,$raw=array (
	// ),$showAdminOption=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//   * 
	//   * @param bool $condition 
	//   * @param mixed $args 
	//   * @return HtmlAssertNode 
	//   * @throws IGKException 
	//   */
	// public function assertnode(bool $condition,...$args){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function attr_expression($p=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * render autho community node - system community link
	//  * @return HtmlItemBase 
	//  * @throws IGKException 
	//  */
	// public function author_community(?array $options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-backgroundlayer
	//  */
	// public function backgroundlayer($imgPath=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $v
	//  */
	// public function badge($v){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function balafoncomponentjs(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-balafonjs
	//  * @param mixed $autoremove
	//  */
	// public function balafonjs($autoremove=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function balafonvideo($ctrl,$index,$attr=null,$list=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * use to configure node before next cibling rendering 
	//  * @param callback #Parameter#6fbcd033 
	//  * @return \IGK\System\Html\Dom\HtmlBeforeRenderNextSiblingChildrenCallbackNode 
	//  */
	// public function beforerendernextsibling(callable $callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-bindarticle
	//  * @param mixed $ctrl
	//  * @param mixed $name
	//  * @param mixed $data
	//  * @param mixed $showAdminOption
	//  */
	// public function bindarticle($ctrl,$name,$data=null,$showAdminOption=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-bindcontent
	//  * @param mixed $content
	//  * @param mixed $entries
	//  * @param mixed $ctrl
	//  */
	// public function bindcontent($content,$entries,$ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * target the menuu display
	//  * @param mixed $target 
	//  * @return object 
	//  * @throws ReflectionException 
	//  * @throws Exception 
	//  */
	// public function bindmenu($target){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param array|\IGK\Controllers\BaseController $data data to bind
	//  * @param mixed $uri uri to load
	//  * @param mixed $name 
	//  * @param null|bool $production 
	//  * @return null|HtmlItemBase 
	//  * @throws IGKException 
	//  */
	// public function bindscript($data,$uri,$name,?bool $production=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-blocknode
	//  */
	// public function blocknode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcbutton(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmccheckbox($id,$value=null,$array=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmccombobox($id,$data=null,$index=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmclineripple(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $listener
	//  */
	// public function bmcloginpage($listener){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcradio($id,$value=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcripple(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcroundtool(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcshape($type=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmcsurface(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmctextarea($id,$value=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmctextfield($id,$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmctextlogofield($id,$logo){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmctextsearchfield($id,$value=null,$uri=null,$target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bmctooltip(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-bodybox
	//  */
	// public function bodybox(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function bootstrap_langselector(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function boxdialog(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-btn
	//  * @param mixed $name
	//  * @param mixed $value
	//  * @param mixed $type
	//  * @param mixed $attributes
	//  */
	// public function btn($name,$value,$type='',$attributes=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * build select node
	//  */
	// public function buildselect($name,$rows,$idk,$callback=null,$selected=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-bullet
	//  */
	// public function bullet(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a button
	//  */
	// public function button($id=null,$buttontype=0,$type=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function calcnode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function calendar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-canvabalafonscript
	//  * @param mixed $uri
	//  */
	// public function canvabalafonscript($uri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a canva editor surface
	//  */
	// public function canvaeditorsurface(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-cardid
	//  * @param mixed $src
	//  * @param mixed $ctrl
	//  */
	// public function cardid($src=null,$ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function carousel(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-cell
	//  */
	// public function cell(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-cellrow
	//  */
	// public function cellrow(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-centerbox
	//  * @param mixed $content
	//  */
	// public function centerbox($content=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function checkbox($id,$value=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-circlewaiter
	//  */
	// public function circlewaiter(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function clearboth(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-clearfloatbox
	//  * @param mixed $t
	//  */
	// public function clearfloatbox($t=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-cleartab
	//  */
	// public function cleartab(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-clonenode
	//  * @param mixed $node
	//  */
	// public function clonenode(\IGK\System\Html\Dom\HtmlItemBase $node){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create base php code
	//  */
	// public function code($type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-col
	//  * @param mixed $clname
	//  */
	// public function col($clname=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * column view item
	//  */
	// public function colviewbox(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $id
	//  * @param mixed $tab
	//  * @param mixed $options the default value is null
	//  */
	// public function combobox($id,$tab,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function comment(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function commentzone(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-communitylink
	//  * @param mixed $name
	//  * @param mixed $link
	//  */
	// public function communitylink($name,$link){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-communitylinks
	//  * @param mixed $tab
	//  */
	// public function communitylinks($tab,?array $options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function communitynode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to create component
	//  */
	// // public function component($listener,$typename,$regName,$unregister=0){
	// // 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// // }
	// /**
	//  * create a node that will only be visible on conditional callback is evaluted to true
	//  */
	// public function conditionalnode($conditioncallback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $menuList
	//  * @param mixed $selected
	//  */
	// public function configsubmenu($menuList,$selected){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//      * bind contact block node helper
	//      * @param mixed $raw 
	//      * @return mixed 
	//      * @throws IGKException 
	//      */
	// public function contact_block($raw){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-container
	//  */
	// public function container(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function containerrowcol($style=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-cookiewarning
	//  * @param mixed $warnurl
	//  */
	// public function cookiewarning($warnurl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-copyright
	//  * @param mixed $ctrl
	//  */
	// public function copyright($ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-csscomponentstyle
	//  * @param mixed $file
	//  * @param mixed $host
	//  */
	// public function csscomponentstyle($file,$host=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  add css link
	//  * @param mixed $hrefmixed : string, object(that implement getValue function and refName properties) or array
	//  */
	// public function csslink($href,$temp=0,$defer=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * represent a css style element
	//  */
	// public function cssstyle($id,$minfile=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-ctrlview
	//  * @param mixed $viewview to show
	//  * @param mixed $ctrlcontroller that will handle the view
	//  * @param mixed $paramsparams to pas to views
	//  */
	// public function ctrlview($view,$ctrl,$params=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create data schema
	//  * @return XmlNode 
	//  */
	// public function dataschema(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a data base schema node 
	//  */
	// public function dbdataschema(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-dbentriescallback
	//  * @param mixed $target
	//  * @param mixed $closurecallback
	//  * @param mixed $queryResult
	//  * @param mixed $fallback
	//  */
	// public function dbentriescallback($target,$callback,$queryResult,$fallback=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-dbresult
	//  * @param mixed $r
	//  * @param mixed $max
	//  */
	// public function dbresult($r,$uri,$selected,$max=-1,$target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * DataBase select component
	//  */
	// public function dbselect($name,$result,$callback=null,$valuekey=IGK_FD_ID){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function dbtableview($tabResult,$theader=null,$header_prefix=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * defer css link loading
	//  * @param mixed $href 
	//  * @return null 
	//  * @throws IGKException 
	//  */
	// public function defercsslink($href){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function definition($title,$def){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function definitions($args){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  create a dialog host that will not being displayed<
	//  * @param mixed $title
	//  */
	// public function dialog($title=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function dialog_circle_waiter(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a dialog box
	//  * @param mixed $title
	//  */
	// public function dialogbox($title){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  create a dialog box content
	//  */
	// public function dialogboxcontent(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create dialogbox options node
	//  */
	// public function dialogboxoptions(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-divcontainer
	//  * @param mixed $attribs
	//  */
	// public function divcontainer($attribs=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function dl(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-domainlink
	//  * @param mixed $src
	//  */
	// public function domainlink($src){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function dropdown_button($text=null,?array $button_attribs=null,?array $items=null,$itemCallable=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function dumpdata($data){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * engine control editor
	//  */
	// public function enginecontrol($name,$type){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-error404
	//  * @param mixed $title
	//  * @param mixed $m
	//  */
	// public function error404($title,$m){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-expo
	//  */
	// public function expo(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $raw
	//  * @param mixed $ctrl the default value is null
	//  */
	// public function expression_node($raw,$ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function extends($parentview){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebook_login_button(?array $options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebookcomments($uri){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebookfollowusbutton($id,$layout=null,$theme=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebooklikebutton($showface=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebooksharebutton(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function facebooktimeline($id){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * load field list to parent
	//  * @param array $fielddata
	//  * @param null|array $datasource 
	//  * @param null|object $engine to use 
	//  * @param string $tagname
	//  * @return mixed 
	//  * @throws IGKException 
	//  */
	// public function fields(array $fielddata,?array $datasource=null,?object $engine=null,?string $tag=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  a BJS's class control. used to show on scroll visibility.
	//  */
	// public function fixedactionbar($targetid='',$offset=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function floatingform(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function followusbutton($name,$uid){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a font symbol.
	//  */
	// public function fontsymbol($name,$code){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function form($uri='',$method='',$notitle=false,$nofoot=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * post urit using data form
	//  * @param string $uri 
	//  * @return HtmlANode<mixed, mixed> 
	//  * @throws IGKException 
	//  */
	// public function form_post(string $uri){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-formactionbutton
	//  * @param mixed $id
	//  * @param mixed $value
	//  * @param mixed $uri
	//  * @param mixed $method
	//  * @param mixed $text
	//  */
	// public function formactionbutton($id,$value,$uri,$method='',$text=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function formcref(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $formfields
	//  * @param mixed $engine the default value is null
	//  */
	// public function formfields($formfields,$engine=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function formgroup(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-formusagecondition
	//  */
	// public function formusagecondition(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-frame
	//  */
	// public function frame(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-framedialog
	//  * @param mixed $id
	//  * @param mixed $ctrl
	//  * @param mixed $closeuri
	//  * @param mixed $reloadcallback
	//  */
	// public function framedialog($id,$ctrl,$closeuri='',$reloadcallback=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function gallery(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $ctrl
	//  * @param mixed $folder
	//  * @param mixed $ignorethumb the default value is 1
	//  */
	// public function galleryfolder($ctrl,$folder,$ignorethumb=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * bind google material icons
	//  * @param mixed $name 
	//  * @param string $title 
	//  * @param string $type 
	//  * @return object 
	//  * @throws ReflectionException 
	//  * @throws IGKException 
	//  */
	// public function google_icon($name,$title='',$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * creaate google login button
	//  * @return HtmlNode 
	//  */
	// public function google_login_button(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function googlecirclewaiter(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * add google follows us button
	//  */
	// public function googlefollowusbutton($id,$height=15,$rel='',$annotation=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * add google maps javascript api node
	//  */
	// public function googlejsmaps($data=null,$apikey=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function googlelinewaiter(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $loc
	//  */
	// public function googlemapgeo($loc,$apikey=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function googleoauthlink($tab){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function googleoth2button($url,$gclient){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a grid node
	//  */
	// public function grid(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function grid_selection($uri='',$qname='',$ajx=false,$ajxtarget=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function headerbar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function hiddenfields(array $fields){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-hlineseparator
	//  */
	// public function hlineseparator(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * call hook to render content on node 
	//  * @param mixed $hook 
	//  * @param mixed $args 
	//  * @return HtmlNoTagNode 
	//  */
	// public function hook($hook,...$args){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create hook node to update content on render
	//  */
	// public function hooknode($hook,?string $context=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create  winui-horizontalpageview
	//  */
	// public function horizontalpageview(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * host callable to 
	//  * @param callable $callback 
	//  * @return mixed 
	//  * @throws Exception 
	//  * @throws IGKException 
	//  */
	// public function host(callable $callback,...$args){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * Hosted object data. will pass the current node to callback as first argument
	//  */
	// public function hostobdata($callback,$host=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-hscrollbar
	//  */
	// public function hscrollbar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-hsep
	//  */
	// public function hsep(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-htmlnode
	//  * @param mixed $tag
	//  */
	// public function htmlnode($tag){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to render a pick a huebar value
	//  */
	// public function huebar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-igkcopyright
	//  */
	// public function igkcopyright(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-igkgloballangselector
	//  */
	// public function igkgloballangselector(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-igkglobalthemeselector
	//  */
	// public function igkglobalthemeselector(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-headerbar
	//  * @param string $title
	//  * @param mixed $baseuri
	//  */
	// public function igkheaderbar($title,$baseuri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-igksitemap
	//  */
	// public function igksitemap(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-imagenode
	//  */
	// public function imagenode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function img($src=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-imglnk
	//  */
	// public function imglnk(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $ctrl 
	//  * @param mixed $view 
	//  * @param mixed|null $params 
	//  * @return mixed 
	//  * @throws IGKException 
	//  */
	// public function include($ctrl,$view,$params=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * include local file as javascript
	//  */
	// public function include_js($file){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-innerimg
	//  */
	// public function innerimg(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function input($id=null,$type='',$value=null,$attributes=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $text the default value is 'Jombotron'
	//  */
	// public function jombotron($text=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * mark parent node with autofixing with. 
	//  */
	// public function js_autofix_width(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsaextern
	//  * @param mixed $method
	//  * @param mixed $args
	//  */
	// public function jsaextern($method,$args=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * inject namespace with properties js namespace
	//  */
	// public function jsbindns(string $namespace,array $data,$coredef=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsbtn
	//  * @param mixed $script
	//  * @param mixed $value
	//  */
	// public function jsbtn($script,$value=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsbtnshowdialog
	//  * @param mixed $id
	//  */
	// public function jsbtnshowdialog($id){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsbutton
	//  * @param mixed $js
	//  */
	// public function jsbutton($js){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * use to close node on client side
	//  * @return IGK\System\Html\Dom\HtmlNode 
	//  */
	// public function jsclone(string $target,?string $complete=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsclonenode
	//  * @param mixed $node
	//  */
	// public function jsclonenode($node){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsclonetarget
	//  * @param mixed $selector
	//  * @param mixed $tag
	//  */
	// public function jsclonetarget($selector,$tag=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jslogger
	//  */
	// public function jslogger(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to call ready invoke
	//  */
	// public function jsreadyscript($script){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-jsreplaceuri
	//  * @param mixed $uri
	//  */
	// public function jsreplaceuri($uri){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to load manually script tag
	//  */
	// public function jsscript($file,$minify=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function jsview(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-label
	//  * @param mixed $for
	//  * @param mixed $key
	//  */
	// public function label($for=null,$key=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-labelinput
	//  * @param mixed $id
	//  * @param mixed $text
	//  * @param mixed $type
	//  * @param mixed $value
	//  * @param mixed $attributes
	//  * @param mixed $require
	//  * @param mixed $description
	//  */
	// public function labelinput($id,$text,$type='',$value=null,$attributes=null,$require=false,$description=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//      * 
	//      * @param BaseController $controller 
	//      * @param mixed $activePage 
	//      * @return void 
	//      * @throws IGKException 
	//      */
	// public function layout(\IGK\Controllers\BaseController $controller,$activePage,...$params){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function layoutpresentation($type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-lborder
	//  */
	// public function lborder(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-linewaiter
	//  */
	// public function linewaiter(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-linkbtn
	//  * @param mixed $uri
	//  * @param mixed $img
	//  * @param mixed $width
	//  * @param mixed $height
	//  */
	// public function linkbtn($uri,$img,$width=16,$height=16){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function list($items,$callback=null,$ordered=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-componentnodecallback
	//  * @param mixed $listener
	//  * @param mixed $name
	//  * @param mixed $closurecallback
	//  */
	// public function livenodecallback($listener,$name,$callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function load_array(array $items,?string $tag=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $expression
	//  * @param mixed $data the default value is null
	//  */
	// public function localizabletext($expression,$data=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * helper: loop thru array<
	//  */
	// public function loop(iterable $array,?callable $callback=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * represent the loremIpSum zone
	//  * @param mixed $modeverbose node
	//  */
	// public function loremipsum($mode=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $href
	//  * @param mixed $text the default value is ""
	//  */
	// public function mailto($href,$text=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * dummry markdown document
	//  * @return MarkdownDocument 
	//  */
	// public function markdown_document(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-memoryusage-info tag
	//  * @return HtmlMemoryUsageInfoNode 
	//  */
	// public function memoryusageinfo(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function menu($tab,$selected=null,$uriListener=null,$callback=null,?\IGK\Models\Users $user=null,$tag='',$item=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function menukey($menus,$ctrl=null,$root='',$item='',$callback=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//   * create a menu layer node 
	//   * @return HtmlItemBase<mixed, string> 
	//   * @throws IGKException 
	//   */
	// public function menulayer(?string $target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function menulist($menuTab){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $items 
	//  * @param mixed $callback subitem menu initialize callback
	//  * @param string $subtag 
	//  * @param string $item 
	//  * @return HtmlItemBase<mixed, string> 
	//  * @throws IGKException 
	//  */
	// public function menus($items,$callback=null,$subtag='',$item='',?object $option=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-moreview
	//  * @param mixed $hide
	//  */
	// public function moreview($hide=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-msdialog
	//  * @param mixed $id
	//  */
	// public function msdialog($id=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-mstitle
	//  * @param mixed $key
	//  */
	// public function mstitle($key){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-navigationlink
	//  * @param mixed $target
	//  */
	// public function navigationlink($target){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function nbsp(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-newsletterregistration
	//  * @param mixed $uri
	//  * @param mixed $type
	//  * @param mixed $ajx
	//  */
	// public function newsletterregistration($uri,$type='',$ajx=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-notagnode
	//  */
	// public function notagnode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * shortcut to create ObData node with noTag to display
	//  */
	// public function notagobdata($content){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to add notification node
	//  */
	// public function notification($nodeType='',$notifyName=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to bind notify global ctrl message
	//  */
	// public function notifyhost($name='',$autohide=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-notifyhostbind
	//  * @param mixed $name
	//  * @param mixed $autohide
	//  */
	// public function notifyhostbind($name=null,$autohide=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-notifyzone
	//  * @param mixed $name
	//  * @param mixed $autohide
	//  * @param mixed $tag
	//  */
	// public function notifyzone($name=null,$autohide=1,$tag=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to add a node with buffer content
	//  */
	// public function obdata($data,$nodeType=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * bind object scripting for callable
	//  * @param callable $callback 
	//  * @return \IGK\System\Html\Dom\HtmlScriptNode
	//  */
	// public function obscript(callable $callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  create node on callback. create a callback object to send to this
	//  */
	// public function onrendercallback($callbackObj){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-page
	//  */
	// public function page(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function pagecenterbox(?callable $host=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  build pagination settings
	//  */
	// public function paginationview($baseuri,$total,$perpage,$selected=1,$ajx=0,$cookiepath=null,$target=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-panelbox
	//  */
	// public function panelbox(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-paneldialog
	//  * @param mixed $title
	//  * @param mixed $content
	//  * @param mixed $settings
	//  */
	// public function paneldialog($title,$content=null,$settings=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  parallax node view
	//  */
	// public function parallaxnode($uri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a paypal button
	//  * @return void 
	//  */
	// public function paypal_button(?array $option=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function picker_zone($uri,$accepts='',$complete=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-popupmenu
	//  */
	// public function popupmenu(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-pre tag
	//  * @param mixed $data 
	//  * @return HtmlNode 
	//  */
	// public function pre($data=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * print button
	//  */
	// public function printbtn($uri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-progressbar
	//  */
	// public function progressbar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create radio button
	//  * @param null|string $id 
	//  * @return HtmlNode<mixed, string> 
	//  */
	// public function radiobutton(?string $id=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function reactapp($name,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-readonlytextzone
	//  * @param mixed $file
	//  */
	// public function readonlytextzone($file){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-registermailform
	//  */
	// public function registermailform(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * renderging Expression
	//  */
	// public function renderingexpression($callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-repeatcontent
	//  * @param mixed $number
	//  */
	// public function repeatcontent($number){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function replace_uri($uri=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function resimg($name,$desc='',$width=16,$height=16){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-responsenode
	//  */
	// public function responsenode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-rollin
	//  */
	// public function rollin(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-roundbullet
	//  */
	// public function roundbullet(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-row
	//  */
	// public function row(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  add a row column
	//  * @param mixed $classLevel css classname that init the column level
	//  */
	// public function rowcolumn($classLevel=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-rowcontainer
	//  */
	// public function rowcontainer(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function script($script=null,$version=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function script_var($name,$data,$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-scrollimg
	//  * @param mixed $src
	//  */
	// public function scrollimg($src){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to load scroll Loader Item
	//  */
	// public function scrollloader($src){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function search_box($value=null,$name='',$param=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function searchbox(string $uri,$id=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * search button view
	//  * @param mixed $uri
	//  */
	// public function searchbutton(string $uri,?string $id=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * add search field
	//  * @param string $id 
	//  * @return HtmlItemBase<mixed, string> 
	//  * @throws IGKException 
	//  */
	// public function searchfield($id=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-sectiontitle
	//  * @param mixed $level
	//  */
	// public function sectiontitle($level=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * help create a select node
	//  * @param mixed $id 
	//  * @return HtmlNode 
	//  */
	// public function select($id=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function select_options($optionsList,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a select tag node JS requirement
	//  * @param mixed $id 
	//  * @param mixed|null $data 
	//  * @param mixed|null $option array of settings show_tag|debug|click|click|require
	//  * @return object 
	//  * @throws ReflectionException 
	//  * @throws IGKException 
	//  */
	// public function selecttag($id,$data=null,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-separator
	//  * @param mixed $type
	//  */
	// public function separator($type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function sharedwithcommunity($tab=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $menulist
	//  */
	// public function sidemenunavigation($menulist){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * mixed create a shortcut to single node viewer
	//  * @param mixed  node tag name or html item
	//  */
	// public function singlenodeviewer($node=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * shortcut to call node->addRow()->addCol()-> and return the column
	//  */
	// public function singlerowcol($col=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * single node view
	//  * @return IGKHtmlSingleNodeViewer 
	//  * @throws ReflectionException 
	//  * @throws IGKException 
	//  */
	// public function singleviewnode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-slabelcheckbox
	//  * @param mixed $id
	//  * @param mixed $value
	//  * @param mixed $attributes
	//  * @param mixed $require
	//  */
	// public function slabelcheckbox($id,$value=false,$attributes=null,$require=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-slabelinput
	//  * @param mixed $id
	//  * @param mixed $type
	//  * @param mixed $value
	//  * @param mixed $attributes
	//  * @param mixed $require
	//  * @param mixed $description
	//  */
	// public function slabelinput($id,$type='',$value=null,$attributes=null,$require=false,$description=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-slabelselect
	//  * @param mixed $id
	//  * @param mixed $values
	//  * @param mixed $valuekey
	//  * @param mixed $defaultCallback
	//  * @param mixed $required
	//  */
	// public function slabelselect($id,$values,$valuekey=false,$defaultCallback=null,$required=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-slabeltextarea
	//  * @param mixed $id
	//  * @param mixed $attributes
	//  * @param mixed $require
	//  * @param mixed $description
	//  */
	// public function slabeltextarea($id,$attributes=null,$require=false,$description=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-space node
	//  * @return HtmlSpaceNode 
	//  */
	// public function space(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function span_label($title,$text){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-spangroup
	//  */
	// public function spangroup(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * build a span link
	//  * @param mixed $expression 
	//  * @param mixed $link 
	//  * @param string $uri 
	//  * @return mixed 
	//  * @throws Exception 
	//  * @throws ReflectionException 
	//  */
	// public function spanlink($expression,$text,$uri=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-style
	//  */
	// public function style(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function submit($name=null,$value=null,$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-submitbtn
	//  * @param mixed $name
	//  * @param mixed $key
	//  */
	// public function submitbtn($name='',$key=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function svg_container(array $containerlist){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-svga
	//  * @param mixed $uri
	//  * @param mixed $svgname
	//  */
	// public function svga($uri,$svgname){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-svgajxformbtn
	//  * @param mixed $uri
	//  * @param mixed $svgname
	//  */
	// public function svgajxformbtn($uri,$svgname){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-svglnkbtn
	//  * @param mixed $uri
	//  * @param mixed $svgname
	//  */
	// public function svglnkbtn($uri,$svgname){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-svgsymbol
	//  * @param mixed $name
	//  */
	// public function svgsymbol($name=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-svguse
	//  * @param mixed $name
	//  */
	// public function svguse($name){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-symbol
	//  * @param mixed $code
	//  * @param mixed $w
	//  * @param mixed $h
	//  * @param mixed $name
	//  */
	// public function symbol($code,$w=16,$h=16,$name=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to add system article
	//  */
	// public function sysarticle($name){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-tabbutton
	//  */
	// public function tabbutton(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function table(?string $id=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tableheader($headers,$filter=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tablehost(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tbncommunityzone(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tbncontributorcommunauty($contributor,$ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tbnpresentationnode($ctrl){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function tbnunderconstructionpage($ctrl=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-td
	//  * @param mixed $for
	//  * @param mixed $key
	//  */
	// public function td($for=null,$key=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * use to add a template node
	//  */
	// public function template($ctrl,$name,$row=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create text node
	//  */
	// public function text($txt=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-textarea
	//  * @param mixed $name
	//  * @param mixed $content
	//  * @param mixed $attributes
	//  */
	// public function textarea($name=null,$content=null,$attributes=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * represent a zone node for text edition
	//  */
	// public function textedit($id,$uri,$c=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a thumbnail document
	//  */
	// public function thumbnaildocument($id){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  represent a tip panel
	//  */
	// public function tip(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-titlelevel
	//  * @param mixed $level
	//  */
	// public function titlelevel($level=1){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-titlenode
	//  * @param mixed $class
	//  * @param mixed $text
	//  */
	// public function titlenode($class,$text){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * for toast message
	//  */
	// public function toast(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function toast_notify($name){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function togglebutton(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function togglestatebutton($id,$value='',$checked=0,$type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-tooltip
	//  */
	// public function tooltip(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-topnavbar
	//  */
	// public function topnavbar(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-trackbarnode
	//  * @param mixed $id
	//  * @param mixed $value
	//  * @param mixed $min
	//  * @param mixed $max
	//  */
	// public function trackbarnode($id,$value,$min=0,$max=100){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  create a transition block node
	//  */
	// public function transitionblock(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function twitterfollowus($id,$showcount=0){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function twittertimeline($id,$theme=null,$color=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function uitrack($type=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-underconstructionpage
	//  */
	// public function underconstructionpage(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param Users $user 
	//  * @return HtmlNode<mixed, string> 
	//  */
	// public function userinfo($user){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function usesvg($name){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function videocontrols($model='',$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-videofilestream
	//  * @param mixed $location
	//  * @param mixed $auth
	//  */
	// public function videofilestream($location,$auth=false){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function view_code(string $file,int $startLine,int $endLine){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $callback callback to call 
	//  * @return mixed 
	//  * @throws Exception 
	//  */
	// public function viewcallback(callable $callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * used to evaluate the content. in xpthml file the content will be evaluated
	//  */
	// public function viewcontent($listener,$data=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  *  add a visibility server node
	//  * @param mixed cond mixed callback or evaluable condition expression
	//  */
	// public function visible($cond){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function voku_paginator($paginator,$path=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-vscrollbar
	//  * @param mixed $cibling
	//  * @param mixed $initTarget
	//  */
	// public function vscrollbar($cibling=null,$initTarget=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-vsep
	//  */
	// public function vsep(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a vue3 application node
	//  * @return void 
	//  */
	// public function vue_app($id){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a vue template node
	//  */
	// public function vue_template(string $id){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  */
	// public function vuejs($doc,$folder,$path='',$id='',$distFolder=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param mixed $id 
	//  * @param Array|OptionBuilder|null $data 
	//  * 
	//  * $data : array [
	//  *  components =>[] : list of component to import 
	//  * 
	//  * each component 
	//  * ]
	//  * @return App 
	//  * @throws IGKException 
	//  */
	// public function vuejs_app($id,$data=null,?array $attribs=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function vuejs_component($tagname,?array $args=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * SFC load single file component loading
	//  * @param IGKHtmlDoc $doc current document
	//  * @param string $folder distribution source folder
	//  * @param string $path where to store in public folder
	//  * @param mixed $app_name options JSExpression | Array | String
	//  */
	// public function vuejs_sfc($doc,$folder,$path='',$app_name=''){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param null|array|ConfigurationOptions $options 
	//  * @return HtmlItemBase<mixed, string> 
	//  * @throws IGKException 
	//  */
	// public function waitme(?array $options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function walk($tagname,$items,$callback){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function webarticle(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-webglgamesurface
	//  * @param mixed $listener
	//  */
	// public function webglgamesurface($listener=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create a node that will only be visible on webmaster mode context
	//  */
	// public function webmasternode(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create 
	//  * @param null|string $tagname 
	//  * @return HtmlWidgetNode 
	//  */
	// public function widget(?string $tagname=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-word
	//  * @param mixed $v
	//  * @param mixed $cl
	//  */
	// public function word($v,$cl){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-wordcasesplitter
	//  * @param mixed $v
	//  * @param mixed $split
	//  */
	// public function wordcasesplitter($v,$split=5){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-wordsplitview
	//  */
	// public function wordsplitview(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create xml node
	//  */
	// public function xmlnode($tag){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// public function xmlviewer(){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-xslt
	//  * @param mixed $xml
	//  * @param mixed $xslt
	//  * @param mixed $global
	//  * @param mixed $options
	//  */
	// public function xslt($xml,$xslt,$global=0,$options=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create winui-xsltranform
	//  * @param mixed $xmluri
	//  * @param mixed $xsluri
	//  * @param mixed $target
	//  */
	// public function xsltranform($xmluri,$xsluri,$target=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * 
	//  * @param string $hook 
	//  * @param mixed $args 
	//  * @return HtmlNoTagNode 
	//  */
	// public function yield(string $hook,...$args){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// /**
	//  * create youtube video tag
	//  * @param string $uri 
	//  * @param null|array $param 
	//  * @return HtmlItemBase<mixed, mixed> 
	//  * @throws IGKException 
	//  */
	// public function youtubevideo(string $uri,?array $param=null){
	// 	return self::AddFuncionListNode($this, [__FUNCTION__, func_get_args()]); 
	// }
	// private static function AddFuncionListNode($p, $n){
	//     if (!$p->getCanAddChilds()) 
	// 		return false;
	//     igk_html_push_node_parent($p);
	//     $r = call_user_func_array('igk_html_node_'.$n[0], $n[1]);
	// 	if ($r!== $p){
	// 		$p->add($r);
	// 	}
	//     igk_html_pop_node_parent();
	//     return $r;
	// }*/
}