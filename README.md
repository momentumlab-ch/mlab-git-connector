# MLab Git Connector

WordPress-Plugin, das Plugins und Themes direkt über eine GitHub-URL installiert. Updates laufen danach über den Standard-Updater von WordPress.

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
- **Updates:** Plugin Update Checker v5, kein eigener Update-Code, keine Cronjobs.
- **Vorbild:** Verhalten wie WP Pusher (Installation per GitHub-URL), aber auf Plugin Update Checker aufgebaut.
- **Sicherheit:** Nonce und `install_plugins`-Recht bei jeder Aktion, alle Eingaben werden bereinigt, alle Ausgaben escaped.
