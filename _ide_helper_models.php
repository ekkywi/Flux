<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property \App\Enums\ApprovalType $request_type
 * @property string|null $requested_role
 * @property string $justification
 * @property array<array-key, mixed>|null $metadata
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property string|null $processed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $processor
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereJustification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereRequestedRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRequest whereUserId($value)
 */
	class AccessRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $user_id
 * @property string $action
 * @property string $category
 * @property string $severity
 * @property string|null $target_type
 * @property string|null $target_id
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $modified_fields
 * @property-read mixed $target_label
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $ip_address
 * @property int $ssh_port
 * @property string $ssh_user
 * @property string $status
 * @property string $environment
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereSshPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereSshUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server withoutTrashed()
 */
	class Server extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $key_name
 * @property string|null $public_key
 * @property string|null $private_key
 * @property string|null $last_rotated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereLastRotatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereUpdatedAt($value)
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $department
 * @property string $role
 * @property bool $is_active
 * @property string|null $last_login_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AccessRequest|null $accessRequest
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

