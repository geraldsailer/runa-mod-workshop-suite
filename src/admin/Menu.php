<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Admin;

/**
 * Handles the Workshop Suite admin menu structure.
 */
final class Menu {
	private const PARENT_SLUG = 'runa-workshop-suite';

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

		// Remove the redundant auto-added submenu that duplicates the parent slug.
		remove_submenu_page(self::PARENT_SLUG, self::PARENT_SLUG);
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

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Workshop Suite', 'runa-mod-workshop-suite') . '</h1>';
		echo '<p>' . esc_html__('Verwalte hier alle Inhalte der Workshop Suite über die Unterpunkte im Menü.', 'runa-mod-workshop-suite') . '</p>';
		echo '</div>';
	}
}
