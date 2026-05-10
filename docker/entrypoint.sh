#!/bin/sh
set -e

cd /var/www/html

CERT_PATH=/etc/ssl/certs/aiven-ca.pem

# If MYSQL_ATTR_SSL_CA contains an inline certificate, write it to a file and export the path
if [ -n "$MYSQL_ATTR_SSL_CA" ]; then
    case "$MYSQL_ATTR_SSL_CA" in
        -----BEGIN*)
            mkdir -p "$(dirname "$CERT_PATH")"
            echo "Writing Aiven CA to $CERT_PATH"
            cat > "$CERT_PATH" <<'AIVEN_CA'
$MYSQL_ATTR_SSL_CA
AIVEN_CA
            chmod 644 "$CERT_PATH" || true
            export MYSQL_ATTR_SSL_CA=$CERT_PATH
            ;;
        /*)
            # already a file path, leave as-is
            ;;
        *)
            # unknown content, leave as-is
            ;;
    esac
fi

if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
        if php artisan migrate:status --no-interaction >/dev/null 2>&1; then
                php artisan migrate --force
        else
                php artisan migrate:fresh --seed --force
        fi
fi

exec apache2-foreground
