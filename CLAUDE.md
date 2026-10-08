# CLAUDE.md — growing.family

Código propio del WordPress de https://growing.family (SiteGround, detrás de Cloudflare).
Producción en vivo: actuar con cuidado. Responder al usuario en español.

## Qué hay en el repo
- `wp-content/themes/growingfamily2` — tema activo "Growing Family 2.0" (basado en Twenty Twenty).
- `wp-content/plugins/gwf-*` — 3 bloques propios.
- No se versiona: core, plugins de terceros, `uploads/`, `wp-config.php`, base de datos.
- `.gitattributes` tiene `* -text`: los archivos se guardan byte a byte igual que en el servidor. No cambiar finales de línea.

## Git
- Autoría en este repo: `Eliomar Garcia <eliomar.garcia@alphasremote.team>` (ya en `git config` local).
- Remoto: `github.com/eliomargarcia-alphas/growing-family-wp` (privado), rama `main`.

## Acceso al servidor
- SSH: alias `siteground` en `~/.ssh/config` → IP `34.174.211.179`, puerto `18765`, clave `~/.ssh/siteground_ed25519`.
  No usar `ssh.growing.family`: pasa por el proxy de Cloudflare y SSH hace timeout.
- WordPress: `~/www/growing.family/public_html`. Staging: `~/www/staging2.growing.family`. WP-CLI disponible (`wp`).
- Backups: `~/backups/` (zips antiguos) y `~/backups/deploys/` (backups automáticos de cada deploy + `LAST_DEPLOYED`).
- WPVibe (conector MCP, plugin `vibe-ai`): para contenido (entradas, páginas, medios, REST). El sitio usa SeedProd:
  cargar `load_skill({ name: "seedprod" })` antes de editar páginas hechas con él.
  SiteGround Antibot puede desafiar peticiones directas a `/wp-json/`; el relay de WPVibe sí funciona.

## Flujo para cambios de código
1. `scripts/site.sh diff` — verificar que producción no se editó fuera de git. Si difiere: `scripts/site.sh pull`, revisar `git diff`, commitear.
2. Editar, commit, `git push`.
3. `scripts/site.sh deploy` — **pedir confirmación al usuario antes**. Hace backup remoto, sube HEAD, purga caché SG
   y se niega si hay cambios sin commitear o drift en producción. Se despliega directo a producción (decisión del usuario).
4. Si se editan archivos del tema vía WPVibe o por SSH directamente, después hacer `pull` + commit para no perderlos.

## Convenciones de rendimiento
- Imágenes de plantillas: usar la versión `*-opt.webp` (generada en el servidor con `cwebp -q 82`, a ~2x su
  tamaño en pantalla; el original queda junto a ella en uploads). Toda `<img>` lleva `width`/`height`;
  `loading="lazy" decoding="async"` bajo el pliegue, `fetchpriority="high"` en la imagen principal.
- `twentytwenty-*.min.css` lo genera SG Optimizer (Minify CSS) desde `style.css`: no se versiona ni se edita.
- Bootstrap: el tema carga `assets/css/bootstrap-gwf.min.css` (solo clases usadas). Si una plantilla nueva
  usa clases de Bootstrap que no estén ahí, regenerarlo desde `bootstrap.min.css`.
- Fuentes locales en `assets/fonts` (Open Sans variable + Cal Sans, subconjunto latin), declaradas en
  `gwf_self_hosted_fonts()`. No volver a enlazar Google Fonts ni Font Awesome.

- Plugins por página: `gwf_trim_plugin_assets()` en functions.php quita CSS/JS de plugins donde no se usan
  (ULike, Easy TOC, AddToAny, CF7 fuera de Contacto, Ajax Search Lite). Las páginas ya **no cargan jQuery**:
  si una plantilla nueva lo necesita, encolarlo explícitamente (`wp_enqueue_script( 'jquery' )`).
  Si se añade un formulario CF7 en otra página, añadirla a la condición de `is_page( 'contacto-y-soporte' )`.

## Reglas
- Backup antes de tocar cualquier archivo del servidor fuera del script; confirmar antes de borrar o sobrescribir.
- Nunca commitear ni mostrar secretos (`wp-config.php`, claves, Application Passwords).
- Tras cambios de CSS/JS/PHP, la caché (SG Optimizer + Cloudflare) puede servir la versión vieja: `wp sg purge`.
