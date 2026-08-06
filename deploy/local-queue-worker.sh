#!/usr/bin/env bash
# Run the Laravel database queue worker for local development.
# Usage: ./deploy/local-queue-worker.sh
# Keep this running in a separate terminal while php artisan serve is active.

set -euo pipefail

cd "$(dirname "$0")/.."

php artisan queue:work database \
  --sleep=3 \
  --tries=1 \
  --timeout=3600 \
  --max-time=3600
