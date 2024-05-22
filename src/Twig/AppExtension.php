<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('formatTimer', [$this, 'formatTimer']),
        ];
    }

    public function formatTimer($milliseconds): string
    {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);

        $seconds = $seconds % 60;
        $minutes = $minutes % 60;
        $milliseconds = $milliseconds % 1000;

        $duration = '';

        if ($hours != 0) {
            $duration .= sprintf('%02dh ', $hours);
        }

        if ($minutes !== 0 || $hours !== 0 || $seconds !== 0) {
            $duration .= sprintf('%02dm ', $minutes);
        }

        if ($seconds !== 0 || $minutes !== 0 || $hours !== 0) {
            $duration .= sprintf('%02ds ', $seconds);
        }

        if ($milliseconds !== 0) {
            $duration .= sprintf('%03dms', $milliseconds);
        }

        return $duration;
    }

}