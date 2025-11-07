<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Handles custom roles for Workshop Suite participants.
 */
final class Roles {
	public const ROLE_PARTICIPANT = 'rna_workshop_participant';

	public function register(): void {
		add_action('init', [$this, 'ensureRole']);
		register_activation_hook(RUNA_WSS_PLUGIN_FILE, [$this, 'ensureRole']);
		register_deactivation_hook(RUNA_WSS_PLUGIN_FILE, [$this, 'removeRole']);
	}

	public function ensureRole(): void {
		if (get_role(self::ROLE_PARTICIPANT)) {
			return;
		}

		add_role(
			self::ROLE_PARTICIPANT,
			__('Workshop Teilnehmer', 'runa-mod-workshop-suite'),
			[
				'read'         => true,
				'upload_files' => false,
			]
		);
	}

	public function removeRole(): void {
		remove_role(self::ROLE_PARTICIPANT);
	}
}
