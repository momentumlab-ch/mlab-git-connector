# Changelog

## 1.1.0

- Alle Texte sind übersetzbar. Die Quellsprache ist Englisch, die Sprachdatei `de_CH` liegt bei.
- Feste Werte wie Timeouts stehen zentral in `inc/options.php` und lassen sich über den Filter `mlab_git_connector_options` anpassen.
- Die Release-Action erstellt das Release automatisch, sobald eine neue Version auf `main` liegt.
- Der Hinweis bei fehlendem Token ist jetzt ebenfalls übersetzbar.

## 1.0.1

- Die Installation läuft auch dann zu Ende, wenn der Server einen Timeout (504) anzeigt. Das Ergebnis erscheint nach dem Neuladen der Seite.
- Die Abfrage des Releases bricht nach 20 Sekunden ab, statt lange zu hängen.

## 1.0.0

- Erste Version: Installation aus einem GitHub-Release, Updates über den WordPress-Updater, Unterstützung für private Repositories über einen Token in wp-config.php.
