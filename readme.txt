=== MomentumLab Git Connector ===
Contributors: momentumlab
Tags: github, install, plugins, updates
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Install WordPress plugins directly from a GitHub repository, and receive their updates.

== Description ==

Enter the GitHub address of a repository. The plugin ZIP attached to its latest release is installed through the standard WordPress plugin installer. Updates come through the normal WordPress update screen.

Private repositories work with a read-only GitHub token that is defined in wp-config.php:

`define( 'MLAB_GIT_CONNECTOR_GITHUB_TOKEN', 'github_pat_...' );`

== Filters ==

`mlab_git_connector_options` changes the fixed values (lookup_timeout, download_timeout, time_limit, notice_ttl, asset_name).

== Installation ==

1. Upload the plugin under Plugins > Add New > Upload Plugin, or install it from its GitHub release.
2. Activate the plugin.
3. Open Plugins > Install from GitHub and enter the repository address.

== Changelog ==

= 1.1.0 =
* Alle Texte sind übersetzbar. Die Quellsprache ist Englisch, die Sprachdatei de_CH liegt bei.
* Feste Werte wie Timeouts stehen zentral in inc/options.php und lassen sich über den Filter mlab_git_connector_options anpassen.
* Die Release-Action erstellt das Release automatisch, sobald eine neue Version auf main liegt.

= 1.0.1 =
* Die Installation läuft auch dann zu Ende, wenn der Server einen Timeout (504) anzeigt. Das Ergebnis erscheint nach dem Neuladen der Seite.
* Die Abfrage des Releases bricht nach 20 Sekunden ab, statt lange zu hängen.

= 1.0.0 =
* Erste Version: Installation aus einem GitHub-Release, Updates über den WordPress-Updater, Unterstützung für private Repositories über einen Token in wp-config.php.
