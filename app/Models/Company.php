<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'primary_color',
        'logo_path',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    // Helper methods
    public function generateColorVariants(): array
    {
        $hsl = $this->hexToHsl($this->primary_color);
        
        return [
            'primary' => $this->primary_color,
            'primary_hover' => $this->adjustLightness($hsl, -10),
            'primary_light' => $this->adjustLightness($hsl, 40),
            'primary_border' => $this->adjustSaturation($hsl, -20),
        ];
    }

    private function hexToHsl(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                case $b: $h = ($r - $g) / $d + 4; break;
            }
            $h /= 6;
        }

        return [round($h * 360), round($s * 100), round($l * 100)];
    }

    private function adjustLightness(array $hsl, int $amount): string
    {
        $hsl[2] = max(0, min(100, $hsl[2] + $amount));
        return $this->hslToHex($hsl);
    }

    private function adjustSaturation(array $hsl, int $amount): string
    {
        $hsl[1] = max(0, min(100, $hsl[1] + $amount));
        return $this->hslToHex($hsl);
    }

    private function hslToHex(array $hsl): string
    {
        $h = $hsl[0] / 360;
        $s = $hsl[1] / 100;
        $l = $hsl[2] / 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = function($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }

        return sprintf("#%02x%02x%02x", round($r * 255), round($g * 255), round($b * 255));
    }
}
