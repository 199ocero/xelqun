<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $team_id
 * @property string $name
 * @property string $client
 * @property string $status
 */
#[Fillable(['team_id', 'name', 'client', 'status', 'budget', 'due_on'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    public const array STATUSES = ['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On hold', 'done' => 'Done'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['budget' => 'decimal:2', 'due_on' => 'date'];
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
