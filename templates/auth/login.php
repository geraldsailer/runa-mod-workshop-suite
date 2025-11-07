<?php
/**
 * Programmatic login / registration landing page.
 *
 * @package RunaModWorkshopSuite
 */

use Runa\WorkshopSuite\Auth\RegisterHandler;
use Runa\WorkshopSuite\Auth\LoginHandler;

defined('ABSPATH') || exit;

$notices = [];
$active_tab = 'login';
$registration_status = isset($_GET['registration']) ? sanitize_key(wp_unslash($_GET['registration'])) : '';
$login_status = isset($_GET['login']) ? sanitize_key(wp_unslash($_GET['login'])) : '';
$login_redirect = LoginHandler::defaultRedirectUrl();
$registration_redirect = home_url('/');
$login_endpoint = add_query_arg('action', LoginHandler::ACTION, admin_url('admin-post.php'));
$registration_endpoint = add_query_arg('action', RegisterHandler::ACTION, admin_url('admin-post.php'));

if ($registration_status) {
	$messages = [
		'success' => __('Registrierung erfolgreich – du kannst dich jetzt anmelden.', 'runa-mod-workshop-suite'),
		'missing' => __('Bitte fülle alle Felder aus.', 'runa-mod-workshop-suite'),
		'invalid' => __('Deine Sitzung ist abgelaufen. Versuche es bitte erneut.', 'runa-mod-workshop-suite'),
		'email'   => __('Die eingegebene E-Mail-Adresse ist ungültig.', 'runa-mod-workshop-suite'),
		'exists'  => __('Es existiert bereits ein Account mit dieser E-Mail-Adresse.', 'runa-mod-workshop-suite'),
		'error'   => __('Ups, da ging etwas schief. Bitte später erneut versuchen.', 'runa-mod-workshop-suite'),
	];

	$type = 'success' === $registration_status ? 'success' : 'error';
	$notices[] = [
		'type'    => $type,
		'message' => $messages[ $registration_status ] ?? $messages['error'],
	];

	if ('success' !== $registration_status) {
		$active_tab = 'register';
	}
}

if ($login_status) {
	$messages = [
		'success'   => __('Login erfolgreich. Du wirst gleich weitergeleitet.', 'runa-mod-workshop-suite'),
		'missing'   => __('Bitte gib eine gültige E-Mail-Adresse und dein Passwort ein.', 'runa-mod-workshop-suite'),
		'invalid'   => __('Deine Sitzung ist abgelaufen. Versuche es bitte erneut.', 'runa-mod-workshop-suite'),
		'not_found' => __('Es wurde kein Nutzer mit dieser E-Mail gefunden.', 'runa-mod-workshop-suite'),
		'failed'    => __('Falsches Passwort. Bitte versuche es erneut.', 'runa-mod-workshop-suite'),
	];

	$type = 'success' === $login_status ? 'success' : 'error';
	$notices[] = [
		'type'    => $type,
		'message' => $messages[ $login_status ] ?? __('Login fehlgeschlagen.', 'runa-mod-workshop-suite'),
	];

	if ('success' !== $login_status) {
		$active_tab = 'login';
	}
}

get_header();
?>

<main class="runa-auth" role="main">
	<div class="runa-auth__shell">
		<section class="runa-auth__hero">
			<div class="runa-auth__hero-content">
				<h1><?php esc_html_e('Willkommen zur Workshop Suite', 'runa-mod-workshop-suite'); ?></h1>
				<p><?php esc_html_e('Melde dich an oder registriere dich, um deine Module, Kurse und Zugänge zentral zu verwalten.', 'runa-mod-workshop-suite'); ?></p>
			</div>
		</section>

		<section class="runa-auth__panel" aria-live="polite">
			<div class="runa-auth__tabs" role="tablist">
				<button class="runa-auth__tab" role="tab" aria-selected="<?php echo 'login' === $active_tab ? 'true' : 'false'; ?>" data-target="login">
					<?php esc_html_e('Login', 'runa-mod-workshop-suite'); ?>
				</button>
				<button class="runa-auth__tab" role="tab" aria-selected="<?php echo 'register' === $active_tab ? 'true' : 'false'; ?>" data-target="register">
					<?php esc_html_e('Registrieren', 'runa-mod-workshop-suite'); ?>
				</button>
			</div>

			<?php foreach ($notices as $notice) : ?>
				<div class="runa-auth__notice runa-auth__notice--<?php echo esc_attr($notice['type']); ?>">
					<span><?php echo 'success' === $notice['type'] ? '✅' : '⚠️'; ?></span>
					<span><?php echo esc_html($notice['message']); ?></span>
				</div>
			<?php endforeach; ?>

			<div class="runa-auth__sso" aria-label="<?php esc_attr_e('Single Sign-On Optionen', 'runa-mod-workshop-suite'); ?>">
				<button type="button" class="runa-auth__sso-btn" data-provider="microsoft">
					<span>🔒</span>
					<span><?php esc_html_e('Weiter mit Microsoft', 'runa-mod-workshop-suite'); ?></span>
				</button>
				<button type="button" class="runa-auth__sso-btn" data-provider="google">
					<span>🔐</span>
					<span><?php esc_html_e('Weiter mit Google', 'runa-mod-workshop-suite'); ?></span>
				</button>
			</div>

			<div class="runa-auth__separator">
				<?php esc_html_e('oder per E-Mail', 'runa-mod-workshop-suite'); ?>
			</div>

			<form class="runa-auth__form <?php echo 'login' === $active_tab ? 'is-active' : ''; ?>" id="runa-auth-login" data-form="login" method="post" action="<?php echo esc_url($login_endpoint); ?>">
				<?php wp_nonce_field('runa_wss_login', '_runa_wss_login_nonce'); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr(LoginHandler::ACTION); ?>" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_url($login_redirect); ?>" />
				<div class="runa-auth__field">
					<label for="runa-login-email"><?php esc_html_e('E-Mail-Adresse', 'runa-mod-workshop-suite'); ?></label>
					<input type="email" id="runa-login-email" name="email" required autocomplete="email" />
				</div>
				<div class="runa-auth__field">
					<label for="runa-login-password"><?php esc_html_e('Passwort', 'runa-mod-workshop-suite'); ?></label>
					<input type="password" id="runa-login-password" name="password" required autocomplete="current-password" />
				</div>
				<button class="runa-auth__submit" type="submit">
					<?php esc_html_e('Anmelden', 'runa-mod-workshop-suite'); ?>
				</button>
			</form>

			<form class="runa-auth__form <?php echo 'register' === $active_tab ? 'is-active' : ''; ?>" id="runa-auth-register" data-form="register" method="post" action="<?php echo esc_url($registration_endpoint); ?>">
				<?php wp_nonce_field('runa_wss_register', '_runa_wss_register_nonce'); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr(RegisterHandler::ACTION); ?>" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_url($registration_redirect); ?>" />
				<div class="runa-auth__field">
					<label for="runa-register-name"><?php esc_html_e('Vollständiger Name', 'runa-mod-workshop-suite'); ?></label>
					<input type="text" id="runa-register-name" name="name" required autocomplete="name" />
				</div>
				<div class="runa-auth__field">
					<label for="runa-register-email"><?php esc_html_e('E-Mail-Adresse', 'runa-mod-workshop-suite'); ?></label>
					<input type="email" id="runa-register-email" name="email" required autocomplete="email" />
				</div>
				<div class="runa-auth__field">
					<label for="runa-register-password"><?php esc_html_e('Passwort', 'runa-mod-workshop-suite'); ?></label>
					<input type="password" id="runa-register-password" name="password" required autocomplete="new-password" />
				</div>
				<button class="runa-auth__submit" type="submit">
					<?php esc_html_e('Account erstellen', 'runa-mod-workshop-suite'); ?>
				</button>
			</form>

			<p class="runa-auth__footnote">
				<?php esc_html_e('SSO-Schaltflächen sind aktuell noch Platzhalter – hier binden wir später Microsoft & Google ein.', 'runa-mod-workshop-suite'); ?>
			</p>
		</section>
	</div>
</main>

<script>
	(() => {
		const tabs = document.querySelectorAll('.runa-auth__tab');
		const forms = document.querySelectorAll('.runa-auth__form');

		tabs.forEach((tab) => {
			tab.addEventListener('click', () => {
				const target = tab.getAttribute('data-target');

				tabs.forEach((btn) => btn.setAttribute('aria-selected', String(btn === tab)));
				forms.forEach((form) => {
					form.classList.toggle('is-active', form.dataset.form === target);
				});
			});
		});
	})();
</script>

<?php
get_footer();
