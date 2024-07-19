<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;
use App\Support\Generators;

class Project extends Model
{
    use HasFactory;

    const NOT_STARTED = 0;
    const PENDING = 1;
    const COMPLETED = 2;
    
    protected $guarded = [];

    protected $table = 'projects';


    public static function createSlug($name)
    {
        $code = Generators::generateRandomCode();
        $slug = Str::slug($name) . '-' . $code;
        
        return $slug;
    }
    
    public function task_progress()
    {
        return $this->hasOne(TaskProgress::class,'projectId');
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class,'projectId');
    }
}
