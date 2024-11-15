<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function competency_standard()
    {
        return $this->belongsTo(CompetencyStandard::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
