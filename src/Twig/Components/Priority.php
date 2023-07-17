<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Enum\Priority as EnumPriority;

#[AsTwigComponent()]
final class Priority
{
    public EnumPriority $priority;
    public string $color;
    public function mount(EnumPriority $priority): void
    {
        $this->priority = $priority;
        $this->color = match($priority->name) {
            EnumPriority::LOW->name => 'bg-primary',
            EnumPriority::MEDIUM->name => 'bg-dark',
            EnumPriority::HIGH->name => 'bg-warning',
            EnumPriority::VERY_HIGH->name => 'bg-danger',
            default => 'bg-light'
        };
    }
}
