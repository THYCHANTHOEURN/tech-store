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
            # Handle three common forms:
            # 1) Raw PEM with newlines (normal)
            # 2) PEM with literal '\n' sequences (from some dashboards)
            # 3) Base64-encoded PEM
            if printf '%s' "$MYSQL_ATTR_SSL_CA" | grep -q "-----BEGIN" >/dev/null 2>&1; then
                # Convert literal \n into newlines and strip CRs
                printf '%b' "$MYSQL_ATTR_SSL_CA" | tr -d '\r' > "$CERT_PATH"
            else
                # Might be base64; try to decode safely
                if printf '%s' "$MYSQL_ATTR_SSL_CA" | grep -Eq '^[A-Za-z0-9+/=[:space:]]+$'; then
                    printf '%s' "$MYSQL_ATTR_SSL_CA" | tr -d '\r\n' | base64 -d > "$CERT_PATH" 2>/dev/null || {
                        # fallback: write raw and hope for the best
                        printf '%s' "$MYSQL_ATTR_SSL_CA" > "$CERT_PATH"
                    }
                else
                    printf '%s' "$MYSQL_ATTR_SSL_CA" | tr -d '\r' > "$CERT_PATH"
                fi
            fi
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
