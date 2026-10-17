# MomentumLab Git Connector

WordPress-Plugin, das Plugins und Themes direkt über eine GitHub-URL installiert. Updates laufen danach über den Standard-Updater von WordPress.

## Status

Version 1.0.0. Installation, Update-Prüfung und Release-Workflow sind umgesetzt. Ein Test auf einer echten WordPress-Site steht noch aus.

## Nutzung

1. Plugin installieren und aktivieren.
2. Im Backend *Plugins → Aus GitHub installieren* öffnen.
3. Die GitHub-Adresse eines Repos eingeben, z. B. `https://github.com/momentumlab-ch/mlab-forms`.
4. Das Plugin lädt das ZIP des neuesten Releases und installiert es. Die Aktivierung erfolgt danach auf der Plugin-Seite.

Das Repo braucht ein Release mit einer ZIP-Datei, die `<slug>.zip` heißt und den Ordner `<slug>/` oben enthält. Der automatische GitHub-Quellcode wird nicht installiert.

## Festgelegte Werte

| Bereich | Wert |
|---|---|
| Plugin-Slug (Ordner im ZIP) | `mlab-git-connector` |
| Funktions- und Klassen-Präfix | `mlab_git_connector_` / `Mlab_Git_Connector_` |
| Konstanten-Präfix | `MLAB_GIT_CONNECTOR_` |
| Token-Konstante für private Repos | `MLAB_GIT_CONNECTOR_GITHUB_TOKEN` (in `wp-config.php`) |
| Text Domain | `mlab-git-connector` |
| Update-Quelle | GitHub Releases dieses Repos, über Plugin Update Checker |

## Entscheidungen

- **Installation:** Nur Hosts `github.com` werden akzeptiert. Installiert wird das Release-ZIP `<slug>.zip`, nie das automatische Quellcode-ZIP.
- **Updates:** Plugin Update Checker v5, kein eigener Update-Code, keine Cronjobs. Geprüft wird alle 12 Stunden.
- **Private Repos:** Das Repo `mlab-git-connector` ist öffentlich, deshalb braucht die Update-Prüfung keinen Token (`private = false` in `inc/updater.php`). Für ein privates Repo auf `true` setzen und den Token in `wp-config.php` eintragen.
- **Vorbild:** Verhalten wie WP Pusher (Installation per GitHub-URL), aber auf Plugin Update Checker aufgebaut.
- **Sicherheit:** Nonce und `install_plugins`-Recht bei jeder Aktion, alle Eingaben werden bereinigt, alle Ausgaben escaped.

## Release

1. Version in Header, Konstante `MLAB_GIT_CONNECTOR_VERSION` und `Stable tag` in `readme.txt` erhöhen. Changelog ergänzen.
2. Nach dem Merge auf `main` den Tag `vX.Y.Z` setzen und pushen.
3. Die GitHub Action `.github/workflows/release.yml` baut `mlab-git-connector.zip` und hängt es an das Release. Sie prüft Ordnername, Versionen und Konfigurationsdateien.

## Prüfungen

- PHPCS mit den WordPress-Standards (`phpcs.xml.dist`): ohne Befund.
- PHP-Syntax aller Dateien: ohne Befund.
