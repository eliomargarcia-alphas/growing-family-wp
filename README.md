# growing.family — código propio de WordPress

Repositorio del código a medida de https://growing.family (alojado en SiteGround).

## Qué contiene

| Ruta | Qué es |
|---|---|
| `wp-content/themes/growingfamily2` | Tema activo "Growing Family 2.0" |
| `wp-content/plugins/gwf-bloque-azul` | Bloque propio |
| `wp-content/plugins/gwf-bloque-imagen-texto` | Bloque propio |
| `wp-content/plugins/gwf-bloque-linea-izquierda` | Bloque propio |

**No** se versionan: WordPress core, plugins de terceros, `uploads/`, `wp-config.php` ni la base de datos.
Esos se respaldan con las copias de SiteGround (Site Tools → Seguridad → Copias de seguridad).

## Flujo de trabajo

1. Antes de empezar: `scripts/site.sh diff` — comprueba que producción no se haya editado fuera de git
   (por ejemplo desde wp-admin o con WPVibe). Si difiere: `scripts/site.sh pull`, revisa con `git diff` y commitea.
2. Edita, prueba y haz commit.
3. `git push` a GitHub.
4. `scripts/site.sh deploy` — hace backup en el servidor (`~/backups/deploys/`), sube el commit actual,
   y purga la caché de SiteGround. Se niega a desplegar si hay cambios sin commitear o si producción
   fue modificada desde el último deploy.

Revertir un deploy: el script imprime el comando exacto con el backup correspondiente.

## Acceso

- SSH: alias `siteground` en `~/.ssh/config` (puerto 18765, se conecta a la IP del servidor porque
  `ssh.growing.family` pasa por Cloudflare y no admite SSH).
- Ruta en el servidor: `~/www/growing.family/public_html`. También existe `~/www/staging2.growing.family`.
