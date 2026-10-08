#!/usr/bin/env bash
# Sincroniza el código propio de growing.family con el servidor de SiteGround.
#
#   scripts/site.sh diff     Compara el repo (HEAD) con lo que hay en producción
#   scripts/site.sh pull     Trae los archivos de producción al working tree (luego revisar con git diff)
#   scripts/site.sh deploy   Backup remoto + sube HEAD a producción + purga la caché de SiteGround
#
# Requiere el alias SSH "siteground" en ~/.ssh/config.
set -euo pipefail

SSH_HOST="siteground"
REMOTE_WP="www/growing.family/public_html"
REMOTE_BACKUPS="backups/deploys"
PATHS=(
  wp-content/themes/growingfamily2
  wp-content/plugins/gwf-bloque-azul
  wp-content/plugins/gwf-bloque-imagen-texto
  wp-content/plugins/gwf-bloque-linea-izquierda
)

ROOT="$(git -C "$(dirname "$0")" rev-parse --show-toplevel)"
cd "$ROOT"

fetch_remote() { # $1 = directorio destino
  mkdir -p "$1"
  ssh "$SSH_HOST" "cd ~/$REMOTE_WP && tar czf - ${PATHS[*]}" | tar xzf - -C "$1"
}

cmd_diff() {
  local tmp_remote tmp_head
  tmp_remote="$(mktemp -d)"; tmp_head="$(mktemp -d)"
  trap 'rm -rf "$tmp_remote" "$tmp_head"' RETURN
  fetch_remote "$tmp_remote"
  git archive HEAD "${PATHS[@]}" | tar xf - -C "$tmp_head"
  if diff -rq -x node_modules "$tmp_head" "$tmp_remote"; then
    echo "OK: producción coincide con HEAD."
  else
    echo
    echo "AVISO: producción difiere de HEAD (cambios hechos fuera de git o sin desplegar)."
    echo "Usa 'scripts/site.sh pull' para traerlos y revisarlos con git diff."
    return 1
  fi
}

cmd_pull() {
  local tmp
  tmp="$(mktemp -d)"
  trap 'rm -rf "$tmp"' RETURN
  fetch_remote "$tmp"
  for p in "${PATHS[@]}"; do
    rm -rf "$p"
    mkdir -p "$(dirname "$p")"
    cp -r "$tmp/$p" "$p"
  done
  git status --short
}

cmd_deploy() {
  if [ -n "$(git status --porcelain)" ]; then
    echo "ERROR: hay cambios sin commitear. Haz commit antes de desplegar." >&2
    exit 1
  fi

  echo "Comprobando que producción no tenga cambios fuera de git..."
  local last_deployed
  last_deployed="$(ssh "$SSH_HOST" "cat ~/$REMOTE_BACKUPS/LAST_DEPLOYED 2>/dev/null" || true)"
  if [ -n "$last_deployed" ] && git cat-file -e "$last_deployed^{commit}" 2>/dev/null; then
    local tmp_remote tmp_last
    tmp_remote="$(mktemp -d)"; tmp_last="$(mktemp -d)"
    fetch_remote "$tmp_remote"
    git archive "$last_deployed" "${PATHS[@]}" | tar xf - -C "$tmp_last"
    if ! diff -rq -x node_modules "$tmp_last" "$tmp_remote"; then
      rm -rf "$tmp_remote" "$tmp_last"
      echo "ERROR: alguien cambió archivos en producción desde el último deploy ($last_deployed)." >&2
      echo "Ejecuta 'scripts/site.sh pull', revisa y commitea esos cambios antes de desplegar." >&2
      exit 1
    fi
    rm -rf "$tmp_remote" "$tmp_last"
  fi

  local sha ts
  sha="$(git rev-parse --short HEAD)"
  ts="$(date +%Y%m%d-%H%M%S)"

  echo "Backup remoto -> ~/$REMOTE_BACKUPS/$ts-before-$sha.tar.gz"
  ssh "$SSH_HOST" "mkdir -p ~/$REMOTE_BACKUPS && cd ~/$REMOTE_WP && tar czf ~/$REMOTE_BACKUPS/$ts-before-$sha.tar.gz ${PATHS[*]}"

  echo "Subiendo $sha a producción..."
  # Sustituye cada carpeta completa: los archivos borrados en git también desaparecen del servidor.
  git archive --format=tar.gz HEAD "${PATHS[@]}" | ssh "$SSH_HOST" "
    set -e
    stage=\$(mktemp -d ~/deploy-stage.XXXX)
    tar xzf - -C \"\$stage\"
    cd ~/$REMOTE_WP
    for p in ${PATHS[*]}; do
      rm -rf \"\$p.old\"
      [ -d \"\$p\" ] && mv \"\$p\" \"\$p.old\"
      mv \"\$stage/\$p\" \"\$p\"
      rm -rf \"\$p.old\"
    done
    rm -rf \"\$stage\"
    echo $(git rev-parse HEAD) > ~/$REMOTE_BACKUPS/LAST_DEPLOYED
    wp sg purge >/dev/null 2>&1 && echo 'Caché de SiteGround purgada.' || echo 'Aviso: no se pudo purgar la caché.'
  "
  echo "Deploy de $sha completado."
  echo "Para revertir: ssh $SSH_HOST 'cd ~/$REMOTE_WP && tar xzf ~/$REMOTE_BACKUPS/$ts-before-$sha.tar.gz'"
}

case "${1:-}" in
  diff)   cmd_diff ;;
  pull)   cmd_pull ;;
  deploy) cmd_deploy ;;
  *) sed -n '2,8p' "$0"; exit 1 ;;
esac
