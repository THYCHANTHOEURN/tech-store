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
    :
fi

APP_PORT="${PORT:-80}"

# Ensure Apache listens on the platform-provided port and all IPv4 interfaces.
if [ -f /etc/apache2/ports.conf ]; then
    sed -i -E "s/^Listen .*/Listen 0.0.0.0:${APP_PORT}/" /etc/apache2/ports.conf || true
fi

if [ -f /etc/apache2/sites-available/000-default.conf ]; then
    sed -i -E "s/<VirtualHost \*:([0-9]+)>/<VirtualHost *:${APP_PORT}>/" /etc/apache2/sites-available/000-default.conf || true
fi

# Provide a ServerName to suppress warnings and ensure proper virtual host behavior
if [ ! -f /etc/apache2/conf-available/servername.conf ]; then
    printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf
    a2enconf servername >/dev/null 2>&1 || true
fi

# Start Apache first so Render can detect an open port quickly.
apache2-foreground &
APACHE_PID=$!
echo "Apache started on port ${APP_PORT} (pid ${APACHE_PID})"

if [ "${AUTO_MIGRATE_ON_STARTUP:-true}" = "true" ] && [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    # Wait for DB readiness, but don't block forever.
    MAX_ATTEMPTS=12
    SLEEP_SECONDS=5
    attempt=1
    until php artisan migrate:status --no-interaction >/dev/null 2>&1; do
        if [ "$attempt" -ge "$MAX_ATTEMPTS" ]; then
            echo "Database still unreachable after $((MAX_ATTEMPTS * SLEEP_SECONDS))s, skipping migrations"
            break
        fi
        echo "Waiting for DB to become available (attempt $attempt/$MAX_ATTEMPTS)"
        attempt=$((attempt + 1))
        sleep $SLEEP_SECONDS
    done

    if php artisan migrate:status --no-interaction >/dev/null 2>&1; then
        if [ "$FORCE_MIGRATE_FRESH_SEED" = "true" ]; then
            echo "FORCE_MIGRATE_FRESH_SEED=true -> running migrate:fresh --seed"
            php artisan migrate:fresh --seed --force
        else
            echo "Running migrations..."
            php artisan migrate --force
            if [ "$RUN_DB_SEED_ON_STARTUP" = "true" ]; then
                echo "RUN_DB_SEED_ON_STARTUP=true -> running db:seed"
                php artisan db:seed --force
            fi
        fi
    else
        echo "Skipping migrate/seed because database is not ready"
    fi
fi

# Regenerate Ziggy routes JS so frontend has up-to-date route definitions
# This helps when the build pipeline didn't run `php artisan ziggy:generate`
# and ensures `resources/js/ziggy.js` matches the runtime `APP_URL`.
if command -v php >/dev/null 2>&1; then
    echo "Generating Ziggy routes file (resources/js/ziggy.js)"
    php artisan ziggy:generate --out=resources/js/ziggy.js || true
fi

wait "$APACHE_PID"
