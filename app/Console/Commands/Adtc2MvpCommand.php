<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class Adtc2MvpCommand extends Command
{
    protected $signature = 'adtc2:mvp
        {--keep-data : Mantém os dados existentes (usa migrate em vez de migrate:fresh)}
        {--skip-migrate : Pula migrations}
        {--skip-seed : Pula seeders}
        {--skip-tests : Pula testes}';

    protected $description = 'Gera MVP completo do sistema de eventos ADTC2';

    public function handle(): int
    {
        $this->info('🚀 ADTC2 Eventos — Gerando MVP completo');
        $this->newLine();

        $this->generateFiles();
        $this->newLine();

        if (!$this->option('skip-migrate')) {
            $fresh = !$this->option('keep-data');
            $cmd = $fresh ? 'migrate:fresh' : 'migrate';

            $this->info($fresh
                ? '📦 Recriando banco (migrate:fresh)...'
                : '📦 Rodando migrations incrementais...');

            try {
                Artisan::call($cmd, ['--force' => true]);
                $this->line('  ' . trim(Artisan::output()));
            } catch (\Throwable $e) {
                $this->error('❌ Falha nas migrations: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        if (!$this->option('skip-seed')) {
            $this->info('🌱 Populando banco...');
            try {
                Artisan::call('db:seed', ['--force' => true]);
                $this->line('  ' . trim(Artisan::output()));
            } catch (\Throwable $e) {
                $this->error('❌ Falha nos seeders: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        if (!$this->option('skip-tests')) {
            $this->info('🧪 Executando testes...');
            $exitCode = Artisan::call('test');
            $this->line(Artisan::output());
            if ($exitCode !== 0) {
                $this->warn('⚠️  Alguns testes falharam — revise o output acima.');
            }
        }

        $this->displaySummary();
        return self::SUCCESS;
    }

    private function hasPest(): bool
    {
        return file_exists(base_path('vendor/bin/pest'));
    }

    private function generateFiles(): void
    {
        $this->info('📝 Gerando arquivos...');

        $this->cleanupOldMigrations();
        $this->cleanupGeneratedFiles();

        $files = [
            // Enums
            'app/Domain/Users/Enums/UserRole.php' => $this->userRoleEnum(),
            'app/Domain/Eventos/Enums/SituacaoEvento.php' => $this->situacaoEnum(),

            // Models
            'app/Models/User.php' => $this->userModel(),
            'app/Models/Congregacao.php' => $this->congregacaoModel(),
            'app/Models/Evento.php' => $this->eventoModel(),

            // Migrations
            'database/migrations/2026_01_01_000001_create_congregacoes_table.php' => $this->migrationCongregacoes(),
            'database/migrations/2026_01_01_000002_add_role_and_congregacao_to_users_table.php' => $this->migrationUsers(),
            'database/migrations/2026_01_01_000003_create_eventos_table.php' => $this->migrationEventos(),

            // Factories
            'database/factories/CongregacaoFactory.php' => $this->congregacaoFactory(),
            'database/factories/EventoFactory.php' => $this->eventoFactory(),
            'database/factories/UserFactory.php' => $this->userFactory(),

            // Policies
            'app/Policies/EventoPolicy.php' => $this->eventoPolicy(),
            'app/Policies/UserPolicy.php' => $this->userPolicy(),

            // Actions — Eventos
            'app/Domain/Eventos/Actions/CreateEventoAction.php' => $this->createEventoAction(),
            'app/Domain/Eventos/Actions/UpdateEventoAction.php' => $this->updateEventoAction(),
            'app/Domain/Eventos/Actions/DeleteEventoAction.php' => $this->deleteEventoAction(),

            // Actions — Users
            'app/Domain/Users/Actions/CreateUserAction.php' => $this->createUserAction(),
            'app/Domain/Users/Actions/UpdateUserAction.php' => $this->updateUserAction(),
            'app/Domain/Users/Actions/DeleteUserAction.php' => $this->deleteUserAction(),

            // FormRequests
            'app/Http/Requests/StoreEventoRequest.php' => $this->storeEventoRequest(),
            'app/Http/Requests/UpdateEventoRequest.php' => $this->updateEventoRequest(),
            'app/Http/Requests/StoreUserRequest.php' => $this->storeUserRequest(),
            'app/Http/Requests/UpdateUserRequest.php' => $this->updateUserRequest(),

            // Controllers
            'app/Http/Controllers/Controller.php' => $this->baseController(),
            'app/Http/Controllers/AuthController.php' => $this->authController(),
            'app/Http/Controllers/DashboardController.php' => $this->dashboardController(),
            'app/Http/Controllers/EventoController.php' => $this->eventoController(),
            'app/Http/Controllers/UserController.php' => $this->userController(),

            // Routes
            'routes/web.php' => $this->routes(),

            // Seeders
            'database/seeders/CongregacaoSeeder.php' => $this->congregacaoSeeder(),
            'database/seeders/UserSeeder.php' => $this->userSeeder(),
            'database/seeders/EventoSeeder.php' => $this->eventoSeeder(),
            'database/seeders/DatabaseSeeder.php' => $this->databaseSeeder(),

            // Views
            'resources/views/layouts/app.blade.php' => $this->layoutView(),
            'resources/views/auth/login.blade.php' => $this->loginView(),
            'resources/views/dashboard.blade.php' => $this->dashboardView(),
            'resources/views/eventos/index.blade.php' => $this->indexView(),
            'resources/views/eventos/form.blade.php' => $this->formView(),
            'resources/views/users/index.blade.php' => $this->usersIndexView(),
            'resources/views/users/form.blade.php' => $this->usersFormView(),

            // Tests
            'tests/Pest.php' => $this->pestConfig(),
            'tests/Unit/EnumsTest.php' => $this->enumsTest(),
            'tests/Feature/AuthTest.php' => $this->authTest(),
            'tests/Feature/EventoTest.php' => $this->eventoTest(),
            'tests/Feature/DashboardTest.php' => $this->dashboardTest(),
            'tests/Feature/UserTest.php' => $this->userTest(),
        ];

        foreach ($files as $path => $content) {
            $fullPath = base_path($path);
            File::ensureDirectoryExists(dirname($fullPath));
            File::put($fullPath, $content);
            $this->line("  ✓ <fg=green>{$path}</>");
        }

        $this->line('  ✓ <fg=green>Removendo testes padrão...</>');
        @File::delete(base_path('tests/Feature/ExampleTest.php'));
        @File::delete(base_path('tests/Unit/ExampleTest.php'));
    }

    /**
     * Mantém apenas as 3 migrations do skeleton Laravel.
     */
    private function cleanupOldMigrations(): void
    {
        $migrationsDir = database_path('migrations');
        if (!File::isDirectory($migrationsDir)) {
            return;
        }

        $preserved = [
            '0001_01_01_000000_create_users_table.php',
            '0001_01_01_000001_create_cache_table.php',
            '0001_01_01_000002_create_jobs_table.php',
        ];

        $removed = 0;
        foreach (File::files($migrationsDir) as $file) {
            if (in_array($file->getFilename(), $preserved, true)) {
                continue;
            }
            File::delete($file->getPathname());
            $this->line("    - migration removida: <fg=red>{$file->getFilename()}</>");
            $removed++;
        }

        if ($removed === 0) {
            $this->line('  ✓ <fg=yellow>Nenhuma migration órfã encontrada.</>');
        }
    }

    /**
     * Lista arquivos ESPECÍFICOS — nunca pastas que contenham
     * arquivos-base do framework (Controller.php, User.php, TestCase.php).
     */
    private function cleanupGeneratedFiles(): void
    {
        $paths = [
            // Diretórios 100% gerados pelo comando
            'app/Domain',
            'app/Policies',
            'app/Http/Requests',

            // Arquivos específicos (não pastas!)
            'app/Http/Controllers/EventoController.php',
            'app/Http/Controllers/AuthController.php',
            'app/Http/Controllers/DashboardController.php',
            'app/Http/Controllers/UserController.php',
            'app/Models/Congregacao.php',
            'app/Models/Evento.php',
            'database/factories/CongregacaoFactory.php',
            'database/factories/EventoFactory.php',
            'database/factories/UserFactory.php',
            'database/seeders/CongregacaoSeeder.php',
            'database/seeders/EventoSeeder.php',
            'database/seeders/UserSeeder.php',
            'resources/views/dashboard.blade.php',
            'resources/views/eventos',
            'resources/views/users',
            'resources/views/auth/login.blade.php',
            'tests/Feature/AuthTest.php',
            'tests/Feature/EventoTest.php',
            'tests/Feature/DashboardTest.php',
            'tests/Feature/UserTest.php',
            'tests/Unit/EnumsTest.php',
        ];

        foreach ($paths as $relative) {
            $path = base_path($relative);
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            } elseif (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('✅ MVP gerado com sucesso!');
        $this->newLine();
        $this->line('<fg=yellow>Credenciais de acesso:</>');
        $this->line('  Admin:        <fg=cyan>admin@adtc2.local</>       / <fg=cyan>password</>');
        $this->line('  Líder SEDE:   <fg=cyan>lider.sede@adtc2.local</>  / <fg=cyan>password</>');
        $this->line('  Visualizador: <fg=cyan>viewer@adtc2.local</>      / <fg=cyan>password</>');
        $this->newLine();
        $this->line('<fg=yellow>Próximos passos:</>');
        $this->line('  1. <fg=cyan>npm install && npm run build</>');
        $this->line('  2. <fg=cyan>php artisan serve</>');
        $this->line('  3. Acesse <fg=cyan>http://localhost:8000</>');
        $this->newLine();
    }

    // ==========================================================
    // ENUMS
    // ==========================================================

    private function userRoleEnum(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Users\Enums;

enum UserRole: string
{
    case AdminGeral = 'admin_geral';
    case LiderCongregacao = 'lider_congregacao';
    case Visualizador = 'visualizador';

    public function label(): string
    {
        return match ($this) {
            self::AdminGeral => 'Administrador Geral',
            self::LiderCongregacao => 'Líder de Congregação',
            self::Visualizador => 'Visualizador',
        };
    }

    public function podeGerenciarEventos(): bool
    {
        return in_array($this, [self::AdminGeral, self::LiderCongregacao], true);
    }

    public function podeGerenciarUsuarios(): bool
    {
        return $this === self::AdminGeral;
    }

    public function veTodosEventos(): bool
    {
        return $this === self::AdminGeral;
    }
}
PHP_FILE;
    }

    private function situacaoEnum(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Eventos\Enums;

enum SituacaoEvento: string
{
    case Agendado = 'agendado';
    case Realizado = 'realizado';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Agendado => 'Agendado',
            self::Realizado => 'Realizado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Agendado => 'bg-blue-100 text-blue-800',
            self::Realizado => 'bg-green-100 text-green-800',
            self::Cancelado => 'bg-red-100 text-red-800',
        };
    }
}
PHP_FILE;
    }

    // ==========================================================
    // MODELS
    // ==========================================================

    private function userModel(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Models;

use App\Domain\Users\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'congregacao_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function isAdminGeral(): bool
    {
        return $this->role === UserRole::AdminGeral;
    }

    public function isLiderCongregacao(): bool
    {
        return $this->role === UserRole::LiderCongregacao;
    }

    public function podeGerenciarEventos(): bool
    {
        return $this->role->podeGerenciarEventos();
    }

    public function podeGerenciarUsuarios(): bool
    {
        return $this->role->podeGerenciarUsuarios();
    }

    public function pertenceACongregacao(int $congregacaoId): bool
    {
        return $this->congregacao_id === $congregacaoId;
    }
}
PHP_FILE;
    }

    private function congregacaoModel(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Models;

use Database\Factories\CongregacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Congregacao extends Model
{
    /** @use HasFactory<CongregacaoFactory> */
    use HasFactory;

    protected $table = 'congregacoes';

    protected $fillable = ['nome'];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
PHP_FILE;
    }

    private function eventoModel(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Models;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Database\Factories\EventoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    /** @use HasFactory<EventoFactory> */
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'evento',
        'anotacoes',
        'congregacao_id',
        'dt_evento',
        'situacao',
    ];

    protected function casts(): array
    {
        return [
            'dt_evento' => 'date',
            'situacao' => SituacaoEvento::class,
        ];
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function scopeFuturos(Builder $q): Builder
    {
        return $q->whereDate('dt_evento', '>=', now()->toDateString())
            ->orderBy('dt_evento');
    }

    public function scopeProximos(Builder $q, int $dias = 30): Builder
    {
        return $q->whereDate('dt_evento', '>=', now()->toDateString())
            ->whereDate('dt_evento', '<=', now()->addDays($dias)->toDateString());
    }

    public function scopeDaCongregacao(Builder $q, int $congregacaoId): Builder
    {
        return $q->where('congregacao_id', $congregacaoId);
    }

    public function scopeFilter(Builder $q, array $f): Builder
    {
        return $q
            ->when($f['busca'] ?? null, function (Builder $qq, $v) {
                $qq->where(function (Builder $inner) use ($v) {
                    $inner->where('evento', 'like', "%{$v}%")
                        ->orWhere('anotacoes', 'like', "%{$v}%");
                });
            })
            ->when($f['congregacao_id'] ?? null, fn (Builder $qq, $v) => $qq->where('congregacao_id', $v))
            ->when($f['situacao'] ?? null, fn (Builder $qq, $v) => $qq->where('situacao', $v))
            ->when($f['de'] ?? null, fn (Builder $qq, $v) => $qq->whereDate('dt_evento', '>=', $v))
            ->when($f['ate'] ?? null, fn (Builder $qq, $v) => $qq->whereDate('dt_evento', '<=', $v));
    }
}
PHP_FILE;
    }

    // ==========================================================
    // MIGRATIONS
    // ==========================================================

    private function migrationCongregacoes(): string
    {
        return <<<'PHP_FILE'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('congregacoes')) {
            return;
        }

        Schema::create('congregacoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('congregacoes');
    }
};
PHP_FILE;
    }

    private function migrationUsers(): string
    {
        return <<<'PHP_FILE'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('visualizador')->after('password');
                $table->index('role');
            });
        }

        if (!Schema::hasColumn('users', 'congregacao_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('congregacao_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('congregacoes')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'congregacao_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['congregacao_id']);
                $table->dropColumn('congregacao_id');
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            });
        }
    }
};
PHP_FILE;
    }

    private function migrationEventos(): string
    {
        return <<<'PHP_FILE'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            return;
        }

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('evento', 150);
            $table->text('anotacoes')->nullable();
            $table->foreignId('congregacao_id')->constrained('congregacoes');
            $table->date('dt_evento');
            $table->string('situacao', 30)->default('agendado');
            $table->timestamps();

            $table->index('dt_evento');
            $table->index('situacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
PHP_FILE;
    }

    // ==========================================================
    // FACTORIES
    // ==========================================================

    private function congregacaoFactory(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CongregacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->city(),
        ];
    }
}
PHP_FILE;
    }

    private function eventoFactory(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Factories;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'evento' => fake()->sentence(3),
            'anotacoes' => fake()->optional()->paragraph(),
            'congregacao_id' => Congregacao::factory(),
            'dt_evento' => fake()->dateTimeBetween('-2 months', '+6 months'),
            'situacao' => SituacaoEvento::Agendado,
        ];
    }

    public function realizado(): static
    {
        return $this->state(fn () => ['situacao' => SituacaoEvento::Realizado]);
    }

    public function cancelado(): static
    {
        return $this->state(fn () => ['situacao' => SituacaoEvento::Cancelado]);
    }

    public function futuro(): static
    {
        return $this->state(fn () => [
            'dt_evento' => now()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
PHP_FILE;
    }

    private function userFactory(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Factories;

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Visualizador,
            'congregacao_id' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::AdminGeral,
            'congregacao_id' => null,
        ]);
    }

    public function lider(?Congregacao $congregacao = null): static
    {
        return $this->state(fn () => [
            'role' => UserRole::LiderCongregacao,
            'congregacao_id' => $congregacao?->id ?? Congregacao::factory(),
        ]);
    }

    public function visualizador(): static
    {
        return $this->state(fn () => ['role' => UserRole::Visualizador]);
    }
}
PHP_FILE;
    }

    // ==========================================================
    // POLICIES
    // ==========================================================

    private function eventoPolicy(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Policies;

use App\Models\Evento;
use App\Models\User;

class EventoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral()
            || $user->pertenceACongregacao($evento->congregacao_id);
    }

    public function create(User $user): bool
    {
        return $user->podeGerenciarEventos();
    }

    public function update(User $user, Evento $evento): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        return $user->isLiderCongregacao()
            && $user->pertenceACongregacao($evento->congregacao_id);
    }

    public function delete(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral();
    }
}
PHP_FILE;
    }

    private function userPolicy(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminGeral();
    }

    public function view(User $user, User $alvo): bool
    {
        return $user->isAdminGeral();
    }

    public function create(User $user): bool
    {
        return $user->isAdminGeral();
    }

    public function update(User $user, User $alvo): bool
    {
        return $user->isAdminGeral();
    }

    public function delete(User $user, User $alvo): bool
    {
        return $user->isAdminGeral() && $user->id !== $alvo->id;
    }
}
PHP_FILE;
    }

    // ==========================================================
    // ACTIONS — EVENTOS
    // ==========================================================

    private function createEventoAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class CreateEventoAction
{
    public function execute(array $data): Evento
    {
        return Evento::create($data);
    }
}
PHP_FILE;
    }

    private function updateEventoAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class UpdateEventoAction
{
    public function execute(Evento $evento, array $data): Evento
    {
        $evento->update($data);
        return $evento->fresh();
    }
}
PHP_FILE;
    }

    private function deleteEventoAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class DeleteEventoAction
{
    public function execute(Evento $evento): void
    {
        $evento->delete();
    }
}
PHP_FILE;
    }

    // ==========================================================
    // ACTIONS — USERS
    // ==========================================================

    private function createUserAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class CreateUserAction
{
    public function execute(array $data): User
    {
        // O cast 'password' => 'hashed' no model já faz o Hash::make
        return User::create($data);
    }
}
PHP_FILE;
    }

    private function updateUserAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }
        $user->update($data);
        return $user->fresh();
    }
}
PHP_FILE;
    }

    private function deleteUserAction(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        $user->delete();
    }
}
PHP_FILE;
    }

    // ==========================================================
    // FORM REQUESTS
    // ==========================================================

    private function storeEventoRequest(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->podeGerenciarEventos();
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:150'],
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'exists:congregacoes,id'],
            'dt_evento' => ['required', 'date'],
            'situacao' => ['required', Rule::enum(SituacaoEvento::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->user()->isAdminGeral() && $this->user()->congregacao_id) {
            $this->merge(['congregacao_id' => $this->user()->congregacao_id]);
        }
    }
}
PHP_FILE;
    }

    private function updateEventoRequest(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('evento'));
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:150'],
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'exists:congregacoes,id'],
            'dt_evento' => ['required', 'date'],
            'situacao' => ['required', Rule::enum(SituacaoEvento::class)],
        ];
    }
}
PHP_FILE;
    }

    private function storeUserRequest(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Requests;

use App\Domain\Users\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->podeGerenciarUsuarios();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'congregacao_id' => [
                'nullable',
                'exists:congregacoes,id',
                Rule::requiredIf(fn () => $this->input('role') === UserRole::LiderCongregacao->value),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('role') === UserRole::AdminGeral->value) {
            $this->merge(['congregacao_id' => null]);
        }
    }
}
PHP_FILE;
    }

    private function updateUserRequest(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Requests;

use App\Domain\Users\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->podeGerenciarUsuarios();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'congregacao_id' => [
                'nullable',
                'exists:congregacoes,id',
                Rule::requiredIf(fn () => $this->input('role') === UserRole::LiderCongregacao->value),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('role') === UserRole::AdminGeral->value) {
            $this->merge(['congregacao_id' => null]);
        }
    }
}
PHP_FILE;
    }

    // ==========================================================
    // CONTROLLERS
    // ==========================================================

    private function baseController(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}
PHP_FILE;
    }

    private function authController(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
PHP_FILE;
    }

    private function dashboardController(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Controllers;

use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $base = Evento::query()->with('congregacao');
        if (!$user->role->veTodosEventos() && $user->congregacao_id) {
            $base->where('congregacao_id', $user->congregacao_id);
        }

        $proximos7 = (clone $base)
            ->whereDate('dt_evento', '>=', now()->toDateString())
            ->whereDate('dt_evento', '<=', now()->addDays(7)->toDateString())
            ->orderBy('dt_evento')
            ->get();

        $proximosEventos = (clone $base)
            ->whereDate('dt_evento', '>=', now()->toDateString())
            ->orderBy('dt_evento')
            ->limit(10)
            ->get();

        $totais = [
            'total' => (clone $base)->count(),
            'proximos30' => (clone $base)
                ->whereDate('dt_evento', '>=', now()->toDateString())
                ->whereDate('dt_evento', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'agendado' => (clone $base)->where('situacao', 'agendado')->count(),
            'realizado' => (clone $base)->where('situacao', 'realizado')->count(),
            'cancelado' => (clone $base)->where('situacao', 'cancelado')->count(),
        ];

        $congregacoes = Congregacao::withCount('eventos')
            ->orderByDesc('eventos_count')
            ->limit(5)
            ->get();

        return view('dashboard', compact('proximos7', 'proximosEventos', 'totais', 'congregacoes'));
    }
}
PHP_FILE;
    }

    private function eventoController(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Controllers;

use App\Domain\Eventos\Actions\CreateEventoAction;
use App\Domain\Eventos\Actions\DeleteEventoAction;
use App\Domain\Eventos\Actions\UpdateEventoAction;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventoController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filtros = $request->only(['busca', 'congregacao_id', 'situacao', 'de', 'ate']);

        $query = Evento::query()->with('congregacao');

        // Escopo forçado por congregação para líder
        if (!$user->role->veTodosEventos() && $user->congregacao_id) {
            $query->where('congregacao_id', $user->congregacao_id);
            $filtros['congregacao_id'] = $user->congregacao_id;
        }

        $query->filter($filtros);
        $eventos = $query->orderBy('dt_evento')->paginate(20)->withQueryString();

        $congregacoes = $user->role->veTodosEventos()
            ? Congregacao::orderBy('nome')->get()
            : Congregacao::where('id', $user->congregacao_id)->get();

        return view('eventos.index', compact('eventos', 'congregacoes', 'filtros'));
    }

    public function create(): View
    {
        $congregacoes = Congregacao::orderBy('nome')->get();
        $evento = new Evento();

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function store(StoreEventoRequest $request, CreateEventoAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    public function edit(Evento $evento): View
    {
        $this->authorize('update', $evento);
        $congregacoes = Congregacao::orderBy('nome')->get();

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function update(UpdateEventoRequest $request, Evento $evento, UpdateEventoAction $action): RedirectResponse
    {
        $action->execute($evento, $request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento atualizado com sucesso.');
    }

    public function destroy(Evento $evento, DeleteEventoAction $action): RedirectResponse
    {
        $this->authorize('delete', $evento);
        $action->execute($evento);

        return redirect()->route('eventos.index')
            ->with('success', 'Evento excluído com sucesso.');
    }
}
PHP_FILE;
    }

    private function userController(): string
    {
        return <<<'PHP_FILE'
<?php

namespace App\Http\Controllers;

use App\Domain\Users\Actions\CreateUserAction;
use App\Domain\Users\Actions\DeleteUserAction;
use App\Domain\Users\Actions\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('congregacao')->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);
        $congregacoes = Congregacao::orderBy('nome')->get();
        $user = new User();

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $congregacoes = Congregacao::orderBy('nome')->get();

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $action->execute($user, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        $this->authorize('delete', $user);
        $action->execute($user);

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
PHP_FILE;
    }

    // ==========================================================
    // ROUTES
    // ==========================================================

    private function routes(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('eventos', EventoController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);
});
PHP_FILE;
    }

    // ==========================================================
    // SEEDERS
    // ==========================================================

    private function congregacaoSeeder(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Seeders;

use App\Models\Congregacao;
use Illuminate\Database\Seeder;

class CongregacaoSeeder extends Seeder
{
    public function run(): void
    {
        $nomes = [
            'SEDE', 'TABATINGA', 'NOVO MARANGUAPE 1', 'NOVO MARANGUAPE 2',
            'NOVO MARANGUAPE 3', 'NOVO PARQUE IRACEMA', 'PARQUE SÃO JOÃO',
            'IRACEMA', 'LAMEIRÃO', 'LAGES', 'SITIO SÃO LUIZ',
            'VITÓRIA', 'JUBAIA',
        ];

        foreach ($nomes as $nome) {
            Congregacao::firstOrCreate(['nome' => $nome]);
        }
    }
}
PHP_FILE;
    }

    private function userSeeder(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Seeders;

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@adtc2.local'],
            [
                'name' => 'Administrador Geral',
                'password' => 'password',
                'role' => UserRole::AdminGeral,
                'email_verified_at' => now(),
            ]
        );

        $sede = Congregacao::where('nome', 'SEDE')->first();
        if ($sede) {
            User::firstOrCreate(
                ['email' => 'lider.sede@adtc2.local'],
                [
                    'name' => 'Líder Sede',
                    'password' => 'password',
                    'role' => UserRole::LiderCongregacao,
                    'congregacao_id' => $sede->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        User::firstOrCreate(
            ['email' => 'viewer@adtc2.local'],
            [
                'name' => 'Visualizador Demo',
                'password' => 'password',
                'role' => UserRole::Visualizador,
                'email_verified_at' => now(),
            ]
        );
    }
}
PHP_FILE;
    }

    private function eventoSeeder(): string
    {
        $eventos = $this->eventosData();

        return <<<PHP_FILE
<?php

namespace Database\Seeders;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        \$eventos = {$eventos};

        \$congregacoes = Congregacao::pluck('id', 'nome');
        \$situacoesValidas = array_column(SituacaoEvento::cases(), 'value');

        foreach (\$eventos as [\$evento, \$anotacoes, \$cong, \$dt, \$sit]) {
            if (!isset(\$congregacoes[\$cong])) {
                continue;
            }

            \$situacao = in_array(\$sit, \$situacoesValidas, true)
                ? \$sit
                : SituacaoEvento::Agendado->value;

            Evento::create([
                'evento' => \$evento,
                'anotacoes' => \$anotacoes ?: null,
                'congregacao_id' => \$congregacoes[\$cong],
                'dt_evento' => \$dt,
                'situacao' => \$situacao,
            ]);
        }
    }
}
PHP_FILE;
    }

    private function databaseSeeder(): string
    {
        return <<<'PHP_FILE'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CongregacaoSeeder::class,
            UserSeeder::class,
            EventoSeeder::class,
        ]);
    }
}
PHP_FILE;
    }

    private function eventosData(): string
    {
        return <<<'PHP_ARRAY'
[
    ['ANIVERSSÁRIO DIRIGENTE', 'ANIVERSSÁRIO DIRIGENTE LÍLIO GASPAR', 'TABATINGA', '2024-02-19', 'agendado'],
    ['CASAMENTO UNIFICADO', 'CASAMENTO UNIFICADO - PROJETO QUE VISA CASAR O MÁXIMO DE CASAIS.', 'TABATINGA', '2024-06-22', 'agendado'],
    ['ENCONTRO PARA CASAIS - ESTUDO', 'AUXÍLIO AOS CASAIS ANTES DO CASAMENTO.', 'TABATINGA', '2024-04-01', 'agendado'],
    ['Aniversario do Pastor Presidente', null, 'SEDE', '2024-01-24', 'agendado'],
    ['Aniversario de posse do Pastor Presidente', null, 'SEDE', '2024-07-29', 'agendado'],
    ['Simpósio de Liderança', 'Pr. Jayro Kaillo, organizado pela CEAD.', 'SEDE', '2024-03-10', 'agendado'],
    ['Terceiro Encontro de Mulheres ADTC2 - NM1', 'Tema: Mulheres Edificadas na Palavra.', 'NOVO MARANGUAPE 1', '2024-05-25', 'agendado'],
    ['Inauguração do Templo da Congregação de Iracema', 'Início às 14:00.', 'IRACEMA', '2024-01-07', 'agendado'],
    ['Aniversário do Conjunto de Adolescentes - KADOSH', null, 'NOVO MARANGUAPE 1', '2024-05-07', 'agendado'],
    ['Aniversário de posse do Presbítero Luciano Alves', '6 anos de posse, às 19h.', 'SITIO SÃO LUIZ', '2024-09-09', 'agendado'],
    ['Aniversário da congregação Lameirão', null, 'LAMEIRÃO', '2024-03-23', 'agendado'],
    ['CAPACITAÇÃO PARA OBREIROS', 'Tema: Obreiro obediente, vida avivada.', 'TABATINGA', '2024-03-04', 'agendado'],
    ['CONFERÊNCIA AVIVA-NOS', 'Conferência de Jovens do Campo.', 'SEDE', '2024-02-12', 'agendado'],
    ['Aniversário do Conjunto de Senhoras Brilho Celeste', 'Culto de Aniversário.', 'PARQUE SÃO JOÃO', '2024-08-31', 'agendado'],
    ['Festa Renovadas por Cristo', 'Culto inicia às 18:30h.', 'NOVO PARQUE IRACEMA', '2024-06-01', 'agendado'],
    ['1° Tela Crente da UNA', 'Confraternização com adolescentes.', 'SEDE', '2024-03-30', 'agendado'],
    ['1° Conferência de Adolescentes', 'Organização: UNA.', 'SEDE', '2024-06-29', 'agendado'],
    ['Culto Especial - Dia da Mulher', 'Dia da Mulher.', 'SEDE', '2024-03-13', 'agendado'],
    ['35 anos do Conjunto Maranata', null, 'SEDE', '2024-05-09', 'agendado'],
    ['Congresso Unificado de Mulheres', 'Campo.', 'SEDE', '2024-10-19', 'agendado'],
    ['Festa do Círculo de Oração', null, 'SEDE', '2024-11-13', 'agendado'],
    ['Festa de Adolescentes - Leão de Judá', 'Aniversário do conjunto.', 'NOVO PARQUE IRACEMA', '2024-09-14', 'agendado'],
    ['Festa do Círculo de Oração', null, 'LAMEIRÃO', '2024-04-30', 'agendado'],
    ['Aniversário do Conjunto de Senhoras', 'Festa de Senhoras.', 'LAMEIRÃO', '2024-09-21', 'agendado'],
    ['Palestra para Adolescentes', null, 'SEDE', '2024-03-09', 'agendado'],
    ['Aniversário do conjunto Shalom', null, 'LAGES', '2024-11-19', 'agendado'],
    ['Aniversário do conjunto Vasos de Honra', null, 'LAGES', '2024-09-28', 'agendado'],
    ['Tarde de louvor e adoração para a juventude', 'Realizado pelo conjunto Shalom.', 'LAGES', '2024-07-07', 'agendado'],
    ['Seminário para adolescentes', 'À tarde.', 'LAGES', '2024-10-19', 'agendado'],
    ['Tarde de avivamento', null, 'LAGES', '2024-04-20', 'agendado'],
    ['Festa das crianças e Tocha Viva', 'Dia das crianças.', 'LAGES', '2024-10-26', 'agendado'],
    ['JANTAR DOS NAMORADOS', 'Alvo: líderes de casais.', 'SEDE', '2024-06-15', 'agendado'],
    ['Culto de Ação de Graças - Severino de Sousa', 'Às 19h.', 'PARQUE SÃO JOÃO', '2024-05-05', 'agendado'],
    ['Congresso da Família', null, 'SEDE', '2024-08-23', 'realizado'],
    ['Festa do Departamento Infantil', null, 'NOVO MARANGUAPE 1', '2024-07-09', 'agendado'],
    ['Festa do Departamento de Senhoras', null, 'NOVO MARANGUAPE 1', '2024-07-27', 'agendado'],
    ['Convivência de Senhoras', null, 'NOVO MARANGUAPE 1', '2024-10-05', 'agendado'],
    ['Jantar dos namorados - Tabatinga', 'Apenas para a congregação.', 'TABATINGA', '2024-06-12', 'agendado'],
    ['Encontro de Casais - NM2', null, 'NOVO MARANGUAPE 2', '2024-10-05', 'agendado'],
    ['Cruzada Moriá', 'Evento evangelístico.', 'NOVO MARANGUAPE 2', '2024-09-21', 'agendado'],
    ['Congresso para casais', null, 'SEDE', '2024-10-25', 'agendado'],
    ['Culto de Senhoras', 'Presidentes: Betânia, Sharly.', 'SEDE', '2024-10-12', 'agendado'],
    ['Conjunto Perfeito Louvor', null, 'NOVO MARANGUAPE 2', '2024-11-02', 'agendado'],
    ['Chá de Mulheres - NM2', null, 'NOVO MARANGUAPE 2', '2024-11-16', 'agendado'],
    ['Culto de Jovens do Setor Isaías', 'Parque São João.', 'PARQUE SÃO JOÃO', '2024-09-02', 'agendado'],
    ['Aniversário do Pastor Antônio das Graças', 'Evento na residência.', 'SEDE', '2024-11-04', 'agendado'],
    ['Aniversário do conjunto Reino de Deus', 'Festa das crianças.', 'LAMEIRÃO', '2024-10-12', 'agendado'],
    ['Aniversário Natalício - Ev. Carlos Neto', 'Culto de Gratidão.', 'NOVO MARANGUAPE 1', '2024-10-14', 'agendado'],
    ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2024-09-07', 'agendado'],
    ['Festa 22 Anos Harmonia Celeste', 'Aniversário conjunto de jovens.', 'NOVO PARQUE IRACEMA', '2024-12-14', 'agendado'],
    ['Evento Evangelístico - Lameirão', 'Programação todo o dia.', 'LAMEIRÃO', '2024-11-23', 'agendado'],
    ['Reunião para os Dirigentes', 'Às 14h na casa do Pastor Presidente.', 'SEDE', '2024-11-30', 'agendado'],
    ['Reunião com a Liderança da Sede', 'Às 19h na Sede.', 'SEDE', '2024-12-02', 'agendado'],
    ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2025-01-05', 'agendado'],
    ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2025-09-07', 'agendado'],
    ['Festa de Posse do Pastor Presidente', null, 'SEDE', '2026-07-24', 'agendado'],
    ['Festa de Posse do Pastor Presidente', null, 'SEDE', '2025-07-23', 'agendado'],
    ['Aviva-nos', 'Juventude do campo. 03 e 04 de Março.', 'SEDE', '2025-03-03', 'agendado'],
    ['Aviva-nos', 'Juventude do campo.', 'SEDE', '2025-03-04', 'agendado'],
    ['Aniversário do Conjunto Acordes Celeste', '1 ano do departamento.', 'VITÓRIA', '2025-03-08', 'agendado'],
    ['CONFERÊNCIA DE ADOLESCENTES', 'Participação de todos os adolescentes.', 'SEDE', '2025-07-02', 'agendado'],
    ['2° Encontro de Mulheres', 'Com Aparecida Borges.', 'SEDE', '2025-06-21', 'agendado'],
    ['Workshop para Regentes', 'Treinamento e capacitação.', 'SEDE', '2025-05-24', 'agendado'],
    ['Capacitação para Professores de EBD', 'Todas as classes.', 'SEDE', '2025-04-12', 'agendado'],
    ['Festa do Conjunto de Crianças', 'Cordeirinhos de Cristo.', 'SEDE', '2025-12-17', 'agendado'],
    ['Encontro Para a Família', 'Palestras simultâneas.', 'SEDE', '2025-10-20', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-07', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-08', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-09', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-10', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-11', 'agendado'],
    ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-12', 'agendado'],
    ['Conferência Missionária', 'Encerramento.', 'SEDE', '2025-10-13', 'agendado'],
    ['Aniversário do conjunto de senhoras', 'Brilhando com Cristo.', 'TABATINGA', '2025-07-08', 'agendado'],
    ['Encontro de Mulheres - Tabatinga', 'Provérbios 31:25.', 'TABATINGA', '2025-04-22', 'agendado'],
    ['Simpósio de casais', null, 'TABATINGA', '2025-05-05', 'agendado'],
    ['Simpósio de casais', null, 'TABATINGA', '2025-05-06', 'agendado'],
    ['Simpósio de casais', null, 'TABATINGA', '2025-05-07', 'agendado'],
    ['Seminário Missionário', null, 'TABATINGA', '2025-07-09', 'agendado'],
    ['Seminário Missionário', null, 'TABATINGA', '2025-07-10', 'agendado'],
    ['Seminário Missionário', null, 'TABATINGA', '2025-07-11', 'agendado'],
    ['Cruzada Evangelística - Gererau', null, 'TABATINGA', '2025-07-12', 'agendado'],
    ['Congresso Missionário', null, 'TABATINGA', '2025-07-15', 'agendado'],
    ['Congresso Missionário', null, 'TABATINGA', '2025-07-16', 'agendado'],
    ['Congresso Missionário', null, 'TABATINGA', '2025-07-17', 'agendado'],
    ['Congresso Missionário', null, 'TABATINGA', '2025-07-18', 'agendado'],
    ['Cruzada Evangelística - Sapupara', null, 'TABATINGA', '2025-07-19', 'agendado'],
    ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-07', 'agendado'],
    ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-08', 'agendado'],
    ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-09', 'agendado'],
    ['Encontro de Casais - Tabatinga', null, 'TABATINGA', '2025-06-12', 'agendado'],
    ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-12', 'agendado'],
    ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-13', 'agendado'],
    ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-14', 'agendado'],
    ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-15', 'agendado'],
    ['Chá de mulheres - NM1', '1° chá de 2025, às 15:30h.', 'NOVO MARANGUAPE 1', '2025-04-19', 'agendado'],
    ['Festa de crianças - NM1', null, 'NOVO MARANGUAPE 1', '2025-10-27', 'agendado'],
    ['Festa de adolescentes - NM1', null, 'NOVO MARANGUAPE 1', '2025-06-24', 'agendado'],
    ['Festa das Crianças - Novo Parque Iracema', null, 'NOVO PARQUE IRACEMA', '2025-08-11', 'agendado'],
    ['Palestra - Sede', 'Das 15h às 16h.', 'SEDE', '2025-03-22', 'agendado'],
    ['Chá com Elas - NM2', 'Das 15h às 16h.', 'NOVO MARANGUAPE 2', '2025-03-15', 'agendado'],
    ['Seminário Para Obreiros - Lages', null, 'LAGES', '2025-05-10', 'agendado'],
    ['Aniversário de Posse e Natalício - Ev. René', null, 'LAGES', '2025-04-28', 'agendado'],
    ['23° Aniversário de Senhoras', 'Renovadas por Cristo.', 'NOVO PARQUE IRACEMA', '2025-10-14', 'agendado'],
    ['Cruzada na SEDE', 'Cruzada evangelística, às 19h.', 'SEDE', '2026-08-22', 'agendado'],
    ['Congresso de missões - Lameirão', '1º congresso de missões.', 'LAMEIRÃO', '2026-11-21', 'agendado'],
    ['Congresso de missões - Lameirão', '1º congresso de missões.', 'LAMEIRÃO', '2026-11-22', 'agendado'],
    ['Culto festivo 24 anos do departamento de jovens', 'Rua Oliveira Cavalcante, 167.', 'NOVO MARANGUAPE 1', '2026-08-22', 'agendado'],
    ['Culto de ação de graças - Ev. Carlos Neto', 'Aniversário de posse e natalício.', 'NOVO MARANGUAPE 1', '2026-10-05', 'agendado'],
    ['Culto festivo 24 anos do círculo de oração', 'Rua Oliveira Cavalcante, 167.', 'NOVO MARANGUAPE 1', '2026-12-01', 'agendado'],
    ['Culto festivo 21 anos Brilho Celeste', 'Às 18:30h.', 'PARQUE SÃO JOÃO', '2026-10-20', 'agendado'],
    ['TARDE DE LOUVOR - SENHORAS', 'Todo o campo, às 14h.', 'SEDE', '2026-10-31', 'agendado'],
    ['CÍRCULO DE ORAÇÃO - Nível Campo', null, 'SEDE', '2026-12-28', 'agendado'],
    ['RETIRO DE JOVENS', 'Nível campo. 31/out a 02/nov.', 'SEDE', '2026-10-31', 'agendado'],
    ['RETIRO DE JOVENS', 'Nível campo.', 'SEDE', '2026-11-01', 'agendado'],
    ['RETIRO DE JOVENS', 'Nível campo.', 'SEDE', '2026-11-02', 'agendado'],
    ['ANIVERSÁRIO DO CONJUNTO FONTES DE ÁGUAS VIVAS', 'Nível campo.', 'SEDE', '2026-08-05', 'agendado'],
    ['ANIVERSÁRIO DO CONJUNTO HERDEIROS DO REINO', 'Todo o campo convocado.', 'SEDE', '2026-12-16', 'agendado'],
    ['ENCONTRO DE CASAIS - SEDE', 'Somente para a Sede.', 'SEDE', '2026-08-15', 'agendado'],
    ['CONGRESSO DA FAMÍLIA - SEDE', '13 e 14 de novembro.', 'SEDE', '2026-11-13', 'agendado'],
    ['CONGRESSO DA FAMÍLIA - SEDE', '13 e 14 de novembro.', 'SEDE', '2026-11-14', 'agendado'],
    ['Convivência de Jovens - Vitória', 'Parceria entre congregações.', 'VITÓRIA', '2026-09-19', 'agendado'],
    ['Congresso Avivamento e Restauração', '31/out a 02/nov.', 'LAGES', '2026-10-31', 'agendado'],
    ['Convivência das Filhas de Sião', 'Liderança do departamento.', 'NOVO MARANGUAPE 1', '2026-10-24', 'agendado'],
    ['Culto festivo 9 anos infantil', 'Cordeirinhos de Cristo.', 'NOVO MARANGUAPE 3', '2026-10-24', 'agendado'],
    ['Festa de Jovens - 32 anos Brilho Celeste', '32° Aniversário.', 'VITÓRIA', '2026-11-23', 'agendado'],
    ['REUNIÃO LIDERANÇA DA UCADEM', 'Líderes de crianças.', 'SEDE', '2026-12-05', 'agendado'],
    ['Cruzada: Jubaia para Cristo', 'Às 19h.', 'JUBAIA', '2026-10-17', 'agendado'],
    ['Confraternização de Dirigentes e Esposas', 'Em Jubaia.', 'SEDE', '2026-12-12', 'agendado'],
    ['Congresso de Crianças - Jubaia', 'Às 18h.', 'JUBAIA', '2026-11-28', 'agendado'],
]
PHP_ARRAY;
    }

    // ==========================================================
    // VIEWS
    // ==========================================================

    private function layoutView(): string
    {
        return <<<'PHP_FILE'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADTC2 Eventos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-indigo-700">📅 ADTC2</a>
                @auth
                    <div class="hidden md:flex gap-4 text-sm">
                        <a href="{{ route('dashboard') }}"
                           class="{{ request()->routeIs('dashboard') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('eventos.index') }}"
                           class="{{ request()->routeIs('eventos.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                            Eventos
                        </a>
                        @if(auth()->user()->isAdminGeral())
                            <a href="{{ route('users.index') }}"
                               class="{{ request()->routeIs('users.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                                Usuários
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
            @auth
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                        {{ auth()->user()->role->label() }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-red-600 hover:underline">Sair</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
PHP_FILE;
    }

    private function loginView(): string
    {
        return <<<'PHP_FILE'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login — ADTC2 Eventos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded shadow p-8 w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center text-indigo-700">📅 ADTC2</h1>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border-gray-300 rounded px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password" required
                       class="w-full border-gray-300 rounded px-3 py-2 border">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember"> Lembrar-me
            </label>
            <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                Entrar
            </button>
        </form>

        <div class="mt-4 text-xs text-gray-500 text-center space-y-1">
            <p>Demo:</p>
            <p>admin@adtc2.local / password</p>
            <p>lider.sede@adtc2.local / password</p>
            <p>viewer@adtc2.local / password</p>
        </div>
    </div>
</body>
</html>
PHP_FILE;
    }

    private function dashboardView(): string
    {
        return <<<'PHP_FILE'
@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

    {{-- Cards de totais --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $totais['total'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Próximos 30d</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $totais['proximos30'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Agendados</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totais['agendado'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Realizados</div>
            <div class="text-2xl font-bold text-green-600">{{ $totais['realizado'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Cancelados</div>
            <div class="text-2xl font-bold text-red-600">{{ $totais['cancelado'] }}</div>
        </div>
    </div>

    {{-- Próximos 7 dias --}}
    <div class="bg-white rounded shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">🗓️ Próximos 7 dias</h2>
        @if($proximos7->isEmpty())
            <p class="text-gray-500 text-sm">Nenhum evento nos próximos 7 dias.</p>
        @else
            <div class="space-y-2">
                @foreach($proximos7 as $ev)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <span class="font-medium text-gray-800">{{ $ev->evento }}</span>
                            <span class="text-xs text-gray-500 ml-2">· {{ $ev->congregacao->nome }}</span>
                        </div>
                        <div class="flex gap-2 items-center">
                            <span class="px-2 py-1 rounded text-xs {{ $ev->situacao->cor() }}">{{ $ev->situacao->label() }}</span>
                            <span class="text-sm text-gray-700">{{ $ev->dt_evento->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        {{-- Próximos eventos --}}
        <div class="md:col-span-2 bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">⏭️ Próximos eventos</h2>
            @if($proximosEventos->isEmpty())
                <p class="text-gray-500 text-sm">Nenhum evento futuro.</p>
            @else
                <div class="space-y-2">
                    @foreach($proximosEventos as $ev)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <span class="font-medium text-gray-800">{{ $ev->evento }}</span>
                                <span class="text-xs text-gray-500 ml-2">· {{ $ev->congregacao->nome }}</span>
                            </div>
                            <span class="text-sm text-gray-700">{{ $ev->dt_evento->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top congregações --}}
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🏛️ Top congregações</h2>
            <div class="space-y-2">
                @foreach($congregacoes as $cong)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $cong->nome }}</span>
                        <span class="text-gray-500">{{ $cong->eventos_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
PHP_FILE;
    }

    private function indexView(): string
    {
        return <<<'PHP_FILE'
@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Eventos</h1>
        @can('create', App\Models\Evento::class)
            <a href="{{ route('eventos.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Novo Evento
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('eventos.index') }}" class="bg-white rounded shadow p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="busca" value="{{ $filtros['busca'] ?? '' }}"
                   placeholder="Buscar evento ou anotação..."
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">

            <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
                <option value="">Todas as congregações</option>
                @foreach($congregacoes as $c)
                    <option value="{{ $c->id }}" @selected(($filtros['congregacao_id'] ?? '') == $c->id)>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>

            <select name="situacao" class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
                <option value="">Todas as situações</option>
                @foreach(\App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                    <option value="{{ $s->value }}" @selected(($filtros['situacao'] ?? '') === $s->value)>
                        {{ $s->label() }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="de" value="{{ $filtros['de'] ?? '' }}"
                   placeholder="De"
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">

            <input type="date" name="ate" value="{{ $filtros['ate'] ?? '' }}"
                   placeholder="Até"
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
        </div>
        <div class="flex gap-2 mt-3">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                Filtrar
            </button>
            <a href="{{ route('eventos.index') }}"
               class="px-4 py-2 rounded border border-gray-300 text-sm hover:bg-gray-50">
                Limpar
            </a>
            <span class="ml-auto text-xs text-gray-500 self-center">
                {{ $eventos->total() }} evento(s) encontrado(s)
            </span>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Congregação</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($eventos as $evento)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $evento->evento }}</div>
                            @if($evento->anotacoes)
                                <div class="text-xs text-gray-500 truncate max-w-md">{{ $evento->anotacoes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $evento->congregacao->nome }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $evento->dt_evento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $evento->situacao->cor() }}">
                                {{ $evento->situacao->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            @can('update', $evento)
                                <a href="{{ route('eventos.edit', $evento) }}"
                                   class="text-indigo-600 hover:underline">Editar</a>
                            @endcan
                            @can('delete', $evento)
                                <form action="{{ route('eventos.destroy', $evento) }}" method="POST"
                                      class="inline ml-2"
                                      onsubmit="return confirm('Excluir este evento?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum evento encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $eventos->links() }}</div>
@endsection
PHP_FILE;
    }

    private function formView(): string
    {
        return <<<'PHP_FILE'
@extends('layouts.app')

@section('content')
    @php $isEdit = $evento->exists; @endphp

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ $isEdit ? 'Editar Evento' : 'Novo Evento' }}
    </h1>

    <form action="{{ $isEdit ? route('eventos.update', $evento) : route('eventos.store') }}"
          method="POST"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Evento *</label>
            <input type="text" name="evento" value="{{ old('evento', $evento->evento) }}"
                   class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
            @error('evento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Anotações</label>
            <textarea name="anotacoes" rows="4"
                      class="w-full border-gray-300 rounded px-3 py-2 border">{{ old('anotacoes', $evento->anotacoes) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Congregação *</label>
                <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border" required>
                    <option value="">Selecione</option>
                    @foreach($congregacoes as $c)
                        <option value="{{ $c->id }}"
                            @selected(old('congregacao_id', $evento->congregacao_id) == $c->id)>
                            {{ $c->nome }}
                        </option>
                    @endforeach
                </select>
                @error('congregacao_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                <input type="date" name="dt_evento"
                       value="{{ old('dt_evento', $evento->dt_evento?->format('Y-m-d')) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required>
                @error('dt_evento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Situação *</label>
                <select name="situacao" class="w-full border-gray-300 rounded px-3 py-2 border" required>
                    @foreach(App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                        <option value="{{ $s->value }}"
                            @selected(old('situacao', $evento->situacao?->value ?? 'agendado') === $s->value)>
                            {{ $s->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                {{ $isEdit ? 'Atualizar' : 'Criar' }}
            </button>
            <a href="{{ route('eventos.index') }}"
               class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
@endsection
PHP_FILE;
    }

    private function usersIndexView(): string
    {
        return <<<'PHP_FILE'
@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
        <a href="{{ route('users.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Novo Usuário
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Congregação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="ml-2 text-xs text-indigo-600 font-semibold">(você)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                                {{ $u->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->congregacao?->nome ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('users.edit', $u) }}"
                               class="text-indigo-600 hover:underline">Editar</a>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST"
                                      class="inline ml-2"
                                      onsubmit="return confirm('Excluir este usuário?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
PHP_FILE;
    }

    private function usersFormView(): string
    {
        return <<<'PHP_FILE'
@extends('layouts.app')

@section('content')
    @php $isEdit = $user->exists; @endphp

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ $isEdit ? 'Editar Usuário' : 'Novo Usuário' }}
    </h1>

    <form action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}"
          method="POST"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Senha {{ $isEdit ? '(deixe em branco para manter)' : '*' }}
                </label>
                <input type="password" name="password"
                       class="w-full border-gray-300 rounded px-3 py-2 border"
                       @if(!$isEdit) required @endif minlength="6">
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil *</label>
                <select name="role" class="w-full border-gray-300 rounded px-3 py-2 border" required
                        onchange="document.getElementById('campo-cong').style.display = this.value === 'lider_congregacao' ? 'block' : 'none';">
                    @foreach(App\Domain\Users\Enums\UserRole::cases() as $r)
                        <option value="{{ $r->value }}"
                            @selected(old('role', $user->role?->value ?? 'visualizador') === $r->value)>
                            {{ $r->label() }}
                        </option>
                    @endforeach
                </select>
                @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div id="campo-cong"
             style="display: {{ old('role', $user->role?->value ?? '') === 'lider_congregacao' ? 'block' : 'none' }};">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Congregação (obrigatória para líder)
            </label>
            <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border">
                <option value="">Selecione</option>
                @foreach($congregacoes as $c)
                    <option value="{{ $c->id }}"
                        @selected(old('congregacao_id', $user->congregacao_id) == $c->id)>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>
            @error('congregacao_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                {{ $isEdit ? 'Atualizar' : 'Criar' }}
            </button>
            <a href="{{ route('users.index') }}"
               class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
@endsection
PHP_FILE;
    }

    // ==========================================================
    // TESTS
    // ==========================================================

    private function pestConfig(): string
    {
        return <<<'PHP_FILE'
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(fn () => $this->withoutVite())
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->in('Unit');
PHP_FILE;
    }

    private function enumsTest(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Domain\Users\Enums\UserRole;

it('admin e líder podem gerenciar eventos', function () {
    expect(UserRole::AdminGeral->podeGerenciarEventos())->toBeTrue()
        ->and(UserRole::LiderCongregacao->podeGerenciarEventos())->toBeTrue()
        ->and(UserRole::Visualizador->podeGerenciarEventos())->toBeFalse();
});

it('apenas admin vê todos os eventos', function () {
    expect(UserRole::AdminGeral->veTodosEventos())->toBeTrue()
        ->and(UserRole::LiderCongregacao->veTodosEventos())->toBeFalse();
});

it('apenas admin gerencia usuários', function () {
    expect(UserRole::AdminGeral->podeGerenciarUsuarios())->toBeTrue()
        ->and(UserRole::LiderCongregacao->podeGerenciarUsuarios())->toBeFalse()
        ->and(UserRole::Visualizador->podeGerenciarUsuarios())->toBeFalse();
});

it('enum de situação retorna labels corretos', function () {
    expect(SituacaoEvento::Agendado->label())->toBe('Agendado')
        ->and(SituacaoEvento::Realizado->label())->toBe('Realizado')
        ->and(SituacaoEvento::Cancelado->label())->toBe('Cancelado');
});
PHP_FILE;
    }

    private function authTest(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Models\User;

use function Pest\Laravel\{get, post};

it('redireciona guest para login ao acessar dashboard', function () {
    get(route('dashboard'))->assertRedirect(route('login'));
});

it('redireciona guest para login ao acessar eventos', function () {
    get(route('eventos.index'))->assertRedirect(route('login'));
});

it('usuário pode fazer login com credenciais válidas', function () {
    $user = User::factory()->admin()->create(['password' => 'secret']);

    post(route('login'), ['email' => $user->email, 'password' => 'secret'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('não autentica com senha errada', function () {
    $user = User::factory()->create();

    post(route('login'), ['email' => $user->email, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('usuário autenticado pode fazer logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});
PHP_FILE;
    }

    private function eventoTest(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

use function Pest\Laravel\{actingAs, delete, get, post, put};

it('admin pode listar todos os eventos', function () {
    $admin = User::factory()->admin()->create();
    Evento::factory()->count(3)->create();

    actingAs($admin)->get(route('eventos.index'))
        ->assertOk()
        ->assertSee('Eventos');
});

it('líder vê apenas eventos da própria congregação', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create(['evento' => 'EVENTO_A', 'congregacao_id' => $congA->id]);
    Evento::factory()->create(['evento' => 'EVENTO_B', 'congregacao_id' => $congB->id]);

    actingAs($lider)->get(route('eventos.index'))
        ->assertSee('EVENTO_A')
        ->assertDontSee('EVENTO_B');
});

it('visualizador não pode criar evento', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)->post(route('eventos.store'), [])
        ->assertForbidden();
});

it('admin pode criar evento', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)->post(route('eventos.store'), [
        'evento' => 'Novo Evento',
        'congregacao_id' => $cong->id,
        'dt_evento' => '2026-12-25',
        'situacao' => SituacaoEvento::Agendado->value,
    ])->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', ['evento' => 'Novo Evento']);
});

it('líder não pode editar evento de outra congregação', function () {
    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();
    $lider = User::factory()->lider($congA)->create();
    $evento = Evento::factory()->create(['congregacao_id' => $congB->id]);

    actingAs($lider)->put(route('eventos.update', $evento), [
        'evento' => 'Hack',
        'congregacao_id' => $congB->id,
        'dt_evento' => '2026-01-01',
        'situacao' => SituacaoEvento::Agendado->value,
    ])->assertForbidden();
});

it('apenas admin pode excluir evento', function () {
    $lider = User::factory()->lider()->create();
    $admin = User::factory()->admin()->create();
    $evento = Evento::factory()->create();

    actingAs($lider)->delete(route('eventos.destroy', $evento))->assertForbidden();
    actingAs($admin)->delete(route('eventos.destroy', $evento))->assertRedirect();
    $this->assertDatabaseMissing('eventos', ['id' => $evento->id]);
});

it('admin pode filtrar por congregação', function () {
    $admin = User::factory()->admin()->create();
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);

    Evento::factory()->create(['evento' => 'EVENTO_A', 'congregacao_id' => $congA->id]);
    Evento::factory()->create(['evento' => 'EVENTO_B', 'congregacao_id' => $congB->id]);

    actingAs($admin)->get(route('eventos.index', ['congregacao_id' => $congA->id]))
        ->assertSee('EVENTO_A')
        ->assertDontSee('EVENTO_B');
});

it('admin pode filtrar por situação', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'AGENDADO_XYZ',
        'congregacao_id' => $cong->id,
        'situacao' => SituacaoEvento::Agendado,
    ]);
    Evento::factory()->create([
        'evento' => 'REALIZADO_XYZ',
        'congregacao_id' => $cong->id,
        'situacao' => SituacaoEvento::Realizado,
    ]);

    actingAs($admin)->get(route('eventos.index', ['situacao' => 'agendado']))
        ->assertSee('AGENDADO_XYZ')
        ->assertDontSee('REALIZADO_XYZ');
});

it('admin pode buscar por texto no nome do evento', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create(['evento' => 'Casamento Coletivo', 'congregacao_id' => $cong->id]);
    Evento::factory()->create(['evento' => 'Batismo nas águas', 'congregacao_id' => $cong->id]);

    actingAs($admin)->get(route('eventos.index', ['busca' => 'Casamento']))
        ->assertSee('Casamento Coletivo')
        ->assertDontSee('Batismo nas águas');
});

it('líder não vê eventos de outra congregação mesmo filtrando por ela', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create(['evento' => 'EVENTO_B_SECRETO', 'congregacao_id' => $congB->id]);

    actingAs($lider)->get(route('eventos.index', ['congregacao_id' => $congB->id]))
        ->assertDontSee('EVENTO_B_SECRETO');
});
PHP_FILE;
    }

    private function dashboardTest(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('admin vê próximos eventos no dashboard', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_FUTURO_ABC',
        'congregacao_id' => $cong->id,
        'dt_evento' => now()->addDays(3),
        'situacao' => 'agendado',
    ]);

    actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('EVENTO_FUTURO_ABC');
});

it('líder vê apenas sua congregação no dashboard', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_A_DASH',
        'congregacao_id' => $congA->id,
        'dt_evento' => now()->addDays(2),
    ]);
    Evento::factory()->create([
        'evento' => 'EVENTO_B_DASH',
        'congregacao_id' => $congB->id,
        'dt_evento' => now()->addDays(2),
    ]);

    actingAs($lider)->get(route('dashboard'))
        ->assertSee('EVENTO_A_DASH')
        ->assertDontSee('EVENTO_B_DASH');
});
PHP_FILE;
    }

    private function userTest(): string
    {
        return <<<'PHP_FILE'
<?php

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use App\Models\User;

use function Pest\Laravel\{actingAs, delete, get, post, put};

it('admin pode listar usuários', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Fulano Teste']);

    actingAs($admin)->get(route('users.index'))
        ->assertOk()
        ->assertSee('Fulano Teste');
});

it('líder não pode listar usuários', function () {
    $lider = User::factory()->lider()->create();

    actingAs($lider)->get(route('users.index'))->assertForbidden();
});

it('visualizador não pode listar usuários', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)->get(route('users.index'))->assertForbidden();
});

it('admin pode criar usuário líder', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)->post(route('users.store'), [
        'name' => 'Novo Líder',
        'email' => 'novo.lider@adtc2.local',
        'password' => 'senha123',
        'role' => UserRole::LiderCongregacao->value,
        'congregacao_id' => $cong->id,
    ])->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'novo.lider@adtc2.local',
        'role' => UserRole::LiderCongregacao->value,
        'congregacao_id' => $cong->id,
    ]);
});

it('admin não pode excluir a si mesmo', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)->delete(route('users.destroy', $admin))->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('admin pode atualizar usuário sem trocar senha', function () {
    $admin = User::factory()->admin()->create();
    $alvo = User::factory()->visualizador()->create([
        'name' => 'Nome Antigo',
        'password' => bcrypt('senha_original'),
    ]);

    actingAs($admin)->put(route('users.update', $alvo), [
        'name' => 'Nome Novo',
        'email' => $alvo->email,
        'role' => UserRole::Visualizador->value,
        // password em branco = manter
    ])->assertRedirect(route('users.index'));

    $alvo->refresh();
    expect($alvo->name)->toBe('Nome Novo')
        ->and(Hash::check('senha_original', $alvo->password))->toBeTrue();
});
PHP_FILE;
    }
}  