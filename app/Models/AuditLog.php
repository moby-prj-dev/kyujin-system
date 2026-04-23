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
    const ACTION_JOB_CLOSED                 = 'job_closed';
    const ACTION_JOB_REOPENED               = 'job_reopened';
    const ACTION_JOB_CONTINUED              = 'job_continued';
    const ACTION_JOB_DELETED                = 'job_deleted';
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
