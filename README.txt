========================================================================
EQUALFLOW — NEW MACHINE SETUP GUIDE
========================================================================

AI-powered project & task management system
Stack: Laravel 11 + PHP 8.4 + MySQL + Blade/Tailwind/Alpine + Python FastAPI
Local dev URL: http://equalflow.test (via Laravel Herd)

This guide takes you from a fresh machine to a running local instance.


========================================================================
1. PREREQUISITES — INSTALL THESE FIRST
========================================================================

  - Laravel Herd            https://herd.laravel.com
  - Composer 2.x            https://getcomposer.org (bundled with Herd)
  - Node.js + npm (20 LTS)  https://nodejs.org
  - Python 3.11+            https://python.org
  - Git                     https://git-scm.com
  - MySQL 8.0+              Bundled with Herd Pro, or install standalone

Verify after install:

  php -v
  composer -V
  node -v
  npm -v
  python --version
  git --version

Herd should be running with PHP 8.4 selected (Herd > PHP > 8.4).


========================================================================
2. CLONE THE REPOSITORY
========================================================================

  cd %USERPROFILE%\Herd
  git clone <your-repo-url> equalflow
  cd equalflow

If no repo yet, copy the project folder into:
  C:\Users\<you>\Herd\equalflow


========================================================================
3. INSTALL PHP DEPENDENCIES
========================================================================

  composer install

If composer install fails (no lock file):

  composer update

Required packages (verify they appear in composer.json):

  - laravel/framework
  - laravel/socialite      (Google OAuth)
  - vinkla/hashids         (URL obfuscation)
  - laravel/tinker

If any are missing:

  composer require laravel/socialite vinkla/hashids


========================================================================
4. INSTALL NODE DEPENDENCIES
========================================================================

  npm install


========================================================================
5. CREATE THE .ENV FILE
========================================================================

  copy .env.example .env
  php artisan key:generate

Open .env in your editor and set:

  APP_NAME=EqualFlow
  APP_ENV=local
  APP_KEY=                      (auto-filled by key:generate)
  APP_DEBUG=true
  APP_URL=http://equalflow.test

  # ===== DATABASE =====
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=equalflow
  DB_USERNAME=root
  DB_PASSWORD=                  (Herd's MySQL root has no password)

  # ===== SESSION / CACHE / QUEUE =====
  SESSION_DRIVER=database
  CACHE_STORE=database
  QUEUE_CONNECTION=database

  # ===== MAIL (local dev — emails go to log file) =====
  MAIL_MAILER=log
  MAIL_FROM_ADDRESS="noreply@equalflow.test"
  MAIL_FROM_NAME="EqualFlow"

  # ===== GOOGLE OAUTH =====
  GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
  GOOGLE_CLIENT_SECRET=your-client-secret
  GOOGLE_REDIRECT_URI=http://equalflow.test/auth/google/callback

  # ===== HASHIDS (URL obfuscation) =====
  HASHIDS_SALT=equalflow-local-salt
  HASHIDS_LENGTH=8

  # ===== AI MICROSERVICE =====
  AI_API_URL=http://127.0.0.1:8001

IMPORTANT:
  - HASHIDS_LENGTH=8 must be an integer with NO quotes (Hashids throws
    on string input).
  - AI_API_URL points to FastAPI on port 8001 (not 8000 — Laravel uses
    8000 for php artisan serve).


========================================================================
6. CREATE THE DATABASE
========================================================================

  mysql -u root

Inside the MySQL prompt:

  CREATE DATABASE equalflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  EXIT;

Or via Herd's UI: Herd > Database > New Database > equalflow


========================================================================
7. RUN MIGRATIONS & SEEDERS
========================================================================

  php artisan migrate
  php artisan db:seed

If project uses a specific seeder:

  php artisan db:seed --class=TestDataSeeder

TEST CREDENTIALS (after seeding):

  Password (all users):  password123
  Company invite code:   CS2024

  Principal            ahmad@creativesolutions.com
  Leader (Civil)       sarah@creativesolutions.com
  Leader (Structural)  mike@creativesolutions.com
  Member (Civil)       aina@creativesolutions.com
  Member (Drafter)     raj@creativesolutions.com


========================================================================
8. LINK STORAGE
========================================================================

  php artisan storage:link

Required for file uploads (task submissions, project documents,
profile pictures).


========================================================================
9. BUILD FRONTEND ASSETS
========================================================================

For development (watch mode):

  npm run dev

For a production build (one-off):

  npm run build

Leave npm run dev running in a separate terminal while developing.


========================================================================
10. SET UP THE AI MICROSERVICE (FASTAPI)
========================================================================

  cd equalflow-ai

Create and activate a virtual environment:

  python -m venv venv
  venv\Scripts\activate

Install dependencies:

  pip install -r requirements.txt

Start the service:

  uvicorn main:app --host 127.0.0.1 --port 8001 --reload

AI service now runs at http://127.0.0.1:8001.
Leave this terminal open.

Verify it's up:

  curl http://127.0.0.1:8001/health

Should return {"status":"ok"} or similar.


========================================================================
11. LINK THE SITE TO HERD
========================================================================

  Herd > Sites > Add Site > select C:\Users\<you>\Herd\equalflow

Herd automatically serves it at http://equalflow.test

If Herd doesn't auto-detect:

  Herd > Sites > Link > equalflow
  (or create a symlink manually)


========================================================================
12. CLEAR CACHES & VERIFY
========================================================================

  php artisan optimize:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  php artisan serve --port=8080

Open your browser:

  http://equalflow.test

You should see the EqualFlow welcome page.


========================================================================
13. GOOGLE OAUTH SETUP (IF YOU NEED IT)
========================================================================

The Google OAuth code exists but needs a Google Cloud Console project.

  1. Go to https://console.cloud.google.com
  2. Create a project named "EqualFlow"
  3. APIs & Services > OAuth consent screen
       - User Type: External
       - Publishing Status: Testing
       - Test Users: add your own Gmail address
         (critical — without this you get "access_denied")
  4. APIs & Services > Credentials > Create OAuth client ID
       - Application Type: Web application
       - Authorized JavaScript origins:  http://equalflow.test
       - Authorized redirect URIs:       http://equalflow.test/auth/google/callback
  5. Copy Client ID and Client Secret into .env:

       GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
       GOOGLE_CLIENT_SECRET=xxx

Then clear config:

  php artisan config:clear


========================================================================
14. RUNNING THE FULL STACK
========================================================================

You need THREE terminals running in parallel:

Terminal 1 — AI microservice:
  cd equalflow-ai
  venv\Scripts\activate
  uvicorn main:app --host 127.0.0.1 --port 8001 --reload

Terminal 2 — Vite dev server:
  cd equalflow
  npm run dev

Terminal 3 — Queue worker (notifications, emails):
  cd equalflow
  php artisan queue:work

Herd runs the web server automatically — no need for php artisan serve
if you're using Herd.

Then browse to http://equalflow.test


========================================================================
15. COMMON ISSUES & FIXES
========================================================================

PROBLEM: TypeError: Hashids\Hashids::__construct(): Argument #2
         ($minHashLength) must be of type int, string given

FIX:     Open config/hashids.php and cast length to (int):

           'length' => (int) env('HASHIDS_LENGTH', 8),

         Also verify .env has HASHIDS_LENGTH=8 WITHOUT quotes.


PROBLEM: 404 on /projects/5 after switching to hashids

CAUSE:   Browser cached an old integer URL.

FIX:     The HasHashid trait should be tolerant — accept BOTH hashids
         and raw integers:

           public function resolveRouteBinding($value, $field = null)
           {
               $decoded = Hashids::decode($value);
               $id = !empty($decoded) ? $decoded[0] : $value;

               if (!is_numeric($id)) {
                   abort(404);
               }

               return $this->where($this->getKeyName(), $id)->firstOrFail();
           }

         Then:

           php artisan optimize:clear

         And hard-reload the browser (Ctrl + Shift + R).


PROBLEM: Personal project not showing for assigned user

CAUSE:   User is assigned a task but isn't in the project_members pivot,
         OR the index blade only iterates $projects and never
         $invitedProjects.

FIX:
  1. Ensure TaskController::store(), storeSubtask(), update(), and
     updateSubtask() auto-create ProjectMember rows for personal projects.
  2. Ensure personal/index.blade.php merges $projects and
     $invitedProjects:
       $allProjects = $projects->concat($invitedProjects ?? collect())
                              ->unique('id');
     and iterates $allProjects.
  3. Backfill existing data:

       php artisan tinker --execute="
       foreach (App\Models\Task::whereNotNull('assigned_to')
           ->whereHas('project', fn(\$q) => \$q->where('type', 'personal'))
           ->get() as \$task) {
           App\Models\ProjectMember::firstOrCreate([
               'project_id' => \$task->project_id,
               'user_id' => \$task->assigned_to,
           ]);
       }
       echo 'Backfill complete.' . PHP_EOL;
       "


PROBLEM: Notifications not appearing

CAUSE 1: Route /notifications/data missing.
FIX:     Ensure routes/web.php has:
           Route::get('/notifications/data',
                      [NotificationController::class, 'index']);

CAUSE 2: Bell fetches the wrong endpoint.
FIX:     resources/views/components/notification-bell.blade.php should
         fetch /notifications/data, not /notifications.

CAUSE 3: Email service throws on task_completed.
FIX:     Create app/Mail/TaskCompletedMail.php, or route
         task_completed -> NotificationMail in EmailNotificationService.


PROBLEM: PSR-4 autoload warning
         "Class App\Http\Controllers\ProfileSetupController located in
          ./app/Http/Requests/ProfileUpdateRequest.php does not comply
          with psr-4"

FIX:     Open app/Http/Requests/ProfileUpdateRequest.php, clean out any
         stray class definitions, then:

           composer dump-autoload


PROBLEM: php artisan migrate fails — table exists

FIX:     Fresh reset:

           php artisan migrate:fresh --seed

         WARNING: This wipes all data. Only do this in local dev.


PROBLEM: AI ranking falls back to "local" instead of "python"

CAUSE:   FastAPI service isn't running, or AI_API_URL is wrong.

FIX:
  1. Verify FastAPI is up:  curl http://127.0.0.1:8001/health
  2. Verify .env has AI_API_URL=http://127.0.0.1:8001
  3. php artisan config:clear


========================================================================
16. RUNNING TESTS (IF APPLICABLE)
========================================================================

  php artisan test

Or a specific test:

  php artisan test --filter=TaskTest


========================================================================
17. PROJECT STRUCTURE CHEAT SHEET
========================================================================

  equalflow/
  |
  +-- app/
  |   +-- Http/Controllers/         TaskController, ProjectController, etc.
  |   +-- Models/                   Task, Project, User, Department, ...
  |   |   +-- Concerns/HasHashid.php
  |   +-- Mail/                     NotificationMail, TaskAssignedMail, ...
  |   +-- Services/                 AIService, WorkloadAnalyzer,
  |   |                             EmailNotificationService
  |   +-- Helpers/                  NotificationHelper
  |   +-- Observers/                TaskObserver
  |
  +-- config/
  |   +-- hashids.php
  |
  +-- database/
  |   +-- migrations/
  |   +-- seeders/
  |
  +-- resources/
  |   +-- views/
  |       +-- layouts/app.blade.php
  |       +-- personal/
  |       +-- projects/
  |       +-- tasks/
  |       +-- departments/
  |
  +-- routes/
  |   +-- web.php
  |
  +-- storage/
  |   +-- logs/laravel.log          tail this while debugging
  |
  +-- equalflow-ai/                 Python FastAPI service
  |   +-- main.py
  |   +-- requirements.txt
  |   +-- venv/
  |
  +-- .env


========================================================================
18. USEFUL COMMANDS REFERENCE
========================================================================

  COMMAND                              WHAT IT DOES
  ---------------------------------------------------------------------
  php artisan migrate                  Run pending migrations
  php artisan migrate:fresh --seed     Drop all, re-migrate, seed
  php artisan db:seed                  Re-run seeders
  php artisan optimize:clear           Clear all caches
  php artisan storage:link             Create public storage symlink
  php artisan tinker                   Interactive REPL
  php artisan queue:work               Process queued jobs
  php artisan route:list               List registered routes
  npm run dev                          Vite dev server (hot reload)
  npm run build                        Production asset build
  composer dump-autoload               Regenerate autoloader


========================================================================
19. TEST CREDENTIALS QUICK REFERENCE
========================================================================

  Password (all users):  password123
  Company invite code:   CS2024

  ROLE                    EMAIL
  ---------------------------------------------------------------------
  Principal               ahmad@creativesolutions.com
  Leader (Civil)          sarah@creativesolutions.com
  Leader (Structural)     mike@creativesolutions.com
  Member (Civil)          aina@creativesolutions.com
  Member (Drafter)        raj@creativesolutions.com


========================================================================
20. GETTING HELP
========================================================================

If something breaks:

  1. Check the log:
       powershell -Command "Get-Content storage\logs\laravel.log -Tail 50"

  2. Check the browser console (F12 > Console) for JS errors.

  3. Check the Network tab (F12 > Network) for failed requests —
     status codes tell you if it's 403 / 404 / 500.

  4. Clear all caches:
       php artisan optimize:clear

  5. Hard-reload the browser: Ctrl + Shift + R

