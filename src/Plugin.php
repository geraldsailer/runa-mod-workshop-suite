<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite;

use Runa\WorkshopSuite\Admin\Menu;
use Runa\WorkshopSuite\Admin\CodesQuotaColumn;
use Runa\WorkshopSuite\Admin\DashboardPage;
use Runa\WorkshopSuite\Auth\LandingPage;
use Runa\WorkshopSuite\Auth\RegisterHandler;
use Runa\WorkshopSuite\Auth\LoginHandler;
use Runa\WorkshopSuite\Auth\AdminAccessGuard;
use Runa\WorkshopSuite\Auth\Roles;
use Runa\WorkshopSuite\Auth\Assets;
use Runa\WorkshopSuite\Auth\FrontendGate;

/**
 * Core plugin bootstrapper.
 */
final class Plugin {
	private static ?self $instance = null;

	private Menu $menu;
	private DashboardPage $dashboardPage;
	private CodesQuotaColumn $codesQuotaColumn;
	private LandingPage $landingPage;
	private RegisterHandler $registerHandler;
	private LoginHandler $loginHandler;
	private AdminAccessGuard $adminAccessGuard;
	private FrontendGate $frontendGate;
	private Roles $roles;
	private Assets $authAssets;

	private function __construct() {
		$pluginDir = dirname(__DIR__);
		$pluginUrl = trailingslashit(plugins_url('', RUNA_WSS_PLUGIN_FILE));

		$this->dashboardPage = new DashboardPage($pluginDir . '/templates/admin/dashboard.php');
		$this->menu = new Menu($this->dashboardPage);
		$this->codesQuotaColumn = new CodesQuotaColumn();
		$this->landingPage = new LandingPage($pluginDir . '/templates/auth/login.php');
		$this->registerHandler = new RegisterHandler();
		$this->loginHandler = new LoginHandler();
		$defaultRedirect = LoginHandler::defaultRedirectUrl();
		$this->adminAccessGuard = new AdminAccessGuard($defaultRedirect);
		$this->frontendGate = new FrontendGate($defaultRedirect);
		$this->roles = new Roles();
		$this->authAssets = new Assets($pluginDir, $pluginUrl);
	}

	public static function instance(): self {
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function boot(): void {
		add_action('admin_menu', [$this->menu, 'register']);
		add_action('admin_menu', [$this->menu, 'cleanup'], 999);
		add_action('admin_enqueue_scripts', [$this->menu, 'enqueueAssets']);
		$this->codesQuotaColumn->register();
		$this->landingPage->register();
		$this->registerHandler->register();
		$this->loginHandler->register();
		$this->adminAccessGuard->register();
		$this->frontendGate->register();
		$this->roles->register();
		$this->authAssets->register();
	}
}
