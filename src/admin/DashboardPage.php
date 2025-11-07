<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Admin;

use Runa\WorkshopSuite\Auth\Roles;

/**
 * Renders the backend dashboard experience for the Workshop Suite.
 */
final class DashboardPage {
	private string $template;

	/**
	 * @param string $template Absolute path to the dashboard template.
	 */
	public function __construct(string $template) {
		$this->template = $template;
	}

	public function render(): void {
		$navGroups = $this->navigation();
		$activeTab = $this->determineActiveTab($navGroups);
		$pluginVersion = $this->pluginVersion();
		$participants = $activeTab === 'users' ? $this->fetchParticipants() : [];

		$template = $this->template;
		$navGroupsVar = $navGroups;
		$activeTabVar = $activeTab;
		$pluginVersionVar = $pluginVersion;
		$participantsVar = $participants;

		if (file_exists($template)) {
			$navGroups = $navGroupsVar;
			$activeTab = $activeTabVar;
			$pluginVersion = $pluginVersionVar;
			$participants = $participantsVar;
			include $template;
		}
	}

	/**
	 * @return array<string, array<int, array<string, string>>>
	 */
	private function navigation(): array {
		return [
			__('Start', 'runa-mod-workshop-suite') => [
				[
					'slug'  => 'dashboard',
					'label' => __('Dashboard', 'runa-mod-workshop-suite'),
					'icon'  => 'dashicons-admin-home',
				],
			],
			__('Benutzerverwaltung', 'runa-mod-workshop-suite') => [
				[
					'slug'  => 'users',
					'label' => __('Benutzer', 'runa-mod-workshop-suite'),
					'icon'  => 'dashicons-groups',
				],
			],
		];
	}

	/**
	 * @param array<string, array<int, array<string, string>>> $nav
	 */
	private function determineActiveTab(array $nav): string {
		$requested = isset($_GET['tab']) ? sanitize_key((string) wp_unslash($_GET['tab'])) : 'dashboard';
		$allowed = ['dashboard'];

		foreach ($nav as $links) {
			foreach ($links as $link) {
				$allowed[] = $link['slug'];
			}
		}

		if (! in_array($requested, $allowed, true)) {
			return 'dashboard';
		}

		return $requested;
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function fetchParticipants(): array {
		$users = get_users([
			'role'    => Roles::ROLE_PARTICIPANT,
			'orderby' => 'display_name',
			'order'   => 'ASC',
		]);

		return array_map(
			static function (\WP_User $user): array {
				$firstName = get_user_meta($user->ID, 'first_name', true);
				$lastName  = get_user_meta($user->ID, 'last_name', true);

				return [
					'first_name' => $firstName ?: '',
					'last_name'  => $lastName ?: '',
					'email'      => $user->user_email,
				];
			},
			$users
		);
	}

	private function pluginVersion(): string {
		if (! defined('RUNA_WSS_PLUGIN_FILE')) {
			return '';
		}

		$data = get_file_data(
			RUNA_WSS_PLUGIN_FILE,
			[
				'Version' => 'Version',
			]
		);

		return (string) ($data['Version'] ?? '');
	}
}
