# Changelog

All notable changes to this project will be documented in this file. The format roughly follows [Keep a Changelog](https://keepachangelog.com/) and adheres to [Semantic Versioning](https://semver.org/).

## [0.1.0] - 2025-11-07

### Added
- Initial plugin bootstrap with PSR-4 style autoloader.
- Workshop Suite admin menu integration, including submenu links for Codes, Kurse, Lektionen, Module und Zielgruppen.
- Cleanup routine that removes duplicate Pods menu entries.
- Custom Pods admin column exposing the `rna_wss_codes_quota` (“Kontingent”) field for the `rna_wss_code` pod.
- Programmatic front-page template with functional email login & registration plus placeholder Microsoft/Google SSO buttons.
- Admin access guard that redirects Frontend-User unmittelbar nach `/dashboard/` und blockiert wp-admin Zugriffe ohne passende Rechte.
- Automatische Rolle `rna_workshop_participant` für neue Registrierungen, inkl. Activation-/Deactivation-Hooks.
- Frontend-Zugriffsschutz, der nicht eingeloggte Besucher konsequent auf die Login-Startseite umleitet.
- RUNA-Hub-inspiriertes Backend-Dashboard samt Navigation & Teilnehmerliste (mit eigenem Admin-CSS).
- Auth-Modul ausgelagert unter `src/Auth` inkl. Template-Ordnerstruktur sowie ausgelagertes Styling (`assets/scss/login.scss` → `assets/css/login.css`).
- Project documentation (`README.md`) and this changelog.
