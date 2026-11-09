<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property int $project_id
 * @property int|null $user_id
 * @property string $title
 * @property string $status
 * @property string $priority
 * @property Carbon|null $due_on
 * @property-read Project $project
 * @property-read User|null $assignee
 */
#[Fillable(['team_id', 'project_id', 'user_id', 'title', 'status', 'priority', 'due_on'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const array STATUSES = ['todo' => 'To do', 'doing' => 'In progress', 'done' => 'Done'];

    public const array PRIORITIES = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['due_on' => 'date'];
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
