<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name',
        'code',
        'project_path',
        'created_by',
    ];

    /**
     * Get the project path with proper formatting
     */
    public function getProjectPathAttribute($value)
    {
        return $value ?: 'uploads/projects/' . $this->name;
    }

    /**
     * Get the project image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getProjectImageUrl();
    }

    /**
     * Generate project path based on name
     */
    public function generateProjectPath()
    {
        if ($this->name) {
            $this->project_path = 'uploads/projects/' . $this->name;
        }
    }

    /**
     * Generate a unique project image URL based on project code
     */
    public function getProjectImageUrl()
    {
        // Always use project code as the seed
        $seed = $this->code;

        // Create a hash from the seed to ensure consistent colors
        $hash = md5($seed);

        // Extract colors from hash for better variety
        $color1 = '#' . substr($hash, 0, 6);
        $color2 = '#' . substr($hash, 6, 6);
        $backgroundColor = '#' . substr($hash, 12, 6);

        // Use DiceBear API with different patterns for variety
        $patterns = ['geometric', 'abstract', 'shapes', 'pixel'];
        $pattern = $patterns[hexdec(substr($hash, 18, 2)) % count($patterns)];
        $size = 200;

        // Create DiceBear URL with custom colors and pattern
        $dicebearUrl = "https://api.dicebear.com/7.x/{$pattern}/svg?seed={$seed}&size={$size}&backgroundColor={$backgroundColor}&colorLevel=2&radius=20";

        return $dicebearUrl;
    }

    /**
     * Get project initials for fallback
     */
    public function getProjectInitials()
    {
        if ($this->code) {
            return strtoupper(substr($this->code, 0, 2));
        }

        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get project color scheme based on code
     */
    public function getProjectColors()
    {
        $seed = $this->code;
        $hash = md5($seed);

        // Predefined color schemes
        $colorSchemes = [
            ['#6366f1', '#8b5cf6'], // Purple
            ['#10b981', '#059669'], // Green
            ['#f59e0b', '#d97706'], // Amber
            ['#ef4444', '#dc2626'], // Red
            ['#8b5cf6', '#7c3aed'], // Violet
            ['#06b6d4', '#0891b2'], // Cyan
            ['#84cc16', '#65a30d'], // Lime
            ['#f97316', '#ea580c'], // Orange
            ['#ec4899', '#db2777'], // Pink
            ['#14b8a6', '#0d9488'], // Teal
        ];

        // Use hash to select consistent color scheme
        $index = hexdec(substr($hash, 0, 2)) % count($colorSchemes);
        return $colorSchemes[$index];
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function files()
    {
        return $this->hasMany(File::class)->whereNull('folder_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
