<?php

use Runa\WorkshopSuite\Auth\Roles;

defined('ABSPATH') || exit;

$navGroups = $navGroups ?? [];
$activeTab = $activeTab ?? 'dashboard';
$pluginVersion = $pluginVersion ?? '';
$participants = $participants ?? [];

$baseUrl = admin_url('admin.php?page=runa-workshop-suite');
?>
<div class="wrap wh-manager-wrapper">
	<aside class="wh-manager-sidebar" aria-label="<?php esc_attr_e('Workshop Suite Navigation', 'runa-mod-workshop-suite'); ?>">
		<div class="manager-version-infotext">
			<span class="title"><?php esc_html_e('Workshop Suite', 'runa-mod-workshop-suite'); ?></span>
			<?php if ($pluginVersion) : ?>
				<span class="version"><?php echo esc_html(sprintf('v%s', $pluginVersion)); ?></span>
			<?php endif; ?>
		</div>
		<?php foreach ($navGroups as $groupLabel => $links) : ?>
			<ul>
				<li class="section-title"><?php echo esc_html($groupLabel); ?></li>
				<?php foreach ($links as $link) : ?>
					<?php
					$slug   = $link['slug'];
					$active = $slug === $activeTab ? 'active' : '';
					$url    = esc_url(add_query_arg('tab', $slug, $baseUrl));
					?>
					<li>
						<a class="wh-link <?php echo esc_attr($active); ?>" href="<?php echo $url; ?>">
							<span class="dashicons <?php echo esc_attr($link['icon']); ?>" aria-hidden="true"></span>
							<span><?php echo esc_html($link['label']); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</aside>

	<div class="wh-manager-content">
		<?php if ($activeTab === 'users') : ?>
			<div class="hub-admin-titlebar">
				<div class="hub-admin-header">
					<div class="hub-admin-header--title">
						<h1><?php esc_html_e('Benutzerverwaltung /', 'runa-mod-workshop-suite'); ?> <span><?php esc_html_e('Benutzer', 'runa-mod-workshop-suite'); ?></span></h1>
						<p><?php esc_html_e('Alle Teilnehmer mit Zugriff auf die Workshop Suite.', 'runa-mod-workshop-suite'); ?></p>
					</div>
				</div>
			</div>

			<div class="hub-admin-body">
				<section class="hub-admin-panel hub-flexbox">
					<div class="hub-admin-panel--titlebar">
						<h2>
							<?php esc_html_e('Workshop Teilnehmer', 'runa-mod-workshop-suite'); ?>
							<span class="badge"><?php echo esc_html((string) count($participants)); ?></span>
						</h2>
					</div>
					<div class="hub-admin-panel--content">
						<?php if (empty($participants)) : ?>
							<p><?php esc_html_e('Es wurden noch keine Teilnehmer registriert.', 'runa-mod-workshop-suite'); ?></p>
						<?php else : ?>
							<table class="widefat striped">
								<thead>
									<tr>
										<th><?php esc_html_e('Vorname', 'runa-mod-workshop-suite'); ?></th>
										<th><?php esc_html_e('Nachname', 'runa-mod-workshop-suite'); ?></th>
										<th><?php esc_html_e('E-Mail', 'runa-mod-workshop-suite'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($participants as $participant) : ?>
										<tr>
											<td><?php echo esc_html($participant['first_name']); ?></td>
											<td><?php echo esc_html($participant['last_name']); ?></td>
											<td>
												<a href="mailto:<?php echo esc_attr($participant['email']); ?>">
													<?php echo esc_html($participant['email']); ?>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</section>
			</div>
		<?php else : ?>
			<div class="hub-admin-titlebar">
				<div class="hub-admin-header">
					<div class="hub-admin-header--title">
						<h1><?php esc_html_e('Start /', 'runa-mod-workshop-suite'); ?> <span><?php esc_html_e('Dashboard', 'runa-mod-workshop-suite'); ?></span></h1>
						<p><?php esc_html_e('Zentrale Übersicht für alle Workshop Suite Module.', 'runa-mod-workshop-suite'); ?></p>
					</div>
				</div>
			</div>

			<div class="hub-admin-body">
				<section class="hub-admin-panel hub-flexbox">
					<div class="hub-admin-panel--titlebar">
						<h2><?php esc_html_e('Schnellzugriff', 'runa-mod-workshop-suite'); ?></h2>
					</div>
					<div class="hub-admin-panel--content">
						<div class="wss-quick-links">
							<a class="wss-quick-links__item" href="<?php echo esc_url(admin_url('admin.php?page=pods-manage-rna_wss_code')); ?>">
								<span class="dashicons dashicons-tickets-alt" aria-hidden="true"></span>
								<strong><?php esc_html_e('Codes', 'runa-mod-workshop-suite'); ?></strong>
								<span><?php esc_html_e('Verwalte Zugangscodes', 'runa-mod-workshop-suite'); ?></span>
							</a>
							<a class="wss-quick-links__item" href="<?php echo esc_url(admin_url('edit.php?post_type=rna_ws_suite_course')); ?>">
								<span class="dashicons dashicons-welcome-learn-more" aria-hidden="true"></span>
								<strong><?php esc_html_e('Kurse', 'runa-mod-workshop-suite'); ?></strong>
								<span><?php esc_html_e('Bearbeite Kursinhalte', 'runa-mod-workshop-suite'); ?></span>
							</a>
							<a class="wss-quick-links__item" href="<?php echo esc_url(admin_url('edit.php?post_type=rna_ws_suite_lektion')); ?>">
								<span class="dashicons dashicons-book" aria-hidden="true"></span>
								<strong><?php esc_html_e('Lektionen', 'runa-mod-workshop-suite'); ?></strong>
								<span><?php esc_html_e('Organisiere Lektionen', 'runa-mod-workshop-suite'); ?></span>
							</a>
						</div>
					</div>
				</section>

				<section class="hub-admin-panel hub-flexbox">
					<div class="hub-admin-panel--titlebar">
						<h2><?php esc_html_e('Benutzerrolle', 'runa-mod-workshop-suite'); ?></h2>
					</div>
					<div class="hub-admin-panel--content">
						<p>
							<?php
							printf(
								/* translators: %s role name. */
								esc_html__('Neu registrierte Teilnehmer erhalten automatisch die Rolle %s mit Leserechten und ohne Backend-Zugriff.', 'runa-mod-workshop-suite'),
								'<code>' . esc_html(Roles::ROLE_PARTICIPANT) . '</code>'
							);
							?>
						</p>
						<p><?php esc_html_e('Passe die Rolle bei Bedarf in den WordPress Benutzerrollen an.', 'runa-mod-workshop-suite'); ?></p>
					</div>
				</section>
			</div>
		<?php endif; ?>
	</div>
</div>
