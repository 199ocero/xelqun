<?php

namespace App\Examples\Snippets;

use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Table;

/**
 * One small preview on a docs page. Which one travels in the token.
 */
class SnippetTable extends Table
{
    /**
     * @return array<string, mixed>
     */
    private function snippet(): array
    {
        return TableSnippets::all()[$this->context['snippet']]();
    }

    protected function boot(): void
    {
        $this->id((string) $this->context['snippet'])->paginationPageOptions([5, 10, 25]);

        ($this->snippet()['configure'] ?? fn (): null => null)($this);
    }

    public function query(): Builder
    {
        return $this->snippet()['query']();
    }

    public function columns(): array
    {
        return $this->snippet()['columns'];
    }

    public function filters(): array
    {
        return $this->snippet()['filters'] ?? [];
    }

    public function recordActions(): array
    {
        return $this->snippet()['recordActions'] ?? [];
    }

    public function headerActions(): array
    {
        return $this->snippet()['headerActions'] ?? [];
    }

    public function bulkActions(): array
    {
        return $this->snippet()['bulkActions'] ?? [];
    }

    public function emptyStateActions(): array
    {
        return $this->snippet()['emptyStateActions'] ?? [];
    }
}
