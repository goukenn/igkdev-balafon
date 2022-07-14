<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20220601 15:03:27

///<summary>Html - core components</summary>
/**
* Html - core components
* @method self a($href= '#', $attributes= null, $index= null) create winui-a
* @method self a_get($uri, $complete= '') 
* @method self a_post($uri, $complete= '') 
* @method self abbr($title= null) create winui-abbr
* @method self abtn($uri= '#', $type= 'default', $role= 'button') 
* @method self accordeon() 
* @method self accordeon_menus($items, $engine= null, $tag= 'ul', $item= 'li') 
* @method self aclearsandreload() create winui-aclearsandreload
* @method self actionbar($actions= null) create winui-actionbar
* @method self actiongroup() 
* @method self actions($actionlist) 
* @method self address() 
* @method self ajsbutton($code, $type= 'default') create winui-ajsbutton
* @method self ajspickfile($u, $options= null) 
* @method self ajxa($lnk= null, $target= '', $replacemode= 'content', $method= 'GET') represent an ajx link
* @method self ajxabutton($link) create winui-ajxabutton
* @method self ajxappendto($cibling) append async content
* @method self ajxdoctitle($title) change the document code in ajx context
* @method self ajxform($uri= null, $target= null) represent ajx form
* @method self ajxlnkreplace($target= '::') create winui-ajxlnkreplace
* @method self ajxpaginationview($baseuri, $total, $perpage, $selected= '1', $target= null) create winui-ajxpaginationview
* @method self ajxpickfile($uri, $param= null) ajx div component used to load a file
* @method self ajxreplacecontent($uri, $method= 'GET') create winui-ajxreplacecontent
* @method self ajxreplacesource($selection) create winui-ajxreplacesource
* @method self ajxtabcomponent($host, $name) add tab component
* @method self ajxtabcontrol() 
* @method self ajxupdateview($cibling) create winui-ajxupdateview
* @method self ajxuriloader($uri, $append= '0') 
* @method self angularapp($directive, $script= null) 
* @method self apploginform($app, $baduri= null, $goodUri= null) 
* @method self arraydata($tab) used to render data
* @method self arraylist($list, $tag= 'li', $callback= null) create winui-arraylist
* @method self article($ctrl, $name, $raw= [], $showAdminOption= '1') bind article
* @method self assertnode(bool $condition,  ...$args) 
* @method self attr_expression($p= null) 
* @method self author_community() render autho community node
* @method self backgroundlayer($imgPath= null) create winui-backgroundlayer
* @method self badge($v) 
* @method self balafoncomponentjs() 
* @method self balafonjs($autoremove= '0') create winui-balafonjs
* @method self balafonvideo($ctrl, $index, $attr= null, $list= '0') 
* @method self bar() 
* @method self beforerendernextsibling(callable $callback) use to configure node before next cibling rendering
* @method self bindarticle($ctrl, $name, $data= null, $showAdminOption= '1') create winui-bindarticle
* @method self bindcontent($content, $entries, $ctrl= null) create winui-bindcontent
* @method self bindmenu($target) target the menuu display
* @method self bindscript($data, $uri, $name, ?bool $production= null) 
* @method self blocknode() create winui-blocknode
* @method self bmcbutton() 
* @method self bmccheckbox($id, $value= null, $array= '0') 
* @method self bmccombobox($id, $data= null, $index= null) 
* @method self bmclineripple() 
* @method self bmcloginpage($listener) 
* @method self bmcradio($id, $value= null) 
* @method self bmcripple() 
* @method self bmcroundtool() 
* @method self bmcshape($type= null) 
* @method self bmcsurface() 
* @method self bmctextarea($id, $value= null) 
* @method self bmctextfield($id, $type= 'text') 
* @method self bmctextlogofield($id, $logo) 
* @method self bmctextsearchfield($id, $value= null, $uri= null, $target= null) 
* @method self bmctooltip() 
* @method self bodybox() create winui-bodybox
* @method self bootstrap_langselector() 
* @method self boxdialog() 
* @method self btn($name, $value, $type= 'submit', $attributes= null) create winui-btn
* @method self buildselect($name, $rows, $idk, $callback= null, $selected= null) build select node
* @method self bullet() create winui-bullet
* @method self button($id= null, $buttontype= '0', $type= null) create a button
* @method self calcnode() 
* @method self calendar() 
* @method self canvabalafonscript($uri= null) create winui-canvabalafonscript
* @method self canvaeditorsurface() create a canva editor surface
* @method self cardid($src= null, $ctrl= null) create winui-cardid
* @method self carousel() 
* @method self cell() create winui-cell
* @method self cellrow() create winui-cellrow
* @method self centerbox($content= null) create winui-centerbox
* @method self checkbox($id, $value= null) 
* @method self circlewaiter() create winui-circlewaiter
* @method self clearboth() 
* @method self clearfloatbox($t= 'b') create winui-clearfloatbox
* @method self cleartab() create winui-cleartab
* @method self clonenode(IGK\System\Html\Dom\HtmlItemBase $node) create winui-clonenode
* @method self code($type= 'php') create base php code
* @method self col($clname= null) create winui-col
* @method self colviewbox() column view item
* @method self combobox($id, $tab, $options= null) 
* @method self comment() 
* @method self commentzone() 
* @method self communitylink($name, $link) create winui-communitylink
* @method self communitylinks($tab) create winui-communitylinks
* @method self communitynode() 
* @method self component($listener, $typename, $regName, $unregister= '0') used to create component
* @method self conditionalnode($conditioncallback) create a node that will only be visible on conditional callback is evaluted to true
* @method self configsubmenu($menuList, $selected) 
* @method self contact_block($raw) bind contact block node helper
* @method self container() create winui-container
* @method self containerrowcol($style= '') 
* @method self cookiewarning($warnurl= null) create winui-cookiewarning
* @method self copyright($ctrl= null) create winui-copyright
* @method self csscomponentstyle($file, $host= null) create winui-csscomponentstyle
* @method self csslink($href, $temp= '0', $defer= '0') add css link
* @method self cssstyle($id, $minfile= '1') represent a css style element
* @method self ctrlview($view, $ctrl, $params= null) create winui-ctrlview
* @method self dataschema() create data schema
* @method self dbdataschema() create a data base schema node
* @method self dbentriescallback($target, $callback, $queryResult, $fallback= null) create winui-dbentriescallback
* @method self dbresult($r, $uri, $selected, $max= '-1', $target= null) create winui-dbresult
* @method self dbselect($name, $result, $callback= null, $valuekey= IGK_FD_ID) DataBase select component
* @method self defercsslink($href, $temp= '0') 
* @method self definition($title, $def) 
* @method self definitions($args) 
* @method self dialog($title= null) create a dialog host that will not being displayed<
* @method self dialog_circle_waiter() 
* @method self dialogbox($title) create a dialog box
* @method self dialogboxcontent() create a dialog box content
* @method self dialogboxoptions() create dialogbox options node
* @method self divcontainer($attribs= null) create winui-divcontainer
* @method self dl() 
* @method self domainlink($src) create winui-domainlink
* @method self dropdown_button($text= null, ?array $button_attribs= null, ?array $items= null, $itemCallable= null) 
* @method self dumpdata($data) 
* @method self enginecontrol($name, $type) engine control editor
* @method self error404($title, $m) create winui-error404
* @method self expo() create winui-expo
* @method self expression_node($raw, $ctrl= null) 
* @method self extends($parentview) 
* @method self facebook_login_button(?array $options= null) 
* @method self facebookcomments($uri) 
* @method self facebookfollowusbutton($id, $layout= null, $theme= null) 
* @method self facebooklikebutton($showface= '') 
* @method self facebooksharebutton() 
* @method self facebooktimeline($id) 
* @method self fields() load field list to parent
* @method self fixedactionbar($targetid= '', $offset= '1') a BJS's class control. used to show on scroll visibility.
* @method self floatingform() 
* @method self followusbutton($name, $uid) 
* @method self fontsymbol($name, $code) create a font symbol.
* @method self form($uri= '.', $method= 'POST', $notitle= '', $nofoot= '') 
* @method self form_post(string $uri) post urit using data form
* @method self formactionbutton($id, $value, $uri, $method= 'GET', $text= null) create winui-formactionbutton
* @method self formcref() 
* @method self formfields($formfields, $engine= null) 
* @method self formgroup() 
* @method self formusagecondition() create winui-formusagecondition
* @method self frame() create winui-frame
* @method self framedialog($id, $ctrl, $closeuri= '.', $reloadcallback= null) create winui-framedialog
* @method self gallery() 
* @method self galleryfolder($ctrl, $folder, $ignorethumb= '1') 
* @method self google_login_button() 
* @method self grid() create a grid node
* @method self grid_selection($uri= '?', $qname= 'style', $ajx= '', $ajxtarget= null) 
* @method self headerbar() 
* @method self hiddenfields(array $fields) 
* @method self hlineseparator() create winui-hlineseparator
* @method self hook($hook,  ...$args) call hook to render content on node
* @method self hooknode($hook, ?string context=null) 
* @method self horizontalpageview() create  winui-horizontalpageview
* @method self host(callable $callback,  ...$args) host callable to
* @method self hostobdata($callback, $host= null) Hosted object data. will pass the current node to callback as first argument
* @method self hscrollbar() create winui-hscrollbar
* @method self hsep() create winui-hsep
* @method self htmlnode($tag) create winui-htmlnode
* @method self huebar() used to render a pick a huebar value
* @method self igkcopyright() create winui-igkcopyright
* @method self igkgloballangselector() create winui-igkgloballangselector
* @method self igkglobalthemeselector() create winui-igkglobalthemeselector
* @method self igkheaderbar($title, $baseuri= null) create winui-headerbar
* @method self igksitemap() create winui-igksitemap
* @method self imagenode() create winui-imagenode
* @method self img($src= null) 
* @method self imglnk() create winui-imglnk
* @method self include($ctrl, $view, $params= null) 
* @method self include_js($file) include local file as javascript
* @method self innerimg() create winui-innerimg
* @method self input($id= null, $type= 'text', $value= null, $attributes= null) 
* @method self jombotron($text= 'Jombotron') 
* @method self js_autofix_width() mark parent node with autofixing with.
* @method self jsaextern($method, $args= null) create winui-jsaextern
* @method self jsbindns(string $namespace, array $data, $coredef= 'igk') inject namespace with properties js namespace
* @method self jsbtn($script, $value= null) create winui-jsbtn
* @method self jsbtnshowdialog($id) create winui-jsbtnshowdialog
* @method self jsbutton($js) create winui-jsbutton
* @method self jsclone(string $target, ?string $complete= null) use to close node on client side
* @method self jsclonenode($node) create winui-jsclonenode
* @method self jsclonetarget($selector, $tag= 'div') create winui-jsclonetarget
* @method self jslogger() create winui-jslogger
* @method self jsreadyscript($script) used to call ready invoke
* @method self jsreplaceuri($uri) create winui-jsreplaceuri
* @method self jsscript($file, $minify= '') used to load manually script tag
* @method self jsview() 
* @method self label($for= null, $key= null) create winui-label
* @method self labelinput($id, $text, $type= 'text', $value= null, $attributes= null, $require= '', $description= null) create winui-labelinput
* @method self layout(IGK\Controllers\BaseController $controller, $activePage,  ...$params) 
* @method self layoutpresentation($type= '1-2') 
* @method self lborder() create winui-lborder
* @method self linewaiter() create winui-linewaiter
* @method self linkbtn($uri, $img, $width= '16', $height= '16') create winui-linkbtn
* @method self list($items, $callback= null, $ordered= '0') 
* @method self livenodecallback($listener, $name, $callback) create winui-componentnodecallback
* @method self load_array(array $items, ?string $tag= 'div') 
* @method self localizabletext($expression, $data= null) 
* @method self loop(iterable $array, ?callable $callback= null) helper: loop thru array<
* @method self loremipsum($mode= '1') represent the loremIpSum zone
* @method self mailto($href, $text= '') 
* @method self markdown_document() dummry markdown document
* @method self memoryusageinfo() create winui-memoryusage-info tag
* @method self menu($tab, $selected= null, $uriListener= null, $callback= null, ?IGK\Models\Users $user= null, $tag= 'ul', $item= 'li') 
* @method self menukey($menus, $ctrl= null, $root= 'ul', $item= 'li', $callback= null) 
* @method self menulayer(?string $target= null) create a menu layer node
* @method self menulist($menuTab) 
* @method self menus($items, $callback= null, $tag= 'ul', $item= 'li') 
* @method self moreview($hide= '1') create winui-moreview
* @method self msdialog($id= null) create winui-msdialog
* @method self mstitle($key) create winui-mstitle
* @method self navigationlink($target) create winui-navigationlink
* @method self nbsp() 
* @method self newsletterregistration($uri, $type= 'email', $ajx= '1') create winui-newsletterregistration
* @method self notagnode() create winui-notagnode
* @method self notagobdata($content) shortcut to create ObData node with noTag to display
* @method self notification($nodeType= 'div', $notifyName= null) used to add notification node
* @method self notifyhost($name= '::global', $autohide= '1') used to bind notify global ctrl message
* @method self notifyhostbind($name= null, $autohide= '1') create winui-notifyhostbind
* @method self notifyzone($name= null, $autohide= '1', $tag= 'div') create winui-notifyzone
* @method self obdata($data, $nodeType= 'div') used to add a node with buffer content
* @method self obscript(callable $callback) bind object scripting for callable
* @method self onrendercallback($callbackObj) create node on callback. create a callback object to send to this
* @method self page() create winui-page
* @method self pagecenterbox(?callable $host= null) 
* @method self paginationview($baseuri, $total, $perpage, $selected= '1', $ajx= '0', $cookiepath= null, $target= '::') build pagination settings
* @method self panelbox() create winui-panelbox
* @method self paneldialog($title, $content= null, $settings= null) create winui-paneldialog
* @method self parallaxnode($uri= null) parallax node view
* @method self paypal_button(?array $option= null) create a paypal button
* @method self picker_zone($uri, $accepts= '', $complete= null) 
* @method self popupmenu() create winui-popupmenu
* @method self pre($data= null) create winui-pre tag
* @method self printbtn($uri= null) print button
* @method self progressbar() create winui-progressbar
* @method self radiobutton(?string $id= null) create radio button
* @method self reactapp($name, $options= null) 
* @method self readonlytextzone($file) create winui-readonlytextzone
* @method self registermailform() create winui-registermailform
* @method self renderingexpression($callback) renderging Expression
* @method self repeatcontent($number) create winui-repeatcontent
* @method self replace_uri($uri= null) 
* @method self resimg($name, $desc= '', $width= '16', $height= '16') 
* @method self responsenode() create winui-responsenode
* @method self rollin() create winui-rollin
* @method self roundbullet() create winui-roundbullet
* @method self row() create winui-row
* @method self rowcolumn($classLevel= null) add a row column
* @method self rowcontainer() create winui-rowcontainer
* @method self script($script= null, $version= null) 
* @method self script_var($name, $data, $type= 'const') 
* @method self scrollimg($src) create winui-scrollimg
* @method self scrollloader($src) used to load scroll Loader Item
* @method self search_box($value= null, $name= 'search_box', $param= null) 
* @method self searchbox(string $uri, $id= 'search') 
* @method self searchbutton(string $uri, ?string $id= 'search') search button view
* @method self sectiontitle($level= null) create winui-sectiontitle
* @method self select($id= null) help create a select node
* @method self select_options($optionsList, $options= null) 
* @method self selecttag($id, $data= null, $options= null) create a select tag node JS requirement
* @method self separator($type= 'horizontal') create winui-separator
* @method self sharedwithcommunity($tab= null) 
* @method self sidemenunavigation($menulist) 
* @method self singlenodeviewer($node= null) mixed create a shortcut to single node viewer
* @method self singlerowcol($col= null) shortcut to call node->addRow()->addCol()-> and return the column
* @method self singleviewnode() single node view
* @method self slabelcheckbox($id, $value= '', $attributes= null, $require= '') create winui-slabelcheckbox
* @method self slabelinput($id, $type= 'text', $value= null, $attributes= null, $require= '', $description= null) create winui-slabelinput
* @method self slabelselect($id, $values, $valuekey= '', $defaultCallback= null, $required= '') create winui-slabelselect
* @method self slabeltextarea($id, $attributes= null, $require= '', $description= null) create winui-slabeltextarea
* @method self space() create winui-space node
* @method self span_label($title, $text) 
* @method self spangroup() create winui-spangroup
* @method self spanlink($expression, $text, $uri= '?') build a span link
* @method self style() create winui-style
* @method self submit($name= null, $value= null, $type= 'submit') 
* @method self submitbtn($name= 'btn_', $key= 'btn.add') create winui-submitbtn
* @method self svg_container(array $containerlist) 
* @method self svga($uri, $svgname) create winui-svga
* @method self svgajxformbtn($uri, $svgname) create winui-svgajxformbtn
* @method self svglnkbtn($uri, $svgname) create winui-svglnkbtn
* @method self svgsymbol($name= null) create winui-svgsymbol
* @method self svguse($name) create winui-svguse
* @method self symbol($code, $w= '16', $h= '16', $name= 'default') create winui-symbol
* @method self sysarticle($name) used to add system article
* @method self tabbutton() create winui-tabbutton
* @method self table(?string $id= null) 
* @method self tableheader($headers, $filter= null) 
* @method self tablehost() 
* @method self tbncommunityzone() 
* @method self tbncontributorcommunauty($contributor, $ctrl= null) 
* @method self tbnpresentationnode($ctrl) 
* @method self tbnunderconstructionpage($ctrl= null) 
* @method self td($for= null, $key= null) create winui-td
* @method self template($ctrl, $name, $row= null) use to add a template node
* @method self text($txt= null) create text node
* @method self textarea($name= null, $content= null, $attributes= null) create winui-textarea
* @method self textedit($id, $uri, $c= null) represent a zone node for text edition
* @method self thumbnaildocument($id) create a thumbnail document
* @method self tip() represent a tip panel
* @method self titlelevel($level= '1') create winui-titlelevel
* @method self titlenode($class, $text) create winui-titlenode
* @method self toast() for toast message
* @method self toast_notify($name) 
* @method self togglebutton() 
* @method self togglestatebutton($id, $value= 'on', $checked= '0', $type= 'window10') 
* @method self tooltip() create winui-tooltip
* @method self topnavbar() create winui-topnavbar
* @method self trackbarnode($id, $value, $min= '0', $max= '100') create winui-trackbarnode
* @method self transitionblock() create a transition block node
* @method self twitterfollowus($id, $showcount= '0') 
* @method self twittertimeline($id, $theme= null, $color= null) 
* @method self uitrack($type= 'default') 
* @method self underconstructionpage() create winui-underconstructionpage
* @method self userinfo($user) 
* @method self usesvg($name) 
* @method self videocontrols($model= 'default', $options= null) 
* @method self videofilestream($location, $auth= '') create winui-videofilestream
* @method self view_code(string $file, int $startLine, int $endLine) 
* @method self viewcallback(callable $callback) 
* @method self viewcontent($listener, $data= null) used to evaluate the content. in xpthml file the content will be evaluated
* @method self visible($cond) add a visibility server node
* @method self voku_paginator($paginator, $path= '?') 
* @method self vscrollbar($cibling= null, $initTarget= null) create winui-vscrollbar
* @method self vsep() create winui-vsep
* @method self vuejs($doc, $folder, $path= '/', $id= 'app', $distFolder= null) 
* @method self vuejs_app($id, $data= null, ?array $attribs= null) $data : array [components =>[] : list of component to importeach component]
* @method self vuejs_component($tagname, ?array $args= null) 
* @method self vuejs_sfc($doc, $folder, $path= '/', $app_name= 'app') SFC load single file component loading
* @method self waitme(?array $options= null) 
* @method self walk($tagname, $items, $callback) 
* @method self webglgamesurface($listener= null) create winui-webglgamesurface
* @method self webmasternode() create a node that will only be visible on webmaster mode context
* @method self widget(?string $tagname= null) create
* @method self word($v, $cl) create winui-word
* @method self wordcasesplitter($v, $split= '5') create winui-wordcasesplitter
* @method self wordsplitview() create winui-wordsplitview
* @method self xmlnode($tag) create xml node
* @method self xmlviewer() 
* @method self xslt($xml, $xslt, $global= '0', $options= null) create winui-xslt
* @method self xsltranform($xmluri, $xsluri, $target= null) create winui-xsltranform
* @method self yield(string $hook,  ...$args) 
* @method self youtubevideo(string $uri, ?array $param= null) create youtube video tag
* */
interface IHtmlComponent{
	// extract.
}