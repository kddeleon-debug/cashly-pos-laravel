<?php

if (! function_exists('initials')) {
    /** "Juan Dela Cruz" -> "JD" */
    function initials(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: '?';
    }
}

if (! function_exists('peso')) {
    function peso(float|int|string|null $amount): string
    {
        return '₱'.number_format((float) $amount, 2);
    }
}
