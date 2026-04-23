<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKInstallSiteConfig.php
// @date: 20220803 13:48:54
// @desc: 
// @file: IGKInstallSiteConfig

//
use IGK\Controllers\BaseController;
use IGK\System\Configuration\Controllers\ConfigControllerBase; 
use IGK\System\Installers\InstallSite;
use IGK\System\WinUI\Menus\MenuItem;
use function igk_resources_gets as __;
/**
 * install site configs
 * @package 
 */
class IGKInstallSiteConfig extends ConfigControllerBase
{
    /**
    * Constant: ref name.
    * @var mixed
    */
    const REF_NAME = "{31f1fa34-33ef-a96c-08c8-f15dc54fadef}";
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
		return  self::REF_NAME;
	}
    /**
    * Installs.
    * @param null|mixed $folder
    * @param null|mixed $packagefolder
    */
    public function install($folder = null, $packagefolder = null)
	{
		if ($packagefolder === null) {
			$packagefolder = igk_get_packages_dir();
		}
		if ($folder == null) {
			if (igk_server()->method("POST") && igk_valid_cref(1)) {
				$folder = igk_uri(igk_getr("rootdir", $folder));
				$packagefolder = igk_getr("packagedir", $packagefolder);
			}
		}
		if ($uri_demand = igk_is_uri_demand($this)) {
			$this->setEnvParam("replaceuri", 1);
		}
		if (empty($folder)) {
			return false;
		}
		$listen = igk_getr("listen");
		$environment = igk_getr("environment", "development");
		InstallSite::Install($folder, $packagefolder, $listen, $environment);
		igk_notifyctrl("installsite")->addSuccessr("Install site success");
	}
    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct();
	}
    /**
    * Sets Config.
    * @param mixed $c
    */
    public function setConfig($c)
	{
	}
    /**
    * Returns Config Page.
    */
    public function getConfigPage()
	{
		return "installsite";
	}
    /**
    * Returns Config Group.
    */
    public function getConfigGroup()
	{
		return "administration";
	}
    /**
    * Returns Is Config Page Available.
    */
    public function getIsConfigPageAvailable()
	{
		return !igk_io_is_subdir(igk_io_applicationdir(), IGK_LIB_DIR);
	}
    /**
    * Initializes Config Menu.
    */
    public function initConfigMenu()
	{
		return [
			new MenuItem(
				"installsite",
				__("install site"),
				$this->getUri("showConfig"),
				30, null, "administration"
			)
		];
	}
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController
	{
		$t = $this->getTargetNode();
		$t->clearChilds();
		if (!$this->getIsConfigPageAvailable())
			return $this;
		if ($this->getEnvParam("replaceuri"))
			$t->addReplaceUri($this->getUri("ShowConfig"));
		$c = $t->addPanelBox();
		$c->addSectionTitle(4)->Content = __("Install Site");
		$c->addNotifyHost("installsite");
		$form = $c->addForm();
		$form["method"] = "POST";
		$form["action"] = $this->getUri("install");
		$form->addFields(
			[
				"rootdir" => ["attribs" => ["class" => "igk-form-control required", "placeholder" => __("Install site folder. use full path")]],
				"packagedir" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("Custom package folder")]],
				"listen" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("port")]],
				"environment" => ["attribs" => ["class" => "igk-form-control", "placeholder" => __("environment")]]
			]
		);
		igk_html_form_initfield($form);
		$div = $form->div();
		$div->addP()->Content = __("TIPS");
		$div->article($this, "help.installer.tips");
		$_ac_bar = $form->addActionBar();
		$_ac_bar->addInput("btn.send", "submit", __("Install"));
		return $this;
	}
}