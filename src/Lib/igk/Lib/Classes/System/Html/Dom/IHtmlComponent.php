<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20260222 15:59:55
namespace IGK\System\Html\Dom;

/**
* Html - core components
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
* @method self a($href= '#', $attributes= null, $index= null, $content= null) create winui-a
* @method self a_get($uri, $complete= '') function __desc__
* @method self a_post($uri, $complete= '') function __desc__
* @method self abbr($title= null) create winui-abbr
* @method self abtn($uri= '#', $type= 'default', $role= 'button') 
* @method self accordeon() 
* @method self accordeon_menus($items, $engine= null, $tag= 'ul', $item= 'li') 
* @method self aclearsandreload() create winui-aclearsandreload
* @method self actionbar($actions= null) create winui-actionbar
* @method self actiongroup() utility helper create an action group .
* @method self actions($actionlist) function __desc__
* @method self address() function __desc__
* @method self ajsbutton($code, $type= 'default') create winui-ajsbutton
* @method self ajspickfile($u, $options= null) 
* @method self ajxa($lnk= null, ?string $target= '', ?string $replacemode= 'content', ?string $method= 'GET') represent an ajx link. use to retrieve view from framework
* @method self ajxabutton($link) create winui-ajxabutton
* @method self ajxappendto($cibling) append async content
* @method self ajxdoctitle($title) change the document code in ajx context
* @method self ajxform($uri= null, $target= null) represent ajx form
* @method self ajxlnkreplace($target= '::') create winui-ajxlnkreplace
* @method self ajxpaginationview($baseuri, $total, $perpage, $selected= '1', $target= null) create winui-ajxpaginationview
* @method self ajxpickfile(string $uri, ?string $param= null) ajx div component used to load a file
* @method self ajxreplacecontent($uri, $method= 'GET') create winui-ajxreplacecontent
* @method self ajxreplacesource($selection) create winui-ajxreplacesource
* @method self ajxtabcomponent($host, $name) add tab component
* @method self ajxtabcontrol() function __desc__
* @method self ajxupdateview($cibling) create winui-ajxupdateview
* @method self ajxuriloader($uri, $append= '0') 
* @method self angularapp($directive, $script= null) 
* @method self apost($uri) add a link that will do a post request
* @method self app_hearder_bar(IGK\Controllers\BaseController $controller) application header bar
* @method self app_login_form(IGK\Controllers\BaseController $controller, string $entryfname) function __desc__
* @method self apploginform($app, $baduri= null, $goodUri= null) 
* @method self arraydata($tab) used to render data
* @method self arraylist($list, $tag= 'li', $callback= null) create winui-arraylist
* @method self article(?IGK\Controllers\BaseController $ctrl= null, ?string $name= null, $raw= [], $showAdminOption= '1') 
* @method self assertnode(bool $condition,  ...$args) 
* @method self attr_expression($p= null) 
* @method self author_community(?array $options= null) render autocommunity node - system community link
* @method self backgroundlayer($imgPath= null) create winui-backgroundlayer
* @method self badge($v) 
* @method self balafoncomponentjs() function __desc__
* @method self balafonjs($autoremove= '0') create winui-balafonjs
* @method self balafonlogo(?string $name= 'balafon_logo') node that contant balafon logo
* @method self bar() function __desc__
* @method self beforerendernextsibling(callable $callback) use to configure node before next cibling rendering
* @method self bgegamesurface($uriBase= '') create bge GameSurface
* @method self bind( ...$arg) binding node to
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
* @method self bootstrap_accordion() 
* @method self bootstrap_alert($class= null) create a bootstrap alert component
* @method self bootstrap_badge() 
* @method self bootstrap_breadcrumb() 
* @method self bootstrap_button() 
* @method self bootstrap_button_group() 
* @method self bootstrap_card() 
* @method self bootstrap_carousel() 
* @method self bootstrap_closebutton() 
* @method self bootstrap_collapse() 
* @method self bootstrap_dropdown() 
* @method self bootstrap_langselector() 
* @method self bootstrap_listgroup() 
* @method self bootstrap_masonry() helper: create a bootstrap mansory
* @method self bootstrap_modal() 
* @method self bootstrap_navbar() 
* @method self bootstrap_navstab() 
* @method self bootstrap_offcanvas() 
* @method self bootstrap_pagination() 
* @method self bootstrap_placeholder() 
* @method self bootstrap_popover(string $title, string $content, $tag= 'button') 
* @method self bootstrap_progress() 
* @method self bootstrap_scrollspy() 
* @method self bootstrap_spinner() 
* @method self bootstrap_toast() 
* @method self bootstrap_tooltip() 
* @method self boxdialog() function __desc__
* @method self breadcrumbs($menus, ?IGK\Controllers\BaseController $ctrl= null, $selected= null) 
* @method self btn($name, $value, $type= 'submit', $attributes= null) create winui-btn
* @method self buildselect($name, $rows, $idk, $callback= null, $selected= null) build select node
* @method self bullet() create winui-bullet
* @method self button(?string $id= null, $button_type_or_content= '0', ?string $type= null) create a button
* @method self button_group() add a form button group
* @method self bview(string $file, ?IGK\Controllers\BaseController $source= null, $args= null) import bview context
* @method self calcnode() 
* @method self calendar() 
* @method self canvabalafonscript($uri= null) create winui-canvabalafonscript
* @method self canvaeditorsurface() create a canva editor surface
* @method self cardid($src= null, $ctrl= null) create winui-cardid
* @method self carousel() function __desc__
* @method self cdata($value= null) create a cdata node
* @method self cell() create winui-cell
* @method self cellrow() create winui-cellrow
* @method self centerbox($content= null) create winui-centerbox
* @method self centerbox_flex() create a centerd box flex box node
* @method self centerbox_grid() create a centered grid box node
* @method self checkbox(string $id, $value= null) create a web checkbox
* @method self circlewaiter() create winui-circlewaiter
* @method self clearboth() 
* @method self clearfloatbox($t= 'b') create winui-clearfloatbox
* @method self cleartab() create winui-cleartab
* @method self clone(string $target) helper: class dom element at location
* @method self clonenode(IGK\System\Html\Dom\HtmlItemBase $node, $children= '') create winui-clonenode
* @method self code($type= 'php') create base php code
* @method self col($clname= null) create winui-col
* @method self colviewbox() column view item
* @method self combobox($id, $tab, $options= null) 
* @method self comment(?string $content= null) create comment node
* @method self commentzone() 
* @method self communitylink($name, $link) create winui-communitylink
* @method self communitylinks($tab, ?array $options= null) create winui-communitylinks
* @method self communitynode() 
* @method self component($listener, $typename, $regName, $unregister= '0') used to create component
* @method self conditionalnode($conditioncallback) create a node that will only be visible on conditional callback is evaluted to true
* @method self configsubmenu($menuList, $selected) 
* @method self connection_community(?string $appName= null, ?string $redirectUri= null, ?IGK\Controllers\BaseController $ctrl= null) 
* @method self contact_block($raw) bind contact block node helper
* @method self container() create winui-container
* @method self containerrowcol($style= '') function __desc__
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
* @method self dbtableview($tabResult, $theader= null, $header_prefix= 'header.') create table view node
* @method self defercsslink($href) defer css link loading
* @method self definition($title, $def) function __desc__
* @method self definitions($args) function __desc__
* @method self dialog_circle_waiter() function __desc__
* @method self dialogbox($title) create a dialog box
* @method self dialogboxcontent() create a dialog box content
* @method self dialogboxoptions() create dialogbox options node
* @method self divcontainer($attribs= null) create winui-divcontainer
* @method self dl() helper: create a Document list node
* @method self domainlink($src) create winui-domainlink
* @method self dotwaiter() 
* @method self dropdown_button($text= null, ?array $button_attribs= null, ?array $items= null, $itemCallable= null) 
* @method self dumpdata($data) function __desc__
* @method self enginecontrol($name, $type) engine control editor
* @method self error404($title, $m) create winui-error404
* @method self expo() create winui-expo
* @method self expression_node($raw, $ctrl= null) 
* @method self facebook_login_button(?array $options= null) 
* @method self facebookcomments($uri) 
* @method self facebookfollowusbutton($id, $layout= null, $theme= null) 
* @method self facebooklikebutton($showface= '') 
* @method self facebookoauthlink($data) 
* @method self facebooksharebutton() 
* @method self facebooktimeline($id) 
* @method self fields($fielddata, $datasource= null, ?object $engine= null, ?string $tag= null) load field list to parent
* @method self fixedactionbar($targetid= '', $offset= '1') a BJS's class control. used to show on scroll visibility.
* @method self flex($tag= 'div') 
* @method self floatingform() 
* @method self followusbutton($name, $uid) 
* @method self fontsymbol($name, $code) create a font symbol.
* @method self form(?string $uri= '.', ?string $method= 'POST', ?bool $notitle= '', ?bool $nofoot= '') function __desc__
* @method self form_post(string $uri) post urit using data form
* @method self formactionbutton($id, $value, $uri, $method= 'GET', $text= null) create winui-formactionbutton
* @method self formcref() function __desc__
* @method self formfields($formfields, $engine= null) 
* @method self formgroup() 
* @method self formusagecondition() create winui-formusagecondition
* @method self frame() create winui-frame
* @method self framedialog($id, $ctrl, $closeuri= '.', $reloadcallback= null) create winui-framedialog
* @method self gallery() 
* @method self galleryfolder($ctrl, $folder, $ignorethumb= '1') 
* @method self google_circle_waiter() 
* @method self google_follow_us_button($id, $height= '15', $rel= 'author', $annotation= 'none') add google follows us button
* @method self google_icon($name, $title= '', $type= 'span', $class= 'material-icons') bind google material icons
* @method self google_icon_outlined($name, $title= '', $type= 'span') 
* @method self google_js_maps($data= null, $apikey= null) add google maps javascript api node
* @method self google_line_waiter() 
* @method self google_login_button() creaate google login button
* @method self google_mapgeo($loc, $apikey= null) 
* @method self google_oauth_link($tab) 
* @method self google_oth2_button($url, $gclient) 
* @method self google_recaptcha(?string $siteKey= null) add google recaptcha
* @method self grid() create a grid node
* @method self grid_selection(?string $uri= '?', ?string $qname= 'style', ?bool $ajx= '', $ajxtarget= null) 
* @method self hamburger_button_menu() 
* @method self headerbar() function __desc__
* @method self hiddenfields(array $fields) function __desc__
* @method self hlineseparator() create winui-hlineseparator
* @method self hook($hook,  ...$args) call hook to render content on node
* @method self hooknode($hook, ?string $context= null) create hook node to update content on render
* @method self horizontalpageview() create  winui-horizontalpageview
* @method self horizontalpane() 
* @method self host(callable $callback,  ...$args) host callable to
* @method self hostobdata($callback, $host= null) Hosted object data. will pass the current node to callback as first argument
* @method self hscrollbar() create winui-hscrollbar
* @method self hsep() create winui-hsep
* @method self htmlnode($tag) create winui-htmlnode
* @method self huebar() used to render a pick a huebar value
* @method self ibview(string $source, ?IGK\Controllers\BaseController $ctrl= null, $args= null) import inline bview
* @method self if(string $condition) use to create if node
* @method self if_condition() helper to create igk:if-condition node
* @method self igkcopyright(?string $title= null) create winui-igkcopyright
* @method self igkgloballangselector() create winui-igkgloballangselector
* @method self igkglobalthemeselector() create winui-igkglobalthemeselector
* @method self igkheaderbar($title, $baseuri= null) create winui-headerbar
* @method self igksitemap() create winui-igksitemap
* @method self imagenode() create winui-imagenode
* @method self img($src= null) function __desc__
* @method self imglnk() create winui-imglnk
* @method self include($ctrl, $view, $params= null) 
* @method self include_js(string $file) include local file as javascript
* @method self inflate(string $file, $data= null) inflate view dans data
* @method self innerimg() create winui-innerimg
* @method self input($id= null, $type= 'text', $value= null, $attributes= null) create input node helper
* @method self jombotron($text= 'Jombotron') 
* @method self js_autofix_width() mark parent node with autofixing with.
* @method self jsaextern($method, $args= null) create winui-jsaextern
* @method self jsbindns(string $namespace, array $data, $coredef= 'igk') inject namespace with properties js namespace
* @method self jsbtn($script, $value= null) create winui-jsbtn
* @method self jsbtnshowdialog($id) create winui-jsbtnshowdialog
* @method self jsbutton($js) create winui-jsbutton
* @method self jsclone(string $target, ?string $complete= null) use to close node on client side
* @method self jsclonenode($node) create winui-jsclonenode
* @method self jsclonetarget(string $selector, ?string $tag= 'div') create igk-winui-clone-target class node
* @method self jslogger() create winui-jslogger
* @method self jspwa($options= null) builder iniline service worker
* @method self jsreadyscript($script) used to call ready invoke
* @method self jsreplaceuri(string $uri) create winui-jsreplaceuri
* @method self jsscript($file, $minify= '') used to load manually script tag
* @method self jsscript_options(string $name, $options) inject options. corejs must be loader in order to work
* @method self jsserviceworker($uri, $scope= null) 
* @method self jsview() function __desc__
* @method self jswaiter() function __desc__
* @method self jumbotron(?string $title= null, $desc= null) create a jumbotron element
* @method self label(?string $for= null, ?string $key= null) create winui-label
* @method self labelinput($id, $text, $type= 'text', $value= null, $attributes= null, $require= '', $description= null) create winui-labelinput
* @method self layout(IGK\Controllers\BaseController $controller, $activePage,  ...$params) 
* @method self layoutpresentation($type= '1-2') 
* @method self lborder() create winui-lborder
* @method self linewaiter() create winui-linewaiter
* @method self linkbtn($uri, $img, $width= '16', $height= '16') create winui-linkbtn
* @method self list($items, $callback= null, $ordered= '0') function __desc__
* @method self listitem(array $list) list items
* @method self livenodecallback($listener, $name, $callback) create winui-componentnodecallback
* @method self load_array(array $items, ?string $tag= 'div') function __desc__
* @method self loadarticle(IGK\Controllers\BaseController $controller, string $article_path, $raw= [], ?bool $show_admin_option= '1') 
* @method self localizabletext($expression, $data= null) 
* @method self login_form(?string $fname= null, ?IGK\Controllers\BaseController $controller= null) create a login form with service
* @method self logo(?IGK\Controllers\BaseController $ctrl= null) 
* @method self loop($array, ?callable $callback= null) helper: loop thru array . or template binding
* @method self loremipsum($mode= '1') represent the loremIpSum zone
* @method self mailpreview() helper: create an mail preview node component
* @method self mailto($href, $text= '') 
* @method self markdown(string $content, $options= null) 
* @method self markdown_document() dummry markdown document
* @method self md_markdown() dummry markdown document
* @method self memoryusageinfo() create winui-memoryusage-info tag
* @method self menukey($menus, $ctrl= null, $root= 'ul', $item= 'li', $callback= null) function __desc__
* @method self menulayer(?string $target= null) create a menu layer node
* @method self menulist($menuTab) function __desc__
* @method self menus($items, $callback= null, $subtag= 'ul', $item= 'li', ?object $option= null) build menus node item
* @method self moreview($hide= '1') create winui-moreview
* @method self msdialog($id= null) create winui-msdialog
* @method self mstitle($key) create winui-mstitle
* @method self navigationlink($target) create winui-navigationlink
* @method self nbsp() function __desc__
* @method self newsletterregistration($uri, $type= 'email', $ajx= '1') create winui-newsletterregistration
* @method self notagnode() create winui-notagnode
* @method self notagobdata($content) shortcut to create ObData node with noTag to display
* @method self notification($nodeType= 'div', $notifyName= null) used to add notification node
* @method self notifyhost($name= '::global', ?bool $autohide= null) used to bind notify global ctrl message
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
* @method self password2facomponent(string $name, ?string $value= null, $options= null) 
* @method self paypal_button(?array $option= null) create a paypal button
* @method self picker_zone($uri, $accepts= '', $complete= null) function __desc__
* @method self popupmenu() create winui-popupmenu
* @method self pre($data= null) create winui-pre tag
* @method self printbtn($uri= null) print button
* @method self progressbar() create winui-progressbar
* @method self pwa_install_button() build install button
* @method self pwa_install_script($options, ?string $id= null) 
* @method self pwa_script($options= null) 
* @method self radiobutton(?string $id= null) create radio button
* @method self readonlytextzone($file) create winui-readonlytextzone
* @method self registermailform() create winui-registermailform
* @method self renderingexpression($callback) renderging Expression
* @method self repeatcontent($number) create winui-repeatcontent
* @method self replace_uri($uri= null) 
* @method self resimg($name, $desc= '', $width= '16', $height= '16') function __desc__
* @method self responsenode() create winui-responsenode
* @method self rollin() create winui-rollin
* @method self roundbullet() create winui-roundbullet
* @method self row() create winui-row
* @method self rowcolumn($classLevel= null) add a row column
* @method self rowcontainer() create winui-rowcontainer
* @method self script($script= null, $version= null) function __desc__
* @method self script_var(string $name, $data, $type= 'const') create node script variable
* @method self scrollimg($src) create winui-scrollimg
* @method self scrollloader($src) used to load scroll Loader Item
* @method self search_box($value= null, $name= 'search_box', $param= null) create a search box
* @method self searchbox(string $uri, $id= 'search') function __desc__
* @method self searchbutton(string $uri, ?string $id= 'search') search button view
* @method self searchfield($id= 'search') add search field
* @method self sectiontitle($level= null) create winui-sectiontitle
* @method self select($id= null) help create a select node
* @method self select_options($optionsList, $options= null) function __desc__
* @method self selecttag($id, $data= null, $options= null) create a select tag node JS requirement
* @method self separator($type= 'horizontal') create winui-separator
* @method self sfsymbol(string $name, ?string $title= null) 
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
* @method self spacer($_content, ?string $extra_class_definition= null) create a spacer container with content
* @method self span_label($title, $text) function __desc__
* @method self spangroup() create winui-spangroup
* @method self spanlink($expression, $text, $uri= '?') build a span link
* @method self style() create winui-style
* @method self submit($name= null, $value= null, $type= 'submit') function __desc__
* @method self submitbtn($name= 'btn_', $key= 'btn.add') create winui-submitbtn
* @method self svg_container(array $containerlist) function __desc__
* @method self svga($uri, $svgname) create winui-svga
* @method self svgajxformbtn($uri, $svgname) create winui-svgajxformbtn
* @method self svglnkbtn($uri, $svgname) create winui-svglnkbtn
* @method self svgsymbol($name= null) create winui-svgsymbol
* @method self svguse($name) create winui-svguse
* @method self swagger_app($options) 
* @method self swagger_script() represent the swagger script content
* @method self symbol($code, $w= '16', $h= '16', $name= 'default') create winui-symbol
* @method self sysarticle($name) used to add system article
* @method self tabbutton() create winui-tabbutton
* @method self table(?string $id= null) function __desc__
* @method self tableheader($headers, $filter= null) function __desc__
* @method self tablehost() function __desc__
* @method self tbncommunityzone() 
* @method self tbncontributorcommunauty($contributor, $ctrl= null) 
* @method self tbnpresentationnode($ctrl) 
* @method self tbnunderconstructionpage(?IGK\Controllers\BaseController $ctrl= null) 
* @method self td($for= null, $key= null) create winui-td
* @method self template($ctrl, $name, $row= null) use to add a template node
* @method self text($txt= null) create text node
* @method self textarea($name= null, $content= null, $attributes= null) create winui-textarea
* @method self textedit($id, $uri, $c= null) represent a zone node for text edition
* @method self thumbnaildocument($id) create a thumbnail document
* @method self time(?string $datetime= null) helper: create a time tag node
* @method self tip() represent a tip panel
* @method self titlelevel($level= '1') create winui-titlelevel
* @method self titlenode($class, $text) create winui-titlenode
* @method self toast() for toast messageattribute :
* @method self toast_notify($name) function __desc__
* @method self togglebutton() function __desc__
* @method self togglestatebutton($id, $value= 'on', $checked= '0', $type= 'window10') 
* @method self togglethemebutton(?string $tag= null) create a toggle button theme
* @method self tooltip() create winui-tooltip
* @method self topnavbar() create winui-topnavbar
* @method self trackbar($id, $value, $min= '0', $max= '100') create winui-trackbar
* @method self transitionblock() create a transition block node
* @method self twitterfollowus($id, $showcount= '0') 
* @method self twittertimeline($id, $theme= null, $color= null) 
* @method self uikit_menubar( ...$menu) 
* @method self uikit_ripple_button() 
* @method self uitrack($type= 'default') 
* @method self underconstructionpage() create winui-underconstructionpage
* @method self userinfo($user) 
* @method self usesvg(string $name) shortcut to load svg document
* @method self videocontrols($model= 'default', $options= null) 
* @method self videofilestream($location, $auth= '') create winui-videofilestream
* @method self view_code(string $file, int $startLine, int $endLine) function __desc__
* @method self view_include(string $path, ?IGK\Controllers\BaseController $ctrl= null) function __desc__
* @method self viewcallback(callable $callback) 
* @method self viewcontent($listener, $data= null) used to evaluate the content. in xpthml file the content will be evaluated
* @method self visible($cond) add a visibility server node
* @method self vite_app(string $id, $options) 
* @method self vite_tag(?string $tag= 'div') create a vite tag node
* @method self voku_paginator($paginator, $path= '?') 
* @method self vscrollbar($cibling= null, $initTarget= null) create winui-vscrollbar
* @method self vsep() create winui-vsep
* @method self vue_app(?string $id= 'app', $data= null) create a vue3 application node
* @method self vue_clone($to= null) helper to clone the vue
* @method self vue_component(?string $tagname= 'div') create a vue component
* @method self vue_item($tag= 'div') helper to clone the vue
* @method self vue_menus(?array $menus= null, ?string $default_class= igk\js\Vue3\VueConstants::DEFAULT_MENU_CLASS) helper to clone the vue
* @method self vue_notag() create vue no tag to init context
* @method self vue_router_link($to= null) 
* @method self vue_scripttemplate(?string $id= null) create a vue template node
* @method self vue_sfc_app(IGK\Controllers\BaseController $ctrl, string $id, string $sfc_file) bind sfc core application
* @method self vue_xtemplate(string $id) 
* @method self walk($tagname, $items, $callback) function __desc__
* @method self webarticle() function __desc__
* @method self webgl_surface($option= null) init webgl surface
* @method self webglgamesurface($listener= null) create winui-webglgamesurface
* @method self webglscriptsurface($scriptFile, $shaderFolder= null) function igk_html_node_webglscriptsurface
* @method self webglsurface($options= null) init webgl surface node
* @method self webmasternode() create a node that will only be visible on webmaster mode context
* @method self widget(?string $tagname= null) create
* @method self winui_dialog($title= null) create a dialog host that will not being displayed<
* @method self word($v, $cl) create winui-word
* @method self wordcasesplitter($v, $split= '5') create winui-wordcasesplitter
* @method self wordsplitview() create winui-wordsplitview
* @method self x_template() 
* @method self xmlnode($tag) create xml node
* @method self xmlviewer() function __desc__
* @method self xslt($xml, $xslt, $global= '0', $options= null) create winui-xslt
* @method self xsltranform($xmluri, $xsluri, $target= null) create winui-xsltranform
* @method self yield(string $hook,  ...$args) 
* @method self youtubevideo(string $uri, ?array $param= null) create youtube video tag
* */
interface IWebHtmlComponent{
}