<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public string $title;
    public string $subtitle;
    public int|string $value;
    public ?int $change;

    /**
     * Maak een nieuw component.
     */
    public function __construct(
        string $title,
        string $subtitle,
        int|string $value,
        int $change = null
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->value = $value;
        $this->change = $change;
    }

    public function render(): View|Closure|string
    {
        return view('components.stat-card');
    }
}
