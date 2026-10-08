# ST Template Control

**English** | [Português (Brasil)](README.pt-BR.md)

**Controls persistent template bindings in GLPI, allowing native template selection based on category, active profile, and entity.**

<img width="2046" height="875" alt="image" src="https://github.com/user-attachments/assets/88dc16f4-6d5f-44aa-86d6-d6ab3dc4091b" />

**ST Template Control** prevents a template saved on a ticket, change or problem from remaining forced during display simply because it is linked to the record. It lets GLPI select the template again using the category, active profile and entity, following GLPI 10-style behaviour.

**Version: 2.0.0.**

**Authors: Service TIC (fork) / Curtis Conard**

This package adopts the ST Template Control identity and description, with no change to the template-handling logic or configuration defaults of the previous fork package.

> A **Service TIC** fork/adaptation based on [Unstick ITIL Templates](https://github.com/cconard96/glpi-unsticktemplates-plugin), by **Curtis Conard**. Original project attribution and the GPL-3.0-or-later licence are retained.

## What problem does it address?

In GLPI 11, a template used during creation can remain linked to the record and continue influencing its display. Fields hidden by a self-service template may therefore remain hidden when a technician opens the same ticket. This scenario is described by the [upstream project](https://github.com/cconard96/glpi-unsticktemplates-plugin).

This plugin removes that saved link from the forced-template position during normal display. It **does not indiscriminately reveal all fields**: the template selected from the current context still determines the form presentation.

**Example:** a requester uses a simplified template, while the technician has another template in their active profile. When there is no higher-priority category template, the plugin allows the display to use the technician's profile template instead of remaining tied to the template saved at creation.

## Features

| Feature | Behaviour |
|---|---|
| Tickets, changes and problems | Independently enabled for each item type. |
| Existing records | Neutralises the persisted template during normal display, without a database write merely from opening the page. |
| New records | Removes the persistent template identifier before insertion; does not undo values already applied during creation. |
| Updates | Removes incoming template-link fields and offers template-link cleanup after an update. |
| Template previews | Preserves an explicitly requested preview. |
| Administration | Configuration screen, optional debug logging and translated interface. |

The plugin uses GLPI extension hooks; **no GLPI core file edits are required**.

## How is the template selected?

```text
Explicitly requested preview
              ↓
Category / item type
              ↓
Active profile of the signed-in user
              ↓
Entity
              ↓
Native GLPI fallback
```

This is a summary of native resolution. Internal rules involving type, category, predefined values and entity configuration remain handled by GLPI. See the [selection method in GLPI 11.0.11](https://github.com/glpi-project/glpi/blob/11.0.11/src/CommonITILObject.php#L7787-L7907).

**There is no configurable custom precedence order.** The plugin does not create a separate template source on the user record: “active profile” means the signed-in user's session profile, not necessarily the ticket requester's profile.

The name **Template Control** refers to controlling persistent template bindings, not a new calculation of ticket priority, urgency, impact or SLA.

## Requirements

| Component | Requirement declared by the plugin |
|---|---|
| GLPI | `>= 11.0.0` and `< 12.0.0` |
| PHP | `>= 8.2.0`, also meeting the installed GLPI version's requirements |
| Plugin directory | `sttemplatecontrol` |
| Configuration access | Native `config` permission: read to view and update to save |

**Declared compatibility is not certification of every 11.x release.** Validate the exact GLPI version and integrations before production use. This package is not intended for GLPI 10 and does not declare GLPI 12 support.

## Installation

1. Take a backup and test in a staging environment. When the previous `sttemplatepriority` fork or the upstream `unsticktemplates` plugin is active, deactivate it before activating this package; avoid two plugins handling the same template links simultaneously.
2. Extract the package's `sttemplatecontrol` directory into GLPI's `plugins` directory. The final path must be `plugins/sttemplatecontrol/setup.php`, without an intermediate directory.
3. Apply ownership and permissions appropriate to the web-server account and the installation's security policy. Do not use `777` permissions.
4. Open **Setup > Plugins**, locate **ST Template Control**, install and activate it.
5. Open **Setup > General > ST Template Control**, review the options and save.

Example extraction, adjusting paths for your server:

```bash
cd /usr/share/glpi/plugins
unzip /path/to/sttemplatecontrol-2.0.0.zip
```

For Git installation, clone **your fork** with the correct local directory name:

```bash
cd /usr/share/glpi/plugins
git clone YOUR_FORK_URL sttemplatecontrol
```

Compiled translations are already included; running Composer is not required to install this plugin. GLPI must already be installed and working.

### Replacing the previously named fork

The internal plugin key is now `sttemplatecontrol`. The previous `sttemplatepriority` package is a different plugin identity, not an in-place upgrade or downgrade of this one.

Before switching, back up the environment and record the previous plugin settings. Deactivate `sttemplatepriority` (and `unsticktemplates`, when applicable), then install the complete `sttemplatecontrol` directory from this ZIP. **Do not merely rename the old directory.**

Settings in `plugin:sttemplatepriority` are not automatically copied to `plugin:sttemplatecontrol`. Review and save all six options in the new plugin before testing creation or updates. The old settings are left untouched. Do not run both plugins at the same time.

The distribution version is **2.0.0**, including when replacing a previously prepared local package with a different version label. No template-link restoration or bulk data migration is performed as part of renaming.

### Updating an existing ST Template Control installation

Back up the directory and database, deactivate the plugin, replace its directory with the new package and follow any update action presented by GLPI. Then reactivate and validate the settings. Do not uninstall merely to replace files: uninstallation removes the plugin configuration.

For future updates under this name, retain the `sttemplatecontrol` directory and key. This package makes no database schema changes and does not convert either prior plugin registration automatically.

## Configuration and defaults

| English option | Default | Purpose |
|---|---|---|
| Enable template compatibility | Yes | Globally enables or disables plugin behaviour. |
| Apply to tickets | Yes | Enables handling for tickets. |
| Apply to changes | Yes | Enables handling for changes. |
| Apply to problems | Yes | Enables handling for problems. |
| Clear persisted template link on updates | Yes | Allows clearing the persisted link after an update. |
| Enable debug logging | No | Records technical diagnostic messages. |

> **Important:** update-time template-link cleanup is **enabled by default**. To evaluate display behaviour first, disable this setting before testing updates. This disables the plugin's post-update cleanup; it does not disable the other input handling or prevent GLPI's own template recalculations.

## Portuguese and English

The interface follows the language selected in the **GLPI user's preferences**; there is no separate language selector in the plugin.

| Language | Catalogue |
|---|---|
| Português (Brasil) | `pt_BR` |
| English — United Kingdom | `en_GB` |
| English — United States | `en_US` |

The Spanish (`es_ES`) and French (`fr_FR`) catalogues already included in the supplied package are retained. The Portuguese interface uses “modelos” for template-related labels. The product and company names are unchanged across languages; technical debug log entries remain in English.

Translations use [GLPI's gettext conventions](https://glpi-developer-documentation.readthedocs.io/en/master/devapi/translations.html) and the `sttemplatecontrol` domain. Editable `.po` sources and runtime `.mo` files are both provided in `locales/`.

To change a translation, edit its `.po` file and recompile with GNU gettext:

```bash
msgfmt --check --check-format locales/pt_BR.po -o locales/pt_BR.mo
msgfmt --check --check-format locales/en_GB.po -o locales/en_GB.mo
msgfmt --check --check-format locales/en_US.po -o locales/en_US.mo
```

Preserve placeholders such as `%s`. When a translation does not appear, confirm the user's language, verify the corresponding `.mo` file and sign in again. If the installation caches older catalogues, assess cache clearing or a PHP service reload using the environment's operational procedures.

## Data impact and rollback

Installing or activating the plugin **does not run a bulk update**. Settings are stored in the native `plugin:sttemplatecontrol` context; no plugin-specific table is created.

During update cleanup, the current item's link field can be set to `0`: `tickettemplates_id`, `changetemplates_id` or `problemtemplates_id`. This is not the same as deleting the template definition.

Deactivating the plugin stops its intervention. Uninstalling removes its settings, but **does not reconstruct cleared links or links that were never stored on new records**. Exact restoration of the previous state requires a backup and an appropriate restore procedure.

There is no bulk-rewrite routine for categories, titles, descriptions, attachments or SLAs. However, because template selection influences displayed fields and form rules, service workflows still require staging validation.

## Diagnostics and validation

When debug logging is enabled, the default log path is:

```text
files/_log/sttemplatecontrol.log
```

The path may differ when GLPI's log directory has been customised. Disable debug logging after troubleshooting and apply the environment's log-retention policy.

Run the isolated checks with PHP, Python 3 and Bash available:

```bash
bash tests/static-check.sh
```

The suite checks PHP syntax, plugin identity and hook registration, template handling, configuration, translation catalogues and local Service TIC panel assets. Translation checks verify actual `.mo` entries, agreement with `.po` sources and formatting-placeholder integrity.

**Isolated tests do not replace a real GLPI instance.** Validate ticket, change and problem creation/editing; different profiles and entities; hidden and mandatory fields; category changes; previews; forms; API; email collectors and other installed plugins. Extensions that bypass the hooks used by this plugin may behave differently.

See the [test plan](docs/PLANO_TESTES.md), [technical analysis](docs/ANALISE_TECNICA.md), [verification of this revision](docs/REVISAO_2.0.0.md) and [changelog](CHANGELOG.md). The technical documents are in Portuguese; the package verification report is bilingual. Identifiers and paths in this package have been updated to the new name.

## Origin, attribution and licence

The upstream project is **Unstick ITIL Templates**, by **Curtis Conard (`cconard96`)**. ST Template Control extends that approach with display handling, per-item-type settings, update handling, diagnostics and multilingual documentation. Service TIC maintains this fork/adaptation and does not claim exclusive authorship of the original idea.

Distributed under **GPL-3.0-or-later**. See [LICENSE](LICENSE) and [NOTICE.md](NOTICE.md). This is an independent plugin, not an official GLPI core component.

***
### Desenvolvido por: Service TIC Soluções Tecnológicas (Developed by: Service TIC Technological Solutions)

- [![E-mail](https://img.icons8.com/ios-filled/16/ffffff/mail.png)](mailto:contato@servicetic.com.br) **E-mail**: [contato@servicetic.com.br](mailto:contato@servicetic.com.br)
- [![Website](https://img.icons8.com/ios-filled/16/ffffff/domain.png)](https://www.servicetic.com.br) **Website**: [www.servicetic.com.br](https://www.servicetic.com.br)
- [![LinkedIn](https://img.icons8.com/ios-filled/16/ffffff/linkedin-circled.png)](https://www.linkedin.com/company/serviceticst) **LinkedIn**: [@serviceticst](https://www.linkedin.com/company/serviceticst)
- [![Instagram](https://img.icons8.com/ios-filled/16/ffffff/instagram-new.png)](https://www.instagram.com/serviceticst) **Instagram**: [@serviceticst](https://www.instagram.com/serviceticst)
- [![Facebook](https://img.icons8.com/ios-filled/16/ffffff/facebook-new.png)](https://www.facebook.com/serviceticst) **Facebook**: [@serviceticst](https://www.facebook.com/serviceticst)
- [![X](https://img.icons8.com/ios-filled/16/ffffff/x.png)](https://x.com/serviceticst) **X (Twitter)**: [@serviceticst](https://x.com/serviceticst)
- [![YouTube](https://img.icons8.com/ios-filled/16/ffffff/youtube-squared.png)](https://youtube.com/c/serviceticst) **YouTube**: [@serviceticst](https://youtube.com/c/serviceticst)
- [![WhatsApp](https://img.icons8.com/ios-filled/16/ffffff/whatsapp.png)](https://whatsapp.com/channel/0029VaAkV3P59PwXAiDepu3N) **WhatsApp Channel**: [Click here](https://whatsapp.com/channel/0029VaAkV3P59PwXAiDepu3N)

[![Service TIC](https://github.com/user-attachments/assets/17192a13-f0b6-4531-add0-99c7f46c24b0)](https://servicetic.com.br/links/)
