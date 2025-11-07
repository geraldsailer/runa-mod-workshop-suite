<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Handles enqueueing assets for the Auth experience.
 */
final class Assets {
	private string $cssPath;
	private string $cssUrl;

	public function __construct(string $pluginDir, string $pluginUrl) {
		$this->cssPath = $pluginDir . '/assets/css/login.css';
		$this->cssUrl  = $pluginUrl . 'assets/css/login.css';
	}

	public function register(): void {
		add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
	}

	public function enqueueStyles(): void {
		if (is_admin() || ! function_exists('is_front_page') || ! is_front_page()) {
			return;
		}

		if (! file_exists($this->cssPath)) {
			return;
		}

		$version = (string) filemtime($this->cssPath);

		wp_enqueue_style(
			'runa-wss-auth',
			$this->cssUrl,
			[],
			$version
		);
	}
}
