<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite;

use Runa\WorkshopSuite\Admin\Menu;
use Runa\WorkshopSuite\Admin\CodesQuotaColumn;

/**
 * Core plugin bootstrapper.
 */
final class Plugin {
	private static ?self $instance = null;

	private Menu $menu;
	private CodesQuotaColumn $codesQuotaColumn;

	private function __construct() {
		$this->menu = new Menu();
		$this->codesQuotaColumn = new CodesQuotaColumn();
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
		$this->codesQuotaColumn->register();
	}
}
