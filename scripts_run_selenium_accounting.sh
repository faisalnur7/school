#!/usr/bin/env bash
set -u

cd /home/faisal/Sites/school
mkdir -p artifacts/selenium

export SELENIUM_BASE_URL="${SELENIUM_BASE_URL:-http://127.0.0.1}"
export SELENIUM_EMAIL="${SELENIUM_EMAIL:-admin@example.com}"
export SELENIUM_PASSWORD="${SELENIUM_PASSWORD:-password}"
export SELENIUM_ARTIFACT_DIR="${SELENIUM_ARTIFACT_DIR:-artifacts/selenium}"

# Ensure predictable login user exists in the running Sail app container.
docker exec school-laravel.test-1 php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$kernel=\$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); \$u=App\Models\User::updateOrCreate(['email'=>getenv('SELENIUM_EMAIL')?:'${SELENIUM_EMAIL}'], ['name'=>'Selenium Admin','password'=>Illuminate\Support\Facades\Hash::make(getenv('SELENIUM_PASSWORD')?:'${SELENIUM_PASSWORD}')]); echo 'selenium_user_ready:'.\$u->id;" > artifacts/selenium/user_setup.log 2>&1

# Pick one student id for fee-module page test (if present)
STUDENT_ID=$(docker exec school-laravel.test-1 php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$kernel=\$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); echo (string) optional(App\Models\Student::query()->first())->id;" 2>/dev/null | tr -d '\r\n')
export SELENIUM_STUDENT_ID="$STUDENT_ID"

# Wait for app to be ready
READY=0
for _ in $(seq 1 40); do
  if curl -sSf "${SELENIUM_BASE_URL}/login" >/dev/null 2>&1; then
    READY=1
    break
  fi
  sleep 1
done

if [ "$READY" -ne 1 ]; then
  echo "Laravel server did not become ready" | tee artifacts/selenium/run.log
  exit 1
fi

python3 tests/selenium/test_accounting_and_fees.py 2>&1 | tee artifacts/selenium/run.log
EXIT_CODE=${PIPESTATUS[0]}

# Create concise summary file
{
  echo "Selenium Run Timestamp: $(date -u +'%Y-%m-%dT%H:%M:%SZ')"
  echo "Base URL: ${SELENIUM_BASE_URL}"
  echo "Student ID Used: ${SELENIUM_STUDENT_ID:-<none>}"
  echo "Exit Code: ${EXIT_CODE}"
} > artifacts/selenium/summary.txt

exit "$EXIT_CODE"
