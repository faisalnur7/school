<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['employee_id', 'document_type', 'file_path', 'original_name', 'uploaded_at'];
    protected $casts    = ['uploaded_at' => 'datetime'];

    public function employee() { return $this->belongsTo(Employee::class); }

    public function getFileUrlAttribute(): string  { return asset($this->file_path); }

    public function getFileSizeAttribute(): string
    {
        $path = public_path($this->file_path);
        if (!file_exists($path)) return '—';
        $bytes = filesize($path);
        foreach (['B','KB','MB'] as $unit) {
            if ($bytes < 1024) return round($bytes, 1) . ' ' . $unit;
            $bytes /= 1024;
        }
        return round($bytes, 1) . ' GB';
    }
}
