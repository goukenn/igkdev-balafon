<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbConfigController.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Database\MySQL\Controllers;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Models\Apps;
use IGK\Models\DbLogs;
use IGK\Models\DbModelDefinitionInfo;
use IGK\Resources\R;
use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
use IGK\System\Database\DbUtils;
use IGK\System\Database\IDatabaseHost;
use IGK\System\Html\Dom\HtmlComponents;
use IGK\System\Html\Dom\HtmlSearchNode;
use IGK\System\Html\HtmlUtils;
use IGK\System\Http\NotAllowedRequestException;
use IGK\System\Http\RequestHandler;
use IGK\System\Number;
use IGKCSVDataAdapter;
use IGKEvents;
use IGKException;

use function igk_resources_gets as __;

///<summary> USE TO CONFIGURE MYSQL DATABASE ACCESS</summary>
/**
 *  USE TO CONFIGURE MYSQL DATABASE ACCESS
 */
final class DbConfigController extends ConfigControllerBase implements IDatabaseHost
{
    const LOADTABLES_DB = 0xa4;
    const SEARCH_DB = 0xa4;
    const SELECTED_DB = 0xa1;
    const TABINFO_DB = 0xa3;
    const VIEWMYADMIN_DB = 0xa2;
    static $sm_tabinfo;

    
      /**
     * indicate if use data schema
     * @return bool 
     */
    function getUseDataSchema():bool{
        return igk_getctrl(SysDbController::class)->getUseDataSchema();
    }
    /**
     * indicate data adpater name to use
     * @return null|string 
     */
    function getDataAdapterName(): string{
        return igk_getctrl(SysDbController::class)->getDataAdapterName();
    }
    /**
     * return data definition. \
     * if getUseDataSchema() return false
     * @return null|DbModelDefinitionInfo 
     */
    function getDataTableInfo(): ?DbModelDefinitionInfo{
        return igk_getctrl(SysDbController::class)->getDataTableInfo();
    }


    ///<summary></summary>
    ///<param name="tr"></param>
    ///<param name="tablename"></param>
    ///<param name="selectedDb" default="null"></param>
    /**
     * 
     * @param mixed $tr
     * @param mixed $tablename
     * @param mixed $selectedDb the default value is null
     */
    private function __addEditTable($tr, $tablename, $selectedDb = null)
    {
        $tr->addTd()->li()->add("a", array("href" => igk_js_post_frame($this->getUri("db_viewtableentries_ajx&n=" . $tablename . ($selectedDb ? "&from=" . $this->selectedDb : IGK_STR_EMPTY)))))->add("img", array(
            "width" => "16px",
            "height" => "16px",
            "src" => R::GetImgUri("edit_16x16"),
            "alt" => __("tip.editdatabase")
        ));
    }
    ///<summary>send query request</summary>
    /**
     * send query request
     */
    public function __db_query_r_ajx()
    {
        $g = null;
        $mysql = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, true);
        $q = trim(igk_getr("clQuery"));
        if (empty($q) || !igk_is_conf_connected())
            igk_exit();
        if (!preg_match("#^(SELECT|UPDATE|DELETE|SHOW|ALTER|INSERT|CREATE|DROP) (.)+#i", $q)) {
            igk_wl("/!\\" . __("Query not allowed : {0}", $q));
            igk_exit();
        }
        if (preg_match("/^SELECT /i", $q)) {
            $g = $mysql->sendQuery("SELECT COUNT(*) as count FROM (" . $mysql->escape_string($q) . ") as dummy");
            if ($g) {
                $r = $g->getRowAtIndex(0);
                if ($r && ($r->count > 50)) {
                    $q .= " Limit 1, 50";
                }
            }
        }
        if ($q) {
            $this->setParam("query", $q);
            $r = $mysql->sendQuery($q, true);

            if ($r && ($r->getRowCount() > 0)) {
                $uri = igk_register_temp_uri(__CLASS__) . "/page/";
                $selected = 1;
                $dv = igk_create_node("div");
                $dv->div()->Content = $q;
                $dv->div()
                    ->setStyle("min-height:80px; line-height:1")
                    ->tablehost()->setClass("posab fit overflow-y-a")
                    ->addDbResult($g, $uri, $selected, igk_configs()->db_query_page_result ?? 50, "#query-s-r");
                $dv->renderAJX();
            } else {
                if ($error = $mysql->getError()) {
                    igk_wl("SQLError: ", $error);
                } else {
                    igk_wl("no data found");
                }
            }
        }
    }
    public function page($view = 0)
    {
        // igk_ajx_replace_uri("#!/page/".$view);
        $q = $this->getParam("query");
        if ($q && ($mysql = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, true)) && $mysql->connect()) {

            $g = $mysql->sendQuery($q);
            if ($g && ($g->RowCount > 0)) {
                $uri = igk_register_temp_uri(__CLASS__) . "/page/";
                $selected = $view;
                $dv = igk_create_node("div");
                $dv->div()->Content = $q;
                $dv->div()->setClass("igk-table-host overflow-x-a fitw bdr-1")
                    ->addDbResult($g, $uri, $selected, 5);
                $dv->renderAJX();
            }
            $mysql->close();
        }
    }
    ///<summary></summary>
    ///<param name="o"></param>
    ///<param name="e"></param>
    /**
     * 
     * @param mixed $o
     * @param mixed $e
     */
    private function __inittable_callback($o, $e)
    {
        $tbname = $e[0];
        $desc = $e[1];
        $this->_regTableDefinition($o, $tbname, $desc);
    }
    ///backup mysql database
    ///<summary>restore data base table from definition</summary>
    /**
     * restore data base table from definition
     */
    private function __restoredb($table, $header, $definition, $sync = 0)
    {
        throw new \IGKException("method not implement : " . __METHOD__);
    }
    ///get data from schemas
    /**
     */
    private function _addTable($tb, $ctrl)
    {
        if (empty($tb))
            return false;
        $v_table = &$this->getLoadTables();
        if ($v_table === null) {
            igk_die("loaded table is empty");
        }
        if (isset($v_table[$tb]) && ($v_table[$tb] !== $ctrl)) {
            igk_die("Table '$tb' already loaded by : " . get_class($v_table[$tb]) . "<br />" . ", Requested =&gt; " . get_class($ctrl) . "<br />" . ", " . (igk_sys_reflect_class(get_class($v_table[$tb])))->getFileName() . " Instance possibly missmatch");
            return false;
        }
        $v_table[$tb] = $ctrl;
        return true;
    }
    ///<summary></summary>
    ///<param name="h"></param>
    ///<param name="mysql"></param>
    /**
     * 
     * @param mixed $h
     * @param mixed $mysql
     */
    private function _db_viewTables($h, $mysql)
    {
        $conf_title = array("class" => "igk-cnf-title");
        $conf_search = $this->getParam('db:searchtable');
        $d = $h->div();
        $d->Content = __("User not allowed to view database");
        $d->addBr();
        if ($mysql->connect()) {
            $mysql->selectdb(igk_configs()->db_name);
            try {
                $r = $this->_getTables($conf_search);
                $d->add("div", $conf_title)->Content = igk_configs()->db_name . " Tables";
                $d->addHSep();
                $d->add(new HtmlSearchNode($this->getUri("searchtable"), $conf_search))->setClass("dispb");
                $d->addHSep();
                $frm = $d->addForm();
                $frm["action"] = $this->getUri("tableview");
                $v_table = $frm->addTable();
                if ($r && ($r->Rows != null)) {
                    $v_theader = false;
                    foreach ($r->Rows as $tab) {
                        if ($tab) {
                            if (!$v_theader) {
                                $v_theader = true;
                                $li = $v_table->addTr();
                                HtmlUtils::AddToggleAllCheckboxTh($li);
                                $li->add("th", array("class" => "fitw"))->Content = __(IGK_FD_NAME);
                                $li->add("th")->Content = IGK_HTML_SPACE;
                                $li->add("th")->Content = IGK_HTML_SPACE;
                            }
                            $tr = $v_table->addTr();
                            foreach ($tab as  $v) {
                                $s = $v;
                                $tr->addTd()->addInput("tname[]", "checkbox", $s);
                                $tr->addTd()->li()->addA(igk_js_post_frame($this->getUri("db_viewtableentries_ajx&n=" . $s . "&from=" . igk_configs()->db_name)))->Content = $s;
                                $this->__addEditTable($tr, $s, igk_configs()->db_name);
                                // $tr->td()->li()->addA(igk_js_post_frame($this->getUri("db_droptable_ajx&n=" . $s . "&from=" . igk_configs()->db_name)))->add("img", array(
                                //     "width" => "16px",
                                //     "height" => "16px",
                                //     "src" => R::GetImgUri("drop_16x16"),
                                //     "alt" => __("info.droptable")
                                // ));

                                $tr->td()->li()->ajxa($this->getUri("db_droptable_ajx&n=" . $s . "&from=" . igk_configs()->db_name))
                                    ->Content = igk_svg_use("drop");
                                // ->add("img", array(
                                //     "width" => "16px",
                                //     "height" => "16px",
                                //     "src" => R::GetImgUri("drop_16x16"),
                                //     "alt" => __("info.droptable")
                                // ));
                            }
                        }
                    }
                    $div = $frm->addActionBar();
                    $div->addAJXA($this->getUri("db_dropSelectedTable_ajx"))->Content = __("btn.droptableselection");
                }
                igk_html_toggle_class($v_table, "tr");
            } catch (\Exception $ex) {
            }
            $mysql->close();
        } else {
            $h->div()->Content = "failed to connect to database";
        }
    }
    ///<summary></summary>
    ///<param name="searchkey" default="null"></param>
    /**
     * 
     * @param mixed $searchkey the default value is null
     */
    private function _getTables($searchkey = null)
    {
        $r = null;
        $ad = igk_get_data_adapter($this, true);
        $tab = [];
        if ($ad && $ad->connect(igk_configs()->db_name)) {
            $r = $ad->listTables();
            $ad->close();
            if ($r) {
                if (count($r) > 0) {
                    if ($searchkey != null) {
                        $q = strtolower($searchkey);
                        foreach ($r as $t) {
                            if (strstr(strtolower($t->table), $q)) {
                                $tab[] = $r;
                            }
                        }
                    } else {
                        $tab = $r;
                    }
                    return $tab;
                }
            }
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="searchkey" default="null"></param>
    /**
     * 
     * @param mixed $searchkey the default value is null
     */
    private function _getTablesFromSelectedDb($searchkey = null)
    {
        if (empty($this->SelectedDb))
            return;
        $r = null;
        $mysql = igk_get_data_adapter($this, true);
        if ($mysql) {
            $mysql->connect($this->SelectedDb);
            $r = $mysql->listTables();
            $mysql->close();
            if ($r && !$r->ResultTypeIsBoolean()) {
                $tab = $r->CreateEmptyResult($r);
                $n = $r->Columns[0]->name;
                if ($searchkey != null) {
                    $q = strtolower($searchkey);
                    foreach ($r->Rows as $t) {
                        if (strstr(strtolower($t->$n), $q)) {
                            $tab->addRow(array("Table" => $t->$n));
                        }
                    }
                    return $tab;
                } else {
                    foreach ($r->Rows as $t) {
                        $tab->addRow(array("Table" => $t->$n));
                    }
                    return $tab;
                }
            }
        }
        return null;
    }

    ///<summary></summary>
    ///<param name="frm"></param>
    /**
     * 
     * @param mixed $frm
     */
    private function _showDataBaseBackup($frm)
    {
        $v_dir = igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER;
        $bckdiv = $frm->div();
        $bckdiv->h2()->Content = __("title.backup");
        $frm->notifyhost("db_restore");
        $v_table = $frm->table()->setClass('igk-table-striped');
        $v_hasfile = false;
        if (is_dir($v_dir)) {
            $v_theader = false;
            foreach (igk_io_getfiles($v_dir, "/\.(csv|zip)$/", false) as $file) {
                if (!$v_theader) {
                    $v_theader = true;
                    $li = $v_table->tr();
                    HtmlUtils::AddToggleAllCheckboxTh($li);
                    $li->th()->Content = __(IGK_FD_NAME);
                    $li->th()->Content = IGK_HTML_SPACE;
                    $li->th()->Content = IGK_HTML_SPACE;
                }
                $f = basename($file);
                $li = $v_table->tr();
                $li->td()->addInput(IGK_STR_EMPTY, "checkbox");
                $li->td()->add("a", array("href" => $this->getUri("downloadbackupfile&file=" . $f)))->Content = $f;
                $li->td()->ajxa($this->getUri("db_dbRestore&file=" . $f))
                    ->Content = igk_svg_use("reload");

                // ->addImg()->setAttributes(array(
                //     "width" => 16,
                //     "height" => 16,
                //     "src" => R::GetImgUri(trim("db_restore_16x16")),
                //     "alt" => __("restore")
                // ));
                $li->td()->ajxa(
                    $this->getUri("dropBackup&file=" . $f)
                )
                    ->content = igk_svg_use("drop");
                // HtmlUtils::AddImgLnk($li->add("td", array("class" => "igk-table-img-action_16x16")), $this->getUri("dropBackup&file=" . $f), "drop_16x16", "16px", "16px", "lb.dropbackup");
                $v_hasfile = true;
            }
        }
        if ($v_hasfile) {
            $bar = $frm->actionbar();
            $bar->ajxa($this->getUri("ClearBackup"))
                ->setClass("igk-btn")
                ->Content = __("btn.clearAllBackup");
        } else {
            $frm->div()->Content = __("No backup found");
        }
        igk_html_toggle_class($v_table, "tr");
    }
    ///<summary></summary>
    ///<param name="r"></param>
    ///<param name="$c"></param>
    ///<param name="conf_title" default="null"></param>
    /**
     * 
     * @param mixed $r
     * @param mixed $c
     * @param mixed $conf_title the default value is null
     */
    private function _showDataBases($r, $c, $conf_title = null)
    {
        $conf_title = $conf_title ?? array("class" => "igk-cnf-title");
        igk_html_add_title($c, "title.DATABASES");
        $c->addHSep();
        $sdb = $this->SelectedDb;
        if (!empty($sdb)) {
            $c->div()->addTip()->Content = __("tip.db.selecteddb", $sdb);
        } else {
            $c->div()->Content = __("No Database selected");
        }
        $div = $c->div();
        $div["class"] = "no-wrap";
        $frm = $div->form();
        $frm["action"] = $this->getUri("dataview");
        $v_table = $frm->div()->setClass("overflow-x-a")->addTable()->setClass("igk-table-striped");
        $v_theader = false;
        if ($r->RowCount > 0) {
            if ($this->SelectedDb == null)
                $this->SelectedDb = strtolower(igk_configs()->db_name);
            foreach ($r->Rows as $tab) {
                if (!$v_theader) {
                    $v_theader = true;
                    $li = $v_table->addTr();
                    // HtmlUtils::AddToggleAllCheckboxTh($li);
                    $li->add("th")->space();
                    $li->add("th", array("class" => "fitw"))->Content = __("clDataBaseName");
                    $li->add("th")->Content = IGK_HTML_SPACE;
                    $li->add("th")->Content = IGK_HTML_SPACE;
                }
                foreach ($tab as  $v) {
                    $tr = $v_table->addTr();
                    if (strtolower($v) == $this->SelectedDb) {
                        $tr["class"] = "+igk-selectdb-row";
                    }
                    $tr->addTd()->space(); // addInput(IGK_STR_EMPTY, "checkbox");
                    $li = $tr->addTd()->li();
                    if ($this->__canEditDb($v)){
                        $li->add("a", array("href" => $this->getUri("selectdb&n=" . $v)))
                            ->Content = $v;
                    }else{
                        $li->Content = $v;
                    }
                    $tr->addTd()->space(); // addLi()->add("a", array("href"=>$this->getUri("editdb&n=".$v)))->add("img", array(
                    $tr->addTd()->space();
                }
            }
        }
        $frm->addBr();
        igk_html_toggle_class($v_table, "tr");
        $frm = $div->form();
        $frm["id"] = "details";
        $frm["action"] = $this->getUri("viewtables");
        return $frm;
    }
    /**
     * can edit database
     * @param string $db 
     * @return bool 
     */
    private function __canEditDb(string $db){
        return !in_array($db, ["mysql", "information_schema", "performance_schema", "sys"]);
    }
    ///$c target node
    /**
     */
    private function _showSelectedDbTables($c, $conf_title = null, $selected = 1)
    {
        $k = "request:" . __FUNCTION__;
        $h = $this->getParam($k);
        $c->setId("tableview");
        if (!igk_is_ajx_demand()) {
            if (!$h) {
                $this->setParam($k, 1);
                $c->div()->addAJXScriptContent($this->getUri("demandToShowDataBase_ajx"));
            } else {
                $this->getParam($k, null);
                igk_die(array(
                    "code" => 404,
                    "message" => "/!\\ loaping on getting database is not allowed."
                ));
            }
            return;
        }
        // igk_wln_e(__LINE__ . " selected table name");
        $db = "";
        $v_search = $this->getParam('db:searchtable');
        $this->setParam($k, null);
        $tab = $this->_getTablesFromSelectedDb($db);
        if (count($tab) <= 0) {
            return;
        }
        igk_html_add_title($c, "title.Tables");
        $c->addHSep();
        $s = new HtmlSearchNode($this->getUri("searchtable"), $v_search);
        $c->div()->add($s);
        $div = $c->div()->setId("igkdb_tablelist");
        $frm = $div->addForm();
        $frm["action"] = $this->getUri("db_dropSelectedTable");
        $frm->div()->Content = $tab->RowCount;
        $table = $frm->div()->setStyle("overflow:auto;")->addTable();
        $tr = $table->addTr();
        HtmlUtils::AddToggleAllCheckboxTh($tr);
        $tr->add("th", array("class" => "fitw"))->Content = __("lb.Name");
        $tr->add("th")->Content = IGK_HTML_SPACE;
        $tr->add("th")->Content = IGK_HTML_SPACE;
        igk_html_paginate(
            $table,
            $frm,
            $tab->Rows,
            10,
            function ($table, $k, $v) {
                $s = $v["Table"];
                $tr = $table->addTr();
                $tr->addTd()->addInput("tname[]", "checkbox", $s);
                $tr->addTd()->add("a", array("href" => igk_js_post_frame($this->getUri("db_viewtableentries_ajx&n=" . $s . "&from=" . $this->SelectedDb))))->Content = $s;
                $this->__addEditTable($tr, $s, $this->SelectedDb);
                $tr->addTd()->li()->add("a", array("href" => igk_js_post_frame($this->getUri("db_droptable_ajx&n=" . $s))))->add("img", array(
                    "width" => "16px",
                    "height" => "16px",
                    "src" => R::GetImgUri("drop_16x16"),
                    "alt" => __("tip.dropdatabase")
                ));
            },
            igk_io_currenturi() . '/' . $this->getUri("viewtable&v="),
            $selected
        );
        $div = $frm->addActionBar();
        $div->addAJXA($this->getUri("db_dropSelectedTable_ajx"))->Content = __("btn.droptableselection");
        $div->addAJXA($this->getUri("db_drop_alltable_ajx"))->Content = __("btn.dropall");
    }
    ///<summary></summary>
    ///<param name="reset"></param>
    /**
     * 
     * @param mixed $reset the default value is 0
     */
    private function _storeDbCache($reset = 0)
    {
        // + | --------------------------------------------------------------
        // + | store all loaded tables info that matche the MySQL dataAdapter
        // + |
        $f = $this->getCacheFile();
        $o = "";
        $tables = &$this->getLoadTables();
        if ($tables) {
            $kt = 0;
            foreach ($tables as $k => $v) {
                if ($kt) {
                    $o .= IGK_LF;
                }
                $o .= $k . ":" . get_class($v);
                $kt = 1;
            }
            igk_io_w2file($f, $o, true);
        } 
        if (!$reset) {
            $tables = [];
        }
    }
    ///<summary></summary>
    ///<param name="zdiv"></param>
    /**
     * 
     * @param mixed $zdiv
     */
    private function _view_conf_backup($zdiv)
    {
        $this->_showDataBaseBackup($zdiv->addPanelBox());
    }
    ///<summary>shows datas base </summary>
    /**
     * shows datas base
     */
    private function _view_conf_datas($zdiv)
    {
        $zdiv->clearChilds();
        $frm = null;
        $mysql = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, true);
        $v_table = null;
        $pan = $zdiv->addPanelbox();
        $pan->h2()->Content = __("Datas");
        $h = $pan->addRow();
        $div1 = $h->addCol("igk-col-3-3")->div()->setClass("db_info");
        $conf_title = null;
        if ($mysql) {
            if ($mysql->connect()) {
                try {
                    $r = $mysql->sendQuery("SHOW DATABASES");
                    if ($r && !$r->ResultTypeIsBoolean() && ($r->RowCount > 0)) {
                        $this->_showDataBases($r, $div1, $conf_title);
                        $div1 = $div1->addActionBar();
                        $div1->addABtn($this->getUri("pinitSDb"))->Content = __("btn.initDb");
                        $div1->addABtn($this->getUri("backupDb"))->Content = __("btn.backupdatabase");
                    } else {
                        $row = $div1->div()->addRow();
                        $row->addCol()->Content = __("msg.cantshowdatabase");
                        $this->_db_viewTables($h, $mysql);
                    }
                    if (!empty($this->SelectedDb)) {
                        $div2 = $h->addCol("igk-col-3-2")->div();
                        $this->_showSelectedDbTables($div2, $conf_title);
                    }
                } catch (Exception $ex) {
                    $this->_db_viewTables($h->addCol("igk-col-3-3"), $mysql);
                }
                $mysql->close();
            } else {
                $div1->addPanel()->Content = __("Failed to connect and select the base MySQL's Database");
            }
        }
    }
    ///<summary></summary>
    ///<param name="zdiv"></param>
    /**
     * 
     * @param mixed $zdiv
     */
    private function _view_conf_general($zdiv)
    {
        $pan = $zdiv->addPanelBox();
        $pan->h3()->Content = __("Mysql Database Configuration");
        $pan->div()->p()->article(null, "help/mysql.db.config.help");


        $frm = $pan->addForm();
        $frm->setStyle("max-width:300px;");
        $frm["method"] = "POST";
        $frm["action"] = $this->getUri("updatedb");
        $cnf = igk_configs();
        $frm->addFields(
            [
                "dbServer" => ["attribs" => ["class" => "igk-form-control required", "placeholder" => __("Server"), "value" => $cnf->db_server]],
                "dbUser" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("user"), "value" => $cnf->db_user]],
                "dbPasswd" => ["type" => "password", "attribs" => ["class" => "igk-form-control", "placeholder" => __("password"), "value" => null]],
                "dbName" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("dbname"), "value" => $cnf->db_name]],
                "dbPort" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("dbport"), "value" => $cnf->db_port]],
            ]
        );

        $frm->addBr();
        $_cbar = $frm->addActionBar();
        $_cbar->addBtn("btn_update", __("Update"))->setClass("-clsubmit +igk-btn");
        $check_uri = $this->getUri("check_con_ajx");
        $_cbar->addBtn("btn_checkconnect", __("Check connection"))->setClass("-clsubmit +igk-btn")->setAttribute("onclick", "javascript: ns_igk.ajx.get('{$check_uri}'); return false");
    }
    ///<summary></summary>
    ///<param name="zdiv"></param>
    /**
     * 
     * @param mixed $zdiv
     */
    private function _view_conf_query($zdiv)
    {
        ///TODO: query selector tool 
        $pan = $zdiv->addPanelBox();
        $pan->h2()->Content =  __("MySQL Query Tool");
        $h = $pan->div()->addRow();
        $dv = $h->addCol("fitw")->div();
        $frm = $dv->addForm();
        $frm["action"] = $this->getUri("__db_query_r_ajx");
        $frm["igk-ajx-form"] = 1;
        $frm["igk-ajx-form-no-autoreset"] = 1;
        $frm["igk-ajx-form-target"] = "#query-s-r";
        $row = $frm->row();
        $row->col("igk-col-12-9")->div()->addTextArea()->setId("clQuery")->setClass("igk-form-control fitw-i")->setStyle("height:150px")
            ->Content = igk_getr('clQuery') ?? "Select * From `table` ";

        $ul = $row->col("igk-col-12-3")->div()->setId("query_helper")->ul();
        if ($r = $this::db_query("SHOW TABLES")) {
            $g = $r->getColumns();
            $clname = $g[0]->name;
            $ul->loop($r->getRows())->host(function ($n, $i) use ($clname) {
                $n->li()->a("#")->on("click", "\$igk('#clQuery').first().o.value = 'SELECT * from `'+this.o.innerHTML+'`';")->content = $i->{$clname};
            });
            $ul->setStyle("max-height: 150px; overflow-x:clip; overflow-y:auto;");
        }

        $acb = $frm->addActionBar();
        $acb->addInput("btn.send", "submit", __("btn.send"))->setClass("-clsubmit +igk-btn igk-btn-default");
        $dv->div()->setId("query-s-r")->setClass("fitw-i");
    }
    ///<summary></summary>
    ///<param name="zdiv"></param>
    /**
     * 
     * @param mixed $zdiv
     */
    private function _view_conf_tools($zdiv)
    {
        $pan = $zdiv->addPanelBox();
        $pan->div()->h2()->Content = __("Tools");
        $pan->notifyhost("mysql:tools");
        $frm = $pan->form();
        $bar = $frm->addActionBar();
        if (igk_server_is_local() && ($h = igk_configs()->phpmyadmin_uri)) {
            $bar->addABtn($this->getUri("gotophpmyadmin"))->Content = __("btn.phpmyadmin");
        }

        $bar->addABtn($this->getUri("pinitSDb"))->Content = __("btn.initDb");
        $bar->addABtn($this->getUri("backupDb"))->Content = __("btn.backupdatabase");
        $bar->addABtn($this->getUri("pdropDb"))->Content = __("Drop database");
        $bar->addABtn($this->getUri("pMigrate"))->Content = __("Migrate");
        $bar->abtn($this->getUri("pCleanTable"))->Content = __("Clean Database");
        if (!igk_environment()->isOPS())
            $bar->addABtn($this->getUri("pSeed"))->Content = __("Seed");
        $bar->addAJXA($this->getUri("db_reload_sys_tables_ajx"))->setClass("igk-btn")->Content = __("btn.reloadsystables");
    }
    private function notifyctrl()
    {
        return igk_notifyctrl("mysql:tools");
    }

    public function pDropDb()
    {
        if (igk_is_conf_connected()) {
            $c = new \IGK\System\Console\Commands\MysqlCommand();
            ob_start();
            $o = $c->exec((object)[
                "options" => (object)["--action" => "dropdb"]
            ]);
            $r = ob_get_clean();
            $this->notifyctrl()->success("database cleaned");
        }
        return igk_navtocurrent();
    }
    public function pCleanTable()
    {
        $ad = $this->getDataAdapter();
        $q = igk_configs()->get("db_prefix", "tbigk_") . "%";
        // $q = "%\\\\_%";
        if (!$ad->connect()) {
            return null;
        }
        $dbname = igk_configs()->db_name;
        $op = "NOT"; //igk_getr("not") ? "NOT" :  "";

        $q = $ad->escape_string($q);
        $field = "tables_in_{$dbname}";
        $rg = $ad->sendQuery("SHOW TABLES WHERE tables_in_{$dbname} {$op} LIKE '$q'");
        if ($rg && ($rg->getRowCount() > 0)) {
            $field = ucfirst($field);
            $ad->stopRelationChecking();
            $ad->beginTransaction();
            foreach ($rg->getRows() as $r) {
                igk_dev_wln($r->$field);
                $ad->sendQuery("DROP TABLE `" . $ad->escape_string($r->$field) . "`");
            }
            $ad->commit();
            $ad->restoreRelationChecking();
        }

        $ad->close();
        return igk_navtocurrent();
    }
    public function pMigrate()
    {
        if (igk_is_conf_connected()) {
            $c = new \IGK\System\Console\Commands\MysqlCommand();
            ob_start();
            $o = $c->exec((object)[
                "options" => (object)["--action" => "migrate"]
            ]);
            $r = ob_get_clean();
            $this->notifyctrl()->success(__("database migrate"));
        }
        return igk_navtocurrent();
    }
    public function pSeed()
    {
        if (igk_is_conf_connected()) {
            if (igk_environment()->isOPS()) {
                $this->notifyctrl()->error(__("database seeding is not for ops"));
                return igk_navtocurrent();
            }
            $c = new \IGK\System\Console\Commands\MysqlCommand();
            $v_notify = igk_notifyctrl("mysql:tools");
            try {
                ob_start();
                $o = $c->exec((object)[
                    "options" => (object)["--action" => "seed"]
                ]);
                ob_get_clean();
                $v_notify->success(__("database seed"));
            } catch (Exception $ex) {
                $v_notify->danger(__("something bad happend. {0}", $ex->getMessage()));
            }
        }
        return igk_navtocurrent();
    }
    ///<summary>backup mysql database</summary>
    /**
     * backup mysql database
     */
    public function backupDb()
    {
        $mysql = igk_get_data_adapter($this, true);
        if (!$mysql) {
            igk_notifyctrl()->addError("can't get " . IGK_MYSQL_DATAADAPTER . " data adapter");
            return;
        }
        $adapter = igk_get_data_adapter(IGK_CSV_DATAADAPTER);
        if (!$adapter) {
            igk_notifyctrl()->addError("can't get csv adapter");
            return;
        }
        $v_date = igk_date_now("Ymd_his");
        $v_file = igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER . "/backup_" . $v_date . ".csv";
        $out = IGK_STR_EMPTY;
        $db_table = $this->_getTables(null);
        $skip_array = [
            DbLogs::table()
        ];
        $warn = "";
        if ($mysql->connect()) {
            if ($db_table) {
                foreach ($db_table as $v) {
                    $v_tbname = $v->table;
                    if (in_array($v_tbname, $skip_array)) {
                        continue;
                    }
                    ///TODO: SELECT
                    $query = $mysql->getGrammar()->createSelectQuery($v_tbname);
                    $r = $mysql->sendQuery($query);
                    if ($r) {
                        $out .= $v_tbname . IGK_LF;
                        $out .= $adapter->toCSVLineEntry($r->Columns, "name") . IGK_LF;
                        if ($r->Rows) {
                            foreach ($r->Rows as $e) {
                                $out .= $adapter->toCSVLineEntry($e) . IGK_LF;
                            }
                        } else {
                            $warn .= ("notice: no data row for [" . $v_tbname . "]\r\n");
                        }
                        $out .= "\0" . IGK_LF;
                    } else {
                        igk_debug_wln("error: mysql adapter failed");
                        igk_debug_wln($r);
                        igk_notifyctrl()->addMsgr("Error.Config");
                    }
                }
            } else {
                igk_debug_wln("error: no table");
                igk_notifyctrl()->addMsgr("Msg.NoTable");
            }
            $mysql->close();
        }
        if (!empty($warn) && !igk_sys_env_production()) {
            igk_ilog($warn);
        }
        IO::CreateDir(dirname($v_file));
        $cout = utf8_encode($out);


        if (strlen($out) > 1000000) {
            igk_zip_content($v_file . ".zip", basename($v_file), $cout);
        } else {
            igk_io_w2file($v_file, $cout, true, 0666);
        }
        $this->View();
        igk_notifyctrl()->addMsgr("msg.dataBackup");
        igk_navtocurrent();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function check_con_ajx()
    {
        igk_reset_db_dataadapter();
        $ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        $con = "failed";
        $type = "igk-danger";
        igk_set_env("sys://Db/NODBSELECT", 1);
        if ($ad && $ad->connect()) {
            $con = "success";
            $type = "igk-success";
            $ad->close();
        } else {
        }
        igk_ajx_toast(implode(" ", [__("Connection:"), __($con)]), $type);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function ClearBackup()
    {
        if (igk_qr_confirm()) {
            $v_dir = igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER;
            IO::RmDir($v_dir);
            $this->View();
            igk_navtocurrent();
        }
        $d = igk_create_node("div");
        $form = $d->form();
        $form["action"] = $this->getUri(__FUNCTION__);
        $form->div()->p()->Content = __(IGK_MSG_DELETEALLDATABASEBACKUP_QUESTION);
        $form->actionbar(function ($a) {
            $a->submit();
        });
        $form->confirm();
        igk_ajx_panel_dialog(__("Confirm Clear Backup"), $d);
    }
    ///<summary>backup table associated to a controller</summary>
    /**
     * backup table associated to a controller
     */
    public function db_backup_tables($ctrl, $outtag, $dbname = null, $storetableinfo = true)
    {
        $tables = $this->getTablesFor($ctrl);

        if (!is_array($tables))
            return;
        $ad = igk_get_data_adapter($this, true);
        if ($ad && $ad->connect($dbname)) {
            $e = $outtag->addNode(IGK_ENTRIES_TAGNAME);
            foreach ($tables as  $v) {
                $tabinfo = $this->getDataTableDefinition($v, false);
                if (!$tabinfo)
                    continue;
                if ($storetableinfo) {
                    $def = $outtag->addNode(DbSchemas::DATA_DEFINITION);
                    $def["TableName"] = $v;
                    foreach ($tabinfo as $ck => $c) {
                        switch ($ck) {
                            case "Description":
                                break;
                            default:
                                foreach ($c as  $nn) {
                                    $col = $def->add(IGK_COLUMN_TAGNAME);
                                    $col->setAttributes($nn);
                                }
                                break;
                        }
                    }
                }
                $table = $e->addNode($v);
                $r = $ad->selectAll($v);
                foreach ($r->Rows as $m => $n) {
                    $table->addNode(IGK_ROW_TAGNAME)->setAttributes($n);
                }
            }
            $ad->close();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_Clearall_db_entry()
    {
        $r = $this->getParam("db:r");
        $dbname = $this->getParam("db:dbname");
        $tbname = $this->getParam("db:table");
        if (igk_qr_confirm()) {
            $db = igk_get_data_adapter($this, true);
            if ($db) {
                $db->connect();
                $db->selectdb($dbname);
                $db->deleteAll($tbname);
                $db->close();
            }
            $this->db_viewtableentries($dbname, $tbname);
        } else {
            $frame = igk_frame_add_confirm($this, "frame_Clearall", $this->getUri("db_Clearall_db_entry"));
            $frame->Form->Div->Content = __("Q.WILLYOUDROPTHECONTENTOFTHISTABLE", $tbname);
        }
    }
    private function _db_restore_db($file, $mode)
    {
        $r = igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER . "/" . $file;
        $v_file = igk_io_basedir($r);
        $result = false;
        if (file_exists($v_file)) {
            $str = "";
            if (igk_io_path_ext($v_file) == "zip") {
                if (igk_app()->getApplication()->lib("zip") == false) {
                    return false;
                }
                $str = igk_zip_unzip_filecontent($v_file,  $n = igk_io_basenamewithoutext($v_file));
            } else {
                $str = igk_io_read_allfile($v_file);
            }

            $str = utf8_decode($str);
            // igk_text($str);
            // exit;
            $h = explode("\0", $str);
            $table = null;
            $definition = null;
            $header = null;
            $warn = "";
            $syncdata = array();
            foreach ($h as  $v) {

                $lines = explode(IGK_LF, trim($v));
                if (count($lines) < 2)
                    continue;

                $entries = array_slice($lines, 2);
                $table = trim($lines[0]);

                $header = igk_array_tokeys(IGKCSVDataAdapter::LoadString(trim($lines[1]))[0]);
                $entriecs = array();
                $definition = IGKCSVDataAdapter::LoadString(implode(IGK_LF, $entries), true, $header);
                if (igk_count($definition) == 0)
                    continue;
                $th = igk_count($header);
                $ec = -1;
                foreach ($definition as $m => $n) {
                    $ec++;
                    if (igk_count($n) != $th) {
                        $warn .= "column header doest not match the entries definition of : {$table} : {$ec} " . IGK_LF;
                        continue;
                    }
                    $row = igk_createobj($header);
                    $i = 0;
                    foreach ($header as $ss => $tt) {
                        $row->{$ss} = $n[$i];
                        $i++;
                    }
                    $entriecs[] = $row;
                }
                $syncdata[$table] = $entriecs;
            }
            $adapt = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
            $r = 1;
            if (($adapt != null) && ($adapt->connect())) {
                $adapt->beginTransaction();
                $adapt->sendQuery("SET foreign_key_checks = 0;");
                $_syncfc = function ($adapt, $t, $e, $m) {
                    if (!($ge = $adapt->sendQuery("SELECT * FROM `{$t}` LIMIT 0")))
                        return false;

                    $tbinfo = igk_db_getdatatableinfokey($t);
                    $o = igk_createobj();
                    $g = $ge->Columns;
                    if (!$g) {
                        igk_ilog("db : $t no column found");
                        return false;
                    }
                    foreach ($ge->Columns as $d) {
                        if (!isset($tbinfo[$d->name])) {
                            igk_set_error("sys", "data table columns not match", 0xDB0001);
                            return 0;
                        }
                        $o->{$d->name} = null;
                    }
                    $r = 0;
                    switch ($m) {
                        case 1:
                            $r = $adapt->delete($t);
                            if ($r) {
                                if (is_array($e)) {
                                    foreach ($e as $v) {
                                        $mm = igk_createobj_filter($v, $o);
                                        $r = $r && $adapt->insert($t, $mm);
                                    }
                                } else
                                    $r = $r && $adapt->insert($t, $e);
                            }
                            break;
                    }
                    return $r;
                };
                $r = 1;

                foreach ($syncdata as $t => $e) {
                    $e_x = $adapt->sendQuery("SELECT * FROM `{$t}` LIMIT 0");
                    if (!$e_x) {
                        $warn .= "table [{$t}] does not exists" . IGK_LF;
                        continue;
                    }
                    $r = $r && $_syncfc($adapt, $t, $e, $mode);
                    if (!$r)
                        break;
                }
                $adapt->sendQuery("SET foreign_key_checks = 1;");
                if ($r) {
                    $adapt->commit();
                } else {
                    $adapt->rollback();
                }
                $adapt->close();
            }
            $result = $r;
        }
        return $result;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_dbRestore()
    {
        $v_f = igk_getr("file");
        $v_mode = igk_getr("mode", 1);
        if (igk_qr_confirm() && $v_mode) {
            if (!$this->_db_restore_db($v_f, $v_mode)) {
                igk_notifyctrl("db_restore")->addError(__("restore db from file failed"));
            }
            $this->View();
        } else {
            // $frame=igk_frame_add_confirm($this, "confirm_restoration", $this->getUri("db_dbRestore"));
            // $frame->Form->Div->Content=__(IGK_MSG_RESTOREBACKUPFILE_QUESTION, $v_f);
            // $frame->Form->addInput("file", "hidden", $v_f);

            $form = igk_create_node("form");
            $form["action"] = $this->getUri("db_dbRestore");
            $form->div()->setStyle("max-width: 200px")->Content = __(IGK_MSG_RESTOREBACKUPFILE_QUESTION, $v_f);
            $form->addInput("file", "hidden", $v_f);
            $form->addInput("confirm", "hidden", "1");
            $form->actionbar(
                HtmlUtils::ConfirmAction()
            );
            igk_ajx_panel_dialog(__("Confirm db restore"), $form);
        }
    }
    ///<summary>drop all tables</summary>
    /**
     * drop all tables
     */
    public function db_drop_alltable_ajx()
    {
        if (igk_qr_confirm()) {
            $this->db_drop_sys_tables();
            igk_ajx_replace_ctrl_view($this);
            igk_exit();
        }
        $b = igk_create_node();
        $frm = $b->addForm();
        $frm["action"] = $this->getUri(__FUNCTION__);
        $frm["igk-ajx-form"] = 1;
        $frm["igk-ajx-form-data"] = "{complete: function(){ ns_igk.winui.notify.close(); }}";
        $frm->addConfirm(1);
        igk_html_binddata($this, $frm->div(), "confirm.dialog.template", (object)array("clMessage" => __("msg.confirmalltablesuppression")));
        $frmdial = igk_ajx_notify_dialog(__("title.confirmalltablesuppression"), $b);
        $frmdial->renderAJX();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_drop_entry()
    {
        $n = igk_getr("n");
        $s = igk_getr("s");
        $dbname = $this->getParam("db:dbname");
        $table = $this->getParam("db:table");
        $db = igk_get_data_adapter($this, true);
        if ($db && $db->connect()) {
            $db->selectdb($dbname);
            $db->delete($table, array($s => $n));
            $db->close();
        }
        $this->db_viewtableentries($dbname, $table);
    }

    ///<summary>drop all tables</summary>
    /**
     * drop all tables
     */
    public function db_drop_sys_tables()
    {
        $ldtables = &$this->getLoadTables();
        $adapter = igk_get_data_adapter($this);
        if (!($tables = $adapter->listTables())) {
            return;
        }
        igk_ilog("START: drop system database...");
        $time = igk_start_time(__METHOD__);
        igk_environment()->set("NoDBLog", 1);
        $adapter->initForInitDb();
        $adapter->setForeignKeyCheck(0);
        foreach ($tables as $t) {
            $adapter->dropTable($t->table);
        }
        $adapter->setForeignKeyCheck(1);
        $adapter->flushForInitDb();
        igk_environment()->set("NoDBLog", null);
        igk_ilog("END: drop system database " . igk_execute_time(__METHOD__, $time));
        $ldtables = [];
    }

    ///<summary>drop table associated to a controller</summary>
    /**
     * drop table associated to a controller
     */
    public function db_drop_tables($ctrl, $dbname = null)
    {
        $tables = $this->getTablesFor($ctrl);
        if (!is_array($tables))
            return;
        $ad = igk_get_data_adapter($ctrl, true);
        if ($ad && $ad->connect($dbname)) {
            $ad->dropTable($tables);
            $ad->close();
        }
    }
    ///<summary></summary>
    ///<param name="dbname" default="null"></param>
    /**
     * 
     * @param mixed $dbname the default value is null
     */
    public function db_dropSelectedTable_ajx($dbname = null)
    {
        if (!$this->ConfigCtrl->IsConnected)
            return;
        $db = $dbname ? $dbname : igk_configs()->db_name;
        if (igk_qr_confirm()) {
            if ($db == null) {
                igk_wln("no db name selected");
                return;
            }
            $h = null;
            $adapter = igk_get_data_adapter($this, true);
            if (!$adapter)
                return;
            $query = $this->getParam("db:dropSelectedTable");
            $h = igk_getv($query, "tname");
            if ($h && $adapter->connect($db)) {
                if ($adapter->dropTable($h)) {
                    igk_notifyctrl()->addMsgr("msg.db.tabledelete_1", igk_count($h));
                } else {
                    igk_notifyctrl()->addErrorr("e.db.deletetableerror_1", igk_debuggerview()->getMessage());
                }
                $adapter->close();
            } else {
                igk_notifyctrl()->addError("Aucune table selectionner");
            }
            $this->setParam("frame:tname", null);
            $this->setParam("db:dropSelectedTable", null);
            igk_notifyctrl()->addMsgr("msg.databaseupdated");
            $c = igk_debuggerview()->getMessage();
            if (!empty($c))
                igk_notifyctrl()->addError($c);
            $this->View();
            igk_navtocurrent();
        } else {
            $tname = igk_getv($_REQUEST, "tname");
            if ($tname && (count($tname) > 0)) {
                $frame = igk_frame_add_confirm($this, "confirm_Clear_backup_file", $this->getUri("db_dropSelectedTable_ajx"));
                $frame->Form->Div->Content = __("q.deleteallselecteddbtable");
                $frame->Form->addInput("dbname", "hidden", $dbname);
                $this->setParam("db:dropSelectedTable", $_REQUEST);
                $frame->renderAJX();
            }
        }
    }
    ///<summary></summary>
    ///<param name="dbname" default="null"></param>
    ///<param name="table" default="null"></param>
    ///<param name="navigate" default="false"></param>
    /**
     * 
     * @param mixed $dbname the default value is null
     * @param mixed $table the default value is null
     * @param mixed $navigate the default value is false
     */
    public function db_droptable($dbname = null, $table = null, $navigate = false)
    {
        $n = igk_getr("n", $table);
        if (igk_qr_confirm()) {
            $mysql = igk_get_data_adapter($this, true);
            if (!$mysql)
                return;
            $dbname = igk_getr("from", $dbname);
            if ($mysql->connect($dbname)) {
                $r = $mysql->dropTable($n);
                $mysql->close();
                if (igk_is_ajx_demand()) {
                    if ($r) {
                        igk_ajx_toast('datatable : ' . $n, 'success');
                    } else {
                        igk_ajx_toast('failed : ' . $n, 'danger');
                    }
                }
            }
            if ($navigate) {
                $this->View();
                igk_navtocurrent("./#igkdb_tablelist");
                igk_exit();
            }
        } else {
            $frame = igk_frame_add_confirm($this, "confirm_drop_table", $this->getUri("db_droptable"));
            $frame->Form->Div->Content = __(IGK_MSG_DELETESINGLETABLE_QUESTION, $n);
            $frame->Form->addInput("n", "hidden", $n);
            return $frame;
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_droptable_ajx()
    {
        $frame = $this->db_droptable();
        if ($frame != null) {
            $frame->renderAJX();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_edit_entry_ajx()
    {
        $n = igk_getr("n");
        $s = igk_getr("s");
        $dbname = $this->getParam("db:dbname");
        $table = $this->getParam("db:table");
        $this->db_edit_entry_frame($this, $dbname, $table, $n, $s, true);
    }
    public function getIgnoreList($table)
    {
        static $ignoreList = null;
        if ($ignoreList === null) {
            $tb = igk_db_get_table_name(IGK_TB_USERS);
            $ignoreList = [
                $tb => ["clPwd", "clDate", "clLastLogin", "clPicture", "clId"]
            ];
        }
        return igk_getv($ignoreList, $table, ["clId"]);
    }
    public function getFieldHandler($table)
    {
        $tb = igk_db_get_table_name(IGK_TB_USERS);
        $r = array_merge([$tb => function ($columninfo, $li, $value, $iparam) {
            $v = $columninfo->name;
            switch ($v) {
                case "clClassName":
                    $li->add("label")->Content = __("lb.{$v}");
                    $sl = $li->add("select");
                    $sl->setId($v);
                    $sl->setClass("igk-form-control");
                    $sl->add("option")->setAttributes(array("value" => "0"));
                    $h = 1;
                    $cllist = igk_sys_get_projects_controllers();
                    $tcllist = array();
                    foreach ($cllist as $b => $c) {
                        $n = get_class($c);
                        $o = $sl->add("option");
                        $o['value'] = $h;
                        $o->Content = $n;
                        $h++;
                        $tcllist[] = $n;
                        if ($n == $value) {
                            $o["selected"] = "true";
                        }
                    }
                    $iparam->setParam("update:cllist", array($v => $tcllist));
                    // $iparam->setParam("update:cllist", []);
                    return 1;

                case "clParent_Id":
                    return 1;
            }
        }], igk_environment()->{"db.fieldhandler"} ?? []);
        return igk_getv($r, $table);
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="dbname"></param>
    ///<param name="table"></param>
    ///<param name="n"></param>
    ///<param name="s"></param>
    ///<param name="render" default="true"></param>
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $dbname
     * @param mixed $table
     * @param mixed $n
     * @param mixed $s
     * @param mixed $render the default value is true
     */
    public function db_edit_entry_frame($ctrl, $dbname, $table, $n, $s, $render = true)
    {
        $this->setParam("update:n", $n);
        $this->setParam("update:s", $s);
        $frm = igk_create_node("form");
        $frm["action"] = $this->getUri("db_update_entry");
        $frm["class"] = ["db-update-entry-form"];
        $frm->setStyle("min-width: 360px");
        $frm->addInput("cltable", "hidden", $table);
        $frm->addInput("cldb", "hidden", $dbname);
        $frm->addInput("cln", "hidden", $n);
        $frm->addInput("cls", "hidden", $s);
        igk_html_form_initfield($frm);

        if ($ctrl !== $this) {
            $frm->addInput("clexternal", "hidden", 1);
            $frm->addInput("ctrl", "hidden", $ctrl->getName());
        }
        $table = igk_db_get_table_name($table);
        $mysql = igk_get_data_adapter($ctrl, true);
        if (($mysql != null) && $mysql->connect($dbname)) {

            $e = $mysql->select($table, array($s => $n));
            $ignore_list = $this->getIgnoreList($table);
            $field_handler = $this->getFieldHandler($table);

            if ($e->RowCount == 1) {
                $ul = $frm->add("ul");
                $l = $e->getRowAtIndex(0);
                foreach ($e->Columns as $k) {
                    $li = $ul->li();
                    $v = $k->name;
                    if (in_array($v, $ignore_list))
                        continue;
                    if ($field_handler && $field_handler($k, $li, $l->$v, $this)) {
                        continue;
                    }
                    igk_html_build_form_array_entry($v, $k->typeName, $li, $l->$v);
                }
            }
            $mysql->close();
        }
        $frm->addHSep();
        $frm->addInput("btn_update", "submit", __("Update"));
        if ($render) {
            igk_ajx_panel_dialog(__("Edition") . " : " . $table, $frm);
        }
        return $frm;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_insert_db_entry()
    {
        $r = $this->getParam("db:r");
        $dbname = $this->getParam("db:dbname");
        $n = $this->getParam("db:table");
        $b = igk_get_robj();
        $db = igk_get_data_adapter($this, true);
        if ($db) {
            $db->connect();
            $db->selectdb($dbname);
            $t = igk_db_getdatatableinfokey($n);
            $db->insert($n, (array)$b, $t);
            $db->close();
        }
        $this->db_viewtableentries($dbname, $n);
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_insert_db_entry_frame_ajx()
    {
        $frame = igk_html_frame($this, __FUNCTION__);
        $frame->clearChilds();
        $frame->Title = __("title.db.insertnewentry");
        $d = $frame->BoxContent;
        $frm = $d->addForm();
        $frm["action"] = $this->getUri("db_insert_db_entry");
        $ul = $frm->add("ul");
        $c = $this->getParam("db:columns");
        if ($c) {
            foreach ($c as $k) {
                $li = $ul->li();
                $pwd = $k->name == IGK_FD_PASSWORD;
                switch (strtolower($k->type)) {
                    case "blob":
                        $li->addSLabelTextarea($k->name, "lb." . $k->name, array("class" => "-cltextarea"));
                        break;
                    case "string":
                    case "text":
                    default:
                        $li->addSLabelInput($k->name, $pwd ? "password" : "text", $pwd ? "" : $k->def);
                        break;
                }
            }
        }
        $frm->addHSep();
        $frm->addBtn("btn_add", __("btn.add"));
        $frame->renderAJX();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_reload_sys_tables_ajx()
    {
        // form request
        if (RequestHandler::IsHandling($this->getUri(__FUNCTION__)) && !igk_is_conf_connected()) {
            throw new NotAllowedRequestException();
        }
        if (igk_server()->method("POST") && igk_qr_confirm()) {
            $this->db_drop_sys_tables();
            igk_environment()->querydebug = 1;
            $this->pinitSDb(false);
            if (igk_is_ajx_demand()) {
                $this->View();
                igk_ajx_panel_dialog_close();
                igk_ajx_replace_node($this->ConfigNode);
                igk_ajx_toast(__("Operation Complete"), "igk-success");
            } else {
                igk_navto_referer();
            }
        } else {
            $frm = igk_create_node("form");
            $frm["action"] = $this->getUri(__FUNCTION__);
            $frm["igk-ajx-form"] = 1;
            $frm->div()->p()->Content = __("q.reloadsystables");
            $frm->actionbar(HtmlUtils::ConfirmAction());
            $frm->confirm();
            igk_ajx_panel_dialog(__("Confirm"), $frm);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function db_update_entry()
    {
        if (!$this->ConfigCtrl->getIsConnected())
            return;


        $dbname = igk_getr("cldb", $this->getParam("db:dbname"));
        $table = igk_db_get_table_name(igk_getr("cltable", $this->getParam("db:table")));
        $n = igk_getr("cln", $this->getParam("update:n"));
        $s = igk_getr("cls", $this->getParam("update:s"));
        $tclist = $this->getParam("update:cllist");
        $this->setParam("update:cllist", null);
        $adapter = igk_get_data_adapter($this, true);
        $ext = igk_getr('clexternal');
        if ($adapter) {

            $o = igk_get_robj();
            igk_array_filter((array)$o, array_fill_keys($v_list = ["cln", "cldb", "cls", "cltable", "clexternal"], null));
            foreach ($v_list as $m) {
                unset($o->$m);
            }
            if ($tclist) {
                foreach ($tclist as $k => $v) {
                    $ii = $o->$k - 1;
                    if ($ii >= 0)
                        $o->$k = igk_getv($v, $ii);
                }
            }
            $adapter->connect($dbname);
            $adapter->update($table, $o, array($s => $n), igk_db_getdatatableinfokey($table));
            $adapter->close();
        }
        if (!$ext) {
            $this->db_viewtableentries($dbname, $table);
        } else {
            $ctrl = igk_getctrl(igk_getr("ctrl"));
            if ($ctrl != null) {
                $ctrl->View();
            }
        }
    }
    ///<summary></summary>
    ///<param name="dbname" default="null"></param>
    ///<param name="table" default="null"></param>
    ///<param name="navigate" default="true"></param>
    ///<param name="adapter" default="null"></param>
    /**
     * 
     * @param mixed $dbname the default value is null
     * @param mixed $table the default value is null
     * @param mixed $navigate the default value is true
     * @param mixed $adapter the default value is null
     */
    public function db_viewtableentries($dbname = null, $table = null, $navigate = true, $adapter = null)
    {
        $tb = $table ? $table : igk_getr("n");
        $db = $dbname == null ? igk_getr("from") : $dbname;
        $mysql = $adapter == null ? igk_get_data_adapter($this, true) : $adapter;
        if (!$mysql)
            return;
        $mysql->connect($db);
        $r = $mysql->selectAll($tb);
        $frame = igk_html_frame($this, "db_view_entries", "./#igkdb_tablelist");
        $frame->Title = __("title.db_viewtableentries_1", $tb);
        $frame->clearChilds();
        $div = $frame->BoxContent->div();
        $div["class"] = "igk-db-tableentries";
        $title = $div->div();
        $div->addHSep();
        $title->setClass("title");
        $content = $div->div();
        $content["class"] = "datas";
        $content["style"] = "overflow:auto;";
        $title->Content = __("title.TableInfo", $db . "." . $tb);
        $table = $content->addTable();
        $table["class"] = "fitw";
        $table["style"] = "border:1px solid black";
        $tr = $table->addTr();
        $tr->add("th")->Content = IGK_HTML_SPACE;
        $pkey = null;
        $pkname = null;
        foreach ($r->Columns as $k) {
            $td = $tr->add("th");
            $td->Content = __($k->name);
            if (($pkey == null) && $k->primary_key == 1) {
                $pkey = $k->name;
            }
        }
        $tr->add("th")->Content = IGK_HTML_SPACE;
        $tr->add("th")->Content = IGK_HTML_SPACE;
        foreach ($r->Rows as $k) {
            $tr = $table->addTr();
            $tr->addTd()->Content = IGK_HTML_SPACE;
            foreach ($k as $e => $v) {
                $tr->addTd()->Content = ($e == IGK_FD_PASSWORD) ? IGK_HTML_SPACE : $v;
            }
            HtmlUtils::AddImgLnk($tr->addTd(), igk_js_post_frame($this->getUri("db_edit_entry_ajx&n=" . $k->$pkey . "&s=" . $pkey)), "edit_16x16");
            HtmlUtils::AddImgLnk($tr->addTd(), $this->getUri("db_drop_entry&n=" . $k->$pkey . "&s=" . $pkey), "drop_16x16");
        }
        $div->addHSep();
        $this->setParam("db:dbname", $db);
        $this->setParam("db:table", $tb);
        $this->setParam("db:r", $r);
        $this->setParam("db:columns", $r->Columns);
        $cdiv = $div->div();
        HtmlUtils::AddImgLnk($cdiv, igk_js_post_frame($this->getUri("db_insert_db_entry_frame_ajx")), "add_16x16");
        HtmlUtils::AddImgLnk($cdiv, $this->getUri("db_Clearall_db_entry"), "drop_16x16");
        HtmlUtils::AddBtnLnk($div, __("btn.close"), $frame->CloseUri)->setAttribute("onclick", "javascript: (function(q){ ns_igk.winui.framebox.close_currentframe(q); })(this); return false;");
        HtmlUtils::ToggleTableClassColor($table, "tr");
        $mysql->Close();
        if ($navigate) {
            igk_navtocurrent();
            igk_exit();
        }
        return $frame;
    }
    ///<summary></summary>
    ///<param name="dbname" default="null"></param>
    ///<param name="table" default="null"></param>
    ///<param name="adapter" default="null"></param>
    /**
     * 
     * @param mixed $dbname the default value is null
     * @param mixed $table the default value is null
     * @param mixed $adapter the default value is null
     */
    public function db_viewtableentries_ajx($dbname = null, $table = null, $adapter = null)
    {
        $frame = $this->db_viewtableentries($dbname, $table, false, $adapter);
        $frame->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function dbChanged()
    {
        $this->resetDataTableDefinition();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function demandToShowDataBase_ajx()
    {
        $c = igk_create_node("div");
        $c["class"] = "igk-dbview";
        $this->_showSelectedDbTables($c, "Database");
        $c->renderAJX();
        igk_exit();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function downloadbackupfile()
    {
        $v_file = igk_getr("file");
        $v_f = igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER . "/" . $v_file;
        if (file_exists($v_f)) {
            igk_download_file(basename($v_f), $v_f);
        } else {
            igk_notifyctrl()->addError("file not present");
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function dropBackup()
    {
        $v_file = igk_getr("file");
        if (igk_qr_confirm()) {
            $v_file = !empty($v_file) ? StringUtility::Uri($v_file) : die("file not valid");
            if (strpos($v_file, "/") !== false)
                die("not allowed not valid");
            $v_f =  igk_io_applicationdir() . "/" . IGK_BACKUP_FOLDER . "/" . $v_file;
            @unlink($v_f);
            $this->View();
            igk_navtocurrent();
        }
        $d = igk_create_node("div");
        $d->p()->Content = __(IGK_MSG_DELETEABACKUPFILE_QUESTION, $v_file);
        $form = $d->form();
        $form["action"] = $this->getUri(__FUNCTION__);
        $form->input("file", "hidden", $v_file);
        $form->input("confirm", "hidden", 1);
        $form->actionbar(function ($a) {
            $a->submit();
        });
        igk_ajx_panel_dialog("Drop", $d);
    }

    ///<summary></summary>
    /**
     * 
     */
    public function dropsysdb()
    {
        if (igk_is_conf_connected()) {
            $ad = igk_get_data_adapter($this);
            if ($ad && $ad->connect()) {
                $ad->sendQuery("drop database if exists " . igk_configs()->db_name . " ");
                $ad->close();
            }
        }
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="exp"></param>
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $exp
     */
    public function dropTablesRegex($ctrl, $exp)
    {
        $db = igk_get_data_adapter($ctrl, true);
        if ($db->connect()) {
            $r = $db->listTables();
            $n = $r->Columns[0]->name;
            $tab = array();
            foreach ($r->Rows as $v) {
                if (preg_match($exp, $v->$n)) {
                    $tab[] = $v->$n;
                }
            }
            $db->dropTable($tab);
            $db->close();
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function editdb()
    {
        $this->View();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getAllTableNames()
    {
        $tables = [];
        $adName = $this->getDataAdapterName();
        if ($ctrl_s = igk_app()->getControllerManager()->getControllers()) {
            foreach ($ctrl_s as $c) {
                if (($c === $this) || ($c->getDataAdapterName() != $adName))
                    continue;
                if ($c->getUseDataSchema()) {
                    $r = $this->loadDataAndNewEntriesFromSchemas();
                    if ($r && ($tb = $r->Data)) {
                        $tables = array_merge($tables, array_keys($tb));
                    }
                } else {
                    if ($table = $c->getTableName()) {
                        $tables[] = $table;
                    }
                }
            }
        }
        return $tables;
    }
    ///<summary></summary>
    /**
     * get the cache faile used to store table:controller definition
     */
    public function getCacheFile()
    {
        return igk_io_cachedir() . DIRECTORY_SEPARATOR . ".mysql.db.cache";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getConfigPage()
    {
        return "mysqldatabase";
    }
    ///<summary>return the controller that manage the table name</summary>
    /**
     * return the controller that manage the table name
     */
    public function getDataTableCtrl($tablename)
    {
        $tab = &$this->getLoadTables();
        if (isset($tab[$tablename])) {
            return $tab[$tablename];
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="global" default="true"></param>
    /**
     * 
     * @param mixed $tablename
     * @param mixed $global the default value is true
     */
 

    ///<summary></summary>
    /**
     * 
     */
    public function getDbConstantFile()
    {
        return igk_sys_db_constant_cache();
    }
    ///<summary>Array of loaded tables</summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return array tables list 
     */
    public function &getLoadTables()
    {
        static $tables = null;
        if ($tables == null) {
            $tables = array();
        }
        return $tables;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getName()
    {
        return IGK_MYSQL_DB_CTRL;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getSearhTable()
    {
        return $this->getParam(self::SEARCH_DB);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getSelectedDb()
    {
        return $this->getParam(self::SELECTED_DB) ?? igk_configs()->db_name;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getTabInfo()
    {
        return $this->getParam(self::TABINFO_DB);
    }
    /**
     * 
     * @param mixed $ctrl 
     * @param bool $include_dependency 
     * @return false|array 
     * @throws IGKException 
     * @deprecated use DbSchema insteed
     */
    public function getTablesFor($ctrl, $include_dependency = false)
    {
        igk_trace();
        die("not implements" . __METHOD__);
        return null;

        if (is_string($ctrl)) {
            $h = igk_getctrl($ctrl);
            if ($h == null)
                return false;
            $ctrl = $h;
        }
        $t = array();
        $tab = &$this->getLoadTables();
        foreach ($tab as $k => $v) {
            if ($v == $ctrl) {
                $t[$k] = $k;
            }
        }
        if ($include_dependency) {
            $s = array_merge($t, array());
            while ($s && (igk_count($s) > 0)) {
                $d = array_pop($s);
                $tabinfo = $this->getDataTableDefinition($d, false);
                if (!$tabinfo)
                    continue;
                $info = igk_array_object_refkey(igk_getv($tabinfo, "ColumnInfo"), IGK_FD_NAME);
                foreach ($info as $m => $n) {
                    if (!isset($n->clLinkType) || isset($s[$n->clLinkType]) || isset($t[$n->clLinkType]))
                        continue;
                    $s[$n->clLinkType] = $n->clLinkType;
                    $t[$n->clLinkType] = $n->clLinkType;
                }
            }
        }
        return $t;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getViewMyAdmin()
    {
        return $this->getParam(self::VIEWMYADMIN_DB);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function gotophpmyadmin()
    {
        // if($this->getViewMyAdmin()){
        if (igk_server_is_local() && $h = igk_configs()->phpmyadmin_uri) {
            igk_navto($h);
            igk_exit();
        }
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="args"></param>
    /**
     * 
     * @param mixed $args
     */
    public function init_table($args)
    {
        $e = $args->args;
        $o = igk_getv($e, 0);
        $tbn = igk_getv($e, 1);
        $data = igk_getv($e, 2);
        $this->_addTable($tbn, $o);
    }


    ///<summary></summary>
    ///<param name="ctrlid" default="null"></param>
    /**
     * 
     * @param mixed $ctrlid the default value is null
     */
    function initCtrlDb($ctrlid = null)
    {
        $ctrl = igk_getctrl($ctrlid);
        if ($ctrl) {
            igk_set_env(IGK_ENV_DB_INIT_CTRL, $ctrl);
            $ctrl->initDb();
            igk_set_env(IGK_ENV_DB_INIT_CTRL, null);
        }
    }
    ///<summary></summary>
    ///<param name="view" default="true"></param>
    ///<param name="nav" default="true"></param>
    /**
     * 
     * @param mixed $view the default value is true
     * @param mixed $nav the default value is true
     */
    public function initSDb($view = true, $nav = true)
    {
        $ad = igk_get_data_adapter($this, true);
        if ($ad) {
            igk_set_env("sys://Db/NODBSELECT", 1);
            $ad->initForInitDb();
            if (!$ad->connect()) {
                igk_wln("/!\\ connection failed");
                igk_exit();
            }
            igk_set_timeout(0);
            $ad->beginTransaction();
            // + | init db environment
            // $init_env=function(){
            igk_set_env("sys://Db/NODBSELECT", null);
            igk_set_env("sys://db_init", 1);
            igk_set_env("sys://db_init/error", 0);
            igk_set_env(IGK_ENV_DB_INIT_CTRL, null);
            // };
            // $init_env();
            $callable = array($this, "init_table");
            $db_name = igk_configs()->db_name;

            igk_reg_hook(IGK_NOTIFICATION_INITTABLE, $callable);
            $ad->createdb($db_name);
            $ad->selectdb($db_name);
            $icomplete = [];
            if (function_exists($global_fc = "InitDb")) {
                call_user_func_array($global_fc, []);
            } else {
                $ad_n = $this->getDataAdapterName();
                // $v_ctab = igk_app()->getControllerManager()->getControllers();
                $v_cctab = ConfigControllerRegistry::ResolvAndInitControllers();
                usort($v_cctab, DbUtils::OrderController); 

                foreach ($v_cctab as $k) {
                    if (($k == $this) || ($k->getDataAdapterName() != $ad_n))
                        continue;
                    igk_set_env(IGK_ENV_DB_INIT_CTRL, $k);
                    $k->initDb();
                    if (method_exists($k, "initDataComplete")) {
                        $icomplete[] = $k;
                    }
                }
            }
            igk_notifyctrl()->addMsgr("msg.db.initialized");
            igk_notification_unreg_event(IGK_NOTIFICATION_INITTABLE, $callable);
            $ad->flushForInitDb();
            if (igk_get_env("sys://db_init/error") > 0) {
                $ad->rollback();
            } else {
                $ad->commit();
                igk_hook(IGKEvents::HOOK_DB_INIT_ENTRIES, array($this));
                igk_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, ["controller" => $this]);
            }
            igk_set_env("sys://db_init", null);
            igk_set_env("sys://db_init/error", null);
            $ad->close();
            if ($view) {
                $this->View();
            }
        } else {
            igk_notifyctrl()->addError(__("E.DbNotConnected"));
            igk_ilog("/!\\ Connect FAILED");
        }
        if ($nav) {
            igk_navtocurrent();
        }
    }

    ///<summary>public function expose system init db . must be conf connected</summary>
    /**
     * public function expose system init db . must be conf connected
     */
    function pinitSDb($nav = true)
    {
        if (!igk_is_conf_connected()) {
            igk_set_header(403);
            igk_navtocurrent();
        }
        set_time_limit(0);
        igk_set_env(__FUNCTION__, 1);
        igk_notification_reset(IGKEvents::HOOK_DB_INIT_ENTRIES);
        IO::RmDir(IGK_APP_DIR . "/Caches/db");
        $this->resetDataTableDefinition();
        $ad = igk_get_data_adapter($this);
        $dbname = igk_configs()->db_name;

        if ($dbname && $ad->connect(null, 0)) {
            if (!$ad->selectdb($dbname) && !$ad->createDb($dbname)) {
                $ad->close();
                igk_dev_wln_e("failed : create db");
            } else {
                $ad->selectdb($dbname);
                $ad->setForeignKeyCheck(0);
                $fc = array($this, '__inittable_callback');
                $keye = 'sys://event/sdb/inittable';
                igk_reg_session_event($keye, $fc);
                $this->initSDb(true, false);
                igk_unreg_session_event($keye, $fc);
                igk_invoke_session_event("sys://event/sdb/finish", array($this, null));

                $ad->setForeignKeyCheck(1);
                $ad->close();
                igk_set_env(__FUNCTION__, null);
                $this->_storeDbCache();
                igk_getctrl(IGK_SESSION_CTRL)->forceview();
                $this->notifyctrl()->success(__("init system database"));
                igk_debug_wln("init system db finish");
            }
        }
        igk_close_session();
        if ($nav && !igk_is_ajx_demand()) {
            igk_ob_clean();
            igk_navtocurrent();
        }
    }
    ///<summary></summary>
    ///<param name="tablename"></param>
    ///<param name="inf"></param>
    /**
     * 
     * @param mixed $tablename
     * @param mixed $inf
     */
    private function regInfo($tablename, $inf)
    {
        if (self::$sm_tabinfo === null)
            self::$sm_tabinfo = array();
        self::$sm_tabinfo[$tablename] = $inf;
    }


    ///<summary></summary>
    /**
     * 
     */
    protected function registerHook()
    {
        igk_reg_hook(IGK_HOOK_DB_CHANGED, function ($e) {
            $this->dbChanged($e);
        });
        igk_reg_hook(IGK_NOTIFICATION_INITTABLE, function ($e) {
            // igk_trace(); 
            $this->_addTable($e->args[1], $e->args[0]);
        });
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="tbname"></param>
    ///<param name="inf"></param>
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $tbname
     * @param mixed $inf
     */
    private function _regTableDefinition($ctrl, $tbname, $inf)
    {
        $tab = &$this->getLoadTables();
        $tab[$tbname] = $ctrl;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function resetDataTableDefinition()
    {
        return;
        igk_die(__METHOD__);
        /**
         * @var array $tab
         */
        // self::$sm_tabinfo=null;
        // $tab= & $this->getLoadTables();
        // $tab = []; 
        // $f=$this->getCacheFile();
        // if(file_exists($f))
        //     @unlink($f);
        // igk_env_count(__FUNCTION__);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function searchtable()
    {
        $q = igk_getr("q");
        $this->setParam('db:searchtable', $q);
        $this->View();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function selectdb()
    {
        $n = igk_getr("n");
        $this->setSelectedDb($n);
        $this->View();
        if ($this->__canEditDb($n)){
            $cnf = igk_configs();
            $cnf->db_name = $n;
            igk_save_config();
        }
        igk_navtocurrent();
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    private function setSelectedDb($v)
    {
        $this->setParam(self::SELECTED_DB, $v);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function updatedb()
    {
        $server = igk_getr("dbServer");
        $user = igk_getr("dbUser");
        $pwd = igk_getr("dbPasswd");
        $dbname = igk_getr("dbName");
        $dbPort = igk_getr("dbPort");
        $cnf = igk_configs();

        $cnf->db_pwd = $pwd;
        $cnf->db_server = $server;
        $cnf->db_user = $user;
        $cnf->db_name = $dbname;
        $cnf->db_port = $dbPort;
        igk_save_config();
        igk_resetr();
        igk_notifyctrl()->addSuccessr("msg.databaseinfoupdated");
        try {
            if ($db = igk_get_data_adapter($this)) {
                $db->Reset();
            }
        } catch (\Exception $ex) {
            igk_ilog(__("failed to reset and connect to db: " . $ex->getMessage()));
        }
        $this->View(); 
        igk_navtocurrent(); 
    }
    
    ///<summary></summary>
    /**
     * 
     */
    public function View(): BaseController
    { 
        if (igk_is_ajx_demand()) {
            $p = igk_getr("v", null);
            if (!empty($p)) {
                $t = igk_create_node("div");
                $f = "_view_conf_{$p}";
                if (method_exists($this, $f)) {
                    $this->setParam("tabview", $p);
                    // $this->setFlag("tabview", $p);
                    call_user_func_array(array($this, $f), array($t));
                    $t->obdata(function()use($p){
                        igk_ajx_replace_uri(igk_io_request_uri_path() . "#!p=" . $p);
                    });
                    igk_do_response($t);//->renderAJX();
                } else {
                    igk_set_header(404);
                    igk_wln_e(__("no function to found!", $p));
                }
            }
            return $this;
        }
        $c = $this->getTargetNode();
        if (!$this->getIsVisible()) {
            $c->remove();
        } else {
            $this->ConfigNode->add($c);
            $c = $c->clearChilds()->addPanelBox();
            igk_html_title($c, __("Configure MySQL Database"));
            $c->addNotifyHost();
            $h = $c->addRow();
            $div = $h->div();
            $pan = $div->addPanelBox();
            $pan->div()->Content = sprintf(__("DataBase : %s"), "MySQL");
            $pan->div()->Content = sprintf(__("Available : %s") , igk_parsebool(defined("IGK_MSQL_DB_Adapter")));
            $pan->div()->Content = "MySQL : " . (defined('IGK_MSQL_DB_AdapterFunc') ? igk_parsebool(IGK_MSQL_DB_AdapterFunc) : 0);
            $pan->div()->Content = "MySQLi : " . (defined('IGK_MSQLi_DB_AdapterFunc') ? igk_parsebool(IGK_MSQLi_DB_AdapterFunc) : 0);
            $cview = $this->getParam("tabview");
            $tab = $div->addComponent($this, HtmlComponents::AJXTabControl, "db:tab-control", 1);
            $tab->addTabPage(__("General"), $this->getUri("view&v=general"), empty($cview) || $cview == 'general');
            $tab->addTabPage(__("Datas"), $this->getUri("view&v=datas"), $cview == 'datas');
            $tab->addTabPage(__("Backup"), $this->getUri("view&v=backup"), $cview == 'backup');
            $tab->addTabPage(__("Tools"), $this->getUri("view&v=tools"), $cview == 'tools');
            $tab->addTabPage(__("Query"), $this->getUri("view&v=query"), $cview == 'query');
        }
        return $this;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function viewtable()
    {
        $selected = igk_getr("v") ?? 1;
        $n = igk_create_node("div");
        $this->_showSelectedDbTables($n, null, $selected);
        $n->renderAJX();
        igk_exit();
    }

    protected function initComplete($context = null)
    {
        parent::initComplete($context);

        // system used download application
        igk_reg_hook(IGK_NOTIFICATION_APP_DOWNLOADED, function ($e) {
            $appName = $e->args["name"];
            // igk_ilog("downloadApp...".$appName);
            DbLogs::add(
                "download app - " . $appName,
                "APP_DOWNLOAD"
            );
            if ($s = Apps::select_row(array("clName" => $appName))) {
                $t =  Number::FromBase($s->clDownloadTime, 36);
                $t++;
                $s->clDownloadTime = Number::ToBase($t, 36);
                $s->clLast = null;
                $s->update();
            } else {
                Apps::create(array(
                    "clName" => $appName,
                    "clDownloadTime" => 1
                ));
            }
        });
    }
}
