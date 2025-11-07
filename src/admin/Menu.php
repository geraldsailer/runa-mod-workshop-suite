<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Admin;

/**
 * Handles the Workshop Suite admin menu structure.
 */
final class Menu {
	private const PARENT_SLUG = 'runa-workshop-suite';
	private DashboardPage $dashboardPage;

	public function __construct(DashboardPage $dashboardPage) {
		$this->dashboardPage = $dashboardPage;
	}

	/**
	 * Registers the parent menu and links to the Pods-managed post types.
	 */
	public function register(): void {
		if (! function_exists('add_menu_page')) {
			return;
		}

		add_menu_page(
			__('Workshop Suite', 'runa-mod-workshop-suite'),
			__('Workshop Suite', 'runa-mod-workshop-suite'),
			'edit_posts',
			self::PARENT_SLUG,
			[$this, 'render_dashboard'],
			'dashicons-welcome-learn-more',
			25
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Dashboard', 'runa-mod-workshop-suite'),
			__('Dashboard', 'runa-mod-workshop-suite'),
			'edit_posts',
			self::PARENT_SLUG,
			[$this, 'render_dashboard']
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Codes', 'runa-mod-workshop-suite'),
			__('Codes', 'runa-mod-workshop-suite'),
			'edit_posts',
			'admin.php?page=pods-manage-rna_wss_code'
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Kurse', 'runa-mod-workshop-suite'),
			__('Kurse', 'runa-mod-workshop-suite'),
			'edit_posts',
			'edit.php?post_type=rna_ws_suite_course'
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Lektionen', 'runa-mod-workshop-suite'),
			__('Lektionen', 'runa-mod-workshop-suite'),
			'edit_posts',
			'edit.php?post_type=rna_ws_suite_lektion'
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Module', 'runa-mod-workshop-suite'),
			__('Module', 'runa-mod-workshop-suite'),
			'edit_posts',
			'edit.php?post_type=rna_ws_suite_modul'
		);

		add_submenu_page(
			self::PARENT_SLUG,
			__('Lektion-Zielgruppen', 'runa-mod-workshop-suite'),
			__('Lektion-Zielgruppen', 'runa-mod-workshop-suite'),
			'edit_posts',
			'edit-tags.php?taxonomy=zielgruppe'
		);
	}

	/**
	 * Removes the original top-level menu entries added by Pods.
	 */
	public function cleanup(): void {
		remove_menu_page('edit.php?post_type=rna_wss_code');
		remove_menu_page('pods-manage-rna_wss_code');
		remove_menu_page('edit.php?post_type=rna_ws_suite_course');
		remove_menu_page('edit.php?post_type=rna_ws_suite_lektion');
		remove_menu_page('edit.php?post_type=rna_ws_suite_modul');
	}

	/**
	 * Simple placeholder for the parent menu landing page.
	 */
	public function render_dashboard(): void {
		if (! current_user_can('edit_posts')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'runa-mod-workshop-suite'));
		}

		$this->dashboardPage->render();
	}

	public function enqueueAssets(string $hook): void {
		if ($hook !== 'toplevel_page_' . self::PARENT_SLUG) {
			return;
		}

		if (! defined('RUNA_WSS_PLUGIN_FILE')) {
			return;
		}

		$cssPath = plugin_dir_path(RUNA_WSS_PLUGIN_FILE) . 'assets/css/admin-dashboard.css';

		if (! file_exists($cssPath)) {
			return;
		}

		wp_enqueue_style(
			'runa-wss-admin',
			plugins_url('assets/css/admin-dashboard.css', RUNA_WSS_PLUGIN_FILE),
			[],
			(string) filemtime($cssPath)
		);
	}
}
