#!/bin/bash
# ============================================================
# 求人LP自動生成システム - Laravelファイル一括配置スクリプト
# 実行場所: ~/kyujin-system（Laravelプロジェクトのルート）
# 実行方法: bash setup_laravel_files.sh
# ============================================================

set -e
echo "=========================================="
echo " ファイル配置を開始します..."
echo "=========================================="

# ============================================================
# Migration ファイル
# ============================================================
MIGRATION_DIR="database/migrations"
echo ""
echo "[1/2] Migrationファイルを配置中..."

cat > $MIGRATION_DIR/2026_04_18_000001_create_master_areas_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_areas', function (Blueprint $table) {
            $table->id();
            $table->string('prefecture');
            $table->string('region');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_areas');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000002_create_master_job_types_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_job_types', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_job_types');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000003_create_master_employment_types_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_employment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_employment_types');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000004_create_master_conditions_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->string('question_text')->nullable();
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_conditions');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000005_create_master_appeals_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_appeals', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name');
            $table->string('question_text')->nullable();
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_appeals');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000006_create_jobs_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('master_areas');
            $table->foreignId('job_type_id')->constrained('master_job_types');
            $table->foreignId('employment_type_id')->constrained('master_employment_types');
            $table->string('title');
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->text('description_generated')->nullable();
            $table->string('status')->default('draft');
            $table->string('token', 64)->unique();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000007_create_job_conditions_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('master_conditions');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['job_id', 'condition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_conditions');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000008_create_job_appeals_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('appeal_id')->constrained('master_appeals');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['job_id', 'appeal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_appeals');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000009_create_billing_agreements_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->boolean('agreement_flag');
            $table->text('agreement_text');
            $table->string('agreement_text_version');
            $table->timestamp('agreed_at');
            $table->string('agreed_ip', 45);
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_agreements');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000010_create_applications_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings');
            $table->string('application_type');
            $table->string('applicant_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('status')->default('received');
            $table->timestamp('applied_at');
            $table->timestamps();
            $table->index(['job_id', 'application_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000011_create_line_application_details_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_application_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('line_user_id')->nullable();
            $table->string('line_session_id')->nullable();
            $table->string('available_from')->nullable();
            $table->boolean('experience_flag')->nullable();
            $table->text('preferred_conditions_summary')->nullable();
            $table->string('area')->nullable();
            $table->json('raw_answers_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_application_details');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000012_create_form_application_details_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_application_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->text('appeal_message');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_application_details');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000013_create_form_desired_job_types_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_desired_job_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('job_type_id')->constrained('master_job_types');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'job_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_desired_job_types');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000014_create_form_desired_conditions_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_desired_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('master_conditions');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'condition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_desired_conditions');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000015_create_billings_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings');
            $table->foreignId('application_id')->constrained('applications');
            $table->string('application_type');
            $table->string('billing_trigger_type')->default('application_completed');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('JPY');
            $table->timestamp('billed_at');
            $table->string('agreement_version');
            $table->string('billing_status')->default('pending');
            $table->string('notification_status')->default('unsent');
            $table->timestamp('created_at')->useCurrent();
            $table->unique('application_id');
            $table->index('billing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000016_create_application_notifications_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications');
            $table->string('sent_to');
            $table->string('send_status');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['application_id', 'send_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_notifications');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000017_create_email_verification_tokens_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('email');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['job_id', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_tokens');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000018_create_audit_logs_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('action_type');
            $table->string('actor_type');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->json('action_payload');
            $table->string('payload_hash');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['entity_type', 'entity_id']);
            $table->index('action_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000019_create_line_condition_answers_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_condition_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('master_conditions');
            $table->string('answer');
            $table->string('answer_text')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'condition_id']);
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_condition_answers');
    }
};
EOF

cat > $MIGRATION_DIR/2026_04_18_000020_create_line_entry_tokens_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_entry_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('line_user_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['token', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_entry_tokens');
    }
};
EOF

echo "  ✅ Migrationファイル 20件 配置完了"

# ============================================================
# Model ファイル
# ============================================================
MODEL_DIR="app/Models"
echo ""
echo "[2/2] Modelファイルを配置中..."

cat > $MODEL_DIR/MasterArea.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterArea extends Model
{
    public $timestamps = false;
    protected $fillable = ['prefecture', 'region', 'name', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'area_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByPrefecture($q, string $prefecture) { return $q->where('prefecture', $prefecture); }
}
EOF

cat > $MODEL_DIR/MasterJobType.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJobType extends Model
{
    public $timestamps = false;
    protected $fillable = ['category', 'name', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'job_type_id'); }
    public function formDesiredJobTypes(): HasMany { return $this->hasMany(FormDesiredJobType::class, 'job_type_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
}
EOF

cat > $MODEL_DIR/MasterEmploymentType.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterEmploymentType extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'employment_type_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
EOF

cat > $MODEL_DIR/MasterCondition.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterCondition extends Model
{
    public $timestamps = false;
    protected $fillable = ['category', 'name', 'question_text', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobConditions(): HasMany { return $this->hasMany(JobCondition::class, 'condition_id'); }
    public function formDesiredConditions(): HasMany { return $this->hasMany(FormDesiredCondition::class, 'condition_id'); }
    public function lineConditionAnswers(): HasMany { return $this->hasMany(LineConditionAnswer::class, 'condition_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
    public function scopeHasQuestion($q) { return $q->whereNotNull('question_text'); }
}
EOF

cat > $MODEL_DIR/MasterAppeal.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAppeal extends Model
{
    public $timestamps = false;
    protected $fillable = ['category', 'name', 'question_text', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobAppeals(): HasMany { return $this->hasMany(JobAppeal::class, 'appeal_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
    public function scopeHasQuestion($q) { return $q->whereNotNull('question_text'); }
}
EOF

cat > $MODEL_DIR/Job.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'job_listings';
    protected $fillable = ['area_id','job_type_id','employment_type_id','title','seo_title','meta_description','description_generated','status','token','contact_email','contact_phone'];

    const STATUS_DRAFT  = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_CLOSED = 'closed';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Job $job) {
            if (empty($job->token)) { $job->token = Str::random(64); }
        });
    }

    public function area(): BelongsTo { return $this->belongsTo(MasterArea::class, 'area_id'); }
    public function jobType(): BelongsTo { return $this->belongsTo(MasterJobType::class, 'job_type_id'); }
    public function employmentType(): BelongsTo { return $this->belongsTo(MasterEmploymentType::class, 'employment_type_id'); }
    public function jobConditions(): HasMany { return $this->hasMany(JobCondition::class); }
    public function jobAppeals(): HasMany { return $this->hasMany(JobAppeal::class); }
    public function billingAgreement(): HasOne { return $this->hasOne(BillingAgreement::class); }
    public function applications(): HasMany { return $this->hasMany(Application::class); }
    public function billings(): HasMany { return $this->hasMany(Billing::class); }
    public function emailVerificationTokens(): HasMany { return $this->hasMany(EmailVerificationToken::class); }
    public function lineEntryTokens(): HasMany { return $this->hasMany(LineEntryToken::class); }

    public function scopeActive($q) { return $q->where('status', self::STATUS_ACTIVE); }
    public function scopeByToken($q, string $token) { return $q->where('token', $token); }
    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function hasValidAgreement(): bool { return $this->billingAgreement()->exists(); }
}
EOF

cat > $MODEL_DIR/JobCondition.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCondition extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'condition_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
}
EOF

cat > $MODEL_DIR/JobAppeal.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAppeal extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'appeal_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function appeal(): BelongsTo { return $this->belongsTo(MasterAppeal::class, 'appeal_id'); }
}
EOF

cat > $MODEL_DIR/BillingAgreement.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingAgreement extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id','agreement_flag','agreement_text','agreement_text_version','agreed_at','agreed_ip','user_agent'];
    protected $casts = ['agreement_flag' => 'boolean', 'agreed_at' => 'datetime'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
}
EOF

cat > $MODEL_DIR/Application.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = ['job_id','application_type','applicant_name','phone','email','status','applied_at'];
    protected $casts = ['applied_at' => 'datetime'];

    const TYPE_LINE = 'line';
    const TYPE_FORM = 'form';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CLOSED    = 'closed';

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function lineDetail(): HasOne { return $this->hasOne(LineApplicationDetail::class); }
    public function formDetail(): HasOne { return $this->hasOne(FormApplicationDetail::class); }
    public function formDesiredJobTypes(): HasMany { return $this->hasMany(FormDesiredJobType::class); }
    public function formDesiredConditions(): HasMany { return $this->hasMany(FormDesiredCondition::class); }
    public function lineConditionAnswers(): HasMany { return $this->hasMany(LineConditionAnswer::class); }
    public function billing(): HasOne { return $this->hasOne(Billing::class); }
    public function notifications(): HasMany { return $this->hasMany(ApplicationNotification::class); }

    public function scopeLine($q) { return $q->where('application_type', self::TYPE_LINE); }
    public function scopeForm($q) { return $q->where('application_type', self::TYPE_FORM); }
    public function isLine(): bool { return $this->application_type === self::TYPE_LINE; }
    public function isForm(): bool { return $this->application_type === self::TYPE_FORM; }
    public function isBilled(): bool { return $this->billing()->exists(); }
}
EOF

cat > $MODEL_DIR/LineApplicationDetail.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineApplicationDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id','line_user_id','line_session_id','available_from','experience_flag','preferred_conditions_summary','area','raw_answers_json'];
    protected $casts = ['experience_flag' => 'boolean', 'raw_answers_json' => 'array'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
}
EOF

cat > $MODEL_DIR/LineConditionAnswer.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineConditionAnswer extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'condition_id', 'answer', 'answer_text'];

    const ANSWER_YES     = 'yes';
    const ANSWER_CONSULT = 'consult';
    const ANSWER_OTHER   = 'other';

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
    public function isAgreed(): bool { return $this->answer === self::ANSWER_YES; }
}
EOF

cat > $MODEL_DIR/LineEntryToken.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LineEntryToken extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'token', 'line_user_id', 'expires_at', 'used_at'];
    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (LineEntryToken $r) {
            if (empty($r->token)) { $r->token = Str::random(64); }
            if (empty($r->expires_at)) { $r->expires_at = now()->addMinutes(30); }
        });
    }

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function isValid(): bool { return is_null($this->used_at) && $this->expires_at->isFuture(); }
    public function markAsUsed(string $lineUserId): void { $this->update(['used_at' => now(), 'line_user_id' => $lineUserId]); }
}
EOF

cat > $MODEL_DIR/FormApplicationDetail.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormApplicationDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'appeal_message', 'ip_address', 'user_agent'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
}
EOF

cat > $MODEL_DIR/FormDesiredJobType.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredJobType extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'job_type_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function jobType(): BelongsTo { return $this->belongsTo(MasterJobType::class, 'job_type_id'); }
}
EOF

cat > $MODEL_DIR/FormDesiredCondition.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredCondition extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'condition_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
}
EOF

cat > $MODEL_DIR/Billing.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id','application_id','application_type','billing_trigger_type','amount','currency','billed_at','agreement_version','billing_status','notification_status'];
    protected $casts = ['billed_at' => 'datetime', 'amount' => 'integer'];

    const BILLING_STATUS_PENDING = 'pending';
    const BILLING_STATUS_BILLED  = 'billed';
    const BILLING_STATUS_PAID    = 'paid';
    const NOTIFICATION_STATUS_UNSENT = 'unsent';
    const NOTIFICATION_STATUS_SENT   = 'sent';
    const NOTIFICATION_STATUS_FAILED = 'failed';

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function scopePending($q) { return $q->where('billing_status', self::BILLING_STATUS_PENDING); }
}
EOF

cat > $MODEL_DIR/ApplicationNotification.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationNotification extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'sent_to', 'send_status', 'error_message', 'sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    const STATUS_SENT   = 'sent';
    const STATUS_FAILED = 'failed';

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function scopeFailed($q) { return $q->where('send_status', self::STATUS_FAILED); }
}
EOF

cat > $MODEL_DIR/EmailVerificationToken.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailVerificationToken extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'token', 'email', 'expires_at', 'used_at'];
    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (EmailVerificationToken $r) {
            if (empty($r->token)) { $r->token = Str::random(64); }
            if (empty($r->expires_at)) { $r->expires_at = now()->addMinutes(30); }
        });
    }

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function isValid(): bool { return is_null($this->used_at) && $this->expires_at->isFuture(); }
    public function markAsUsed(): void { $this->update(['used_at' => now()]); }
}
EOF

cat > $MODEL_DIR/AuditLog.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['entity_type','entity_id','action_type','actor_type','actor_id','action_payload','payload_hash'];
    protected $casts = ['action_payload' => 'array'];

    const ENTITY_JOB         = 'job';
    const ENTITY_APPLICATION = 'application';
    const ENTITY_BILLING     = 'billing';
    const ENTITY_AGREEMENT   = 'agreement';
    const ENTITY_TOKEN       = 'token';

    const ACTION_JOB_CREATED                = 'job_created';
    const ACTION_AGREEMENT_SAVED            = 'agreement_saved';
    const ACTION_LP_GENERATED              = 'lp_generated';
    const ACTION_LINE_APPLICATION_STARTED   = 'line_application_started';
    const ACTION_LINE_CONDITION_ANSWERED    = 'line_condition_answered';
    const ACTION_LINE_APPLICATION_COMPLETED = 'line_application_completed';
    const ACTION_FORM_APPLICATION_VIEWED    = 'form_application_viewed';
    const ACTION_FORM_APPLICATION_SUBMITTED = 'form_application_submitted';
    const ACTION_BILLING_GENERATED          = 'billing_generated';
    const ACTION_ADMIN_CONFIRMED            = 'admin_confirmed';
    const ACTION_NOTIFICATION_SENT          = 'notification_sent';
    const ACTION_TOKEN_REISSUED             = 'token_reissued';

    const ACTOR_APPLICANT = 'applicant';
    const ACTOR_ADMIN     = 'admin';
    const ACTOR_SYSTEM    = 'system';

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new \LogicException('AuditLog is append only.');
    }

    public function delete(): bool
    {
        throw new \LogicException('AuditLog is append only.');
    }

    public static function record(string $entityType, int $entityId, string $actionType, string $actorType, array $payload, ?int $actorId = null): self
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        return static::create([
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'action_type'    => $actionType,
            'actor_type'     => $actorType,
            'actor_id'       => $actorId,
            'action_payload' => $payload,
            'payload_hash'   => hash('sha256', $json),
        ]);
    }

    public function verifyHash(): bool
    {
        return hash('sha256', json_encode($this->action_payload, JSON_UNESCAPED_UNICODE)) === $this->payload_hash;
    }

    public function scopeForEntity($q, string $entityType, int $entityId)
    {
        return $q->where('entity_type', $entityType)->where('entity_id', $entityId);
    }
}
EOF

echo "  ✅ Modelファイル 20件 配置完了"

# ============================================================
# migrate 実行
# ============================================================
echo ""
echo "[3/3] php artisan migrate を実行中..."
./vendor/bin/sail artisan migrate

echo ""
echo "=========================================="
echo " ✅ すべての作業が完了しました！"
echo "   - Migration: 20テーブル作成"
echo "   - Model: 20ファイル配置"
echo "=========================================="
