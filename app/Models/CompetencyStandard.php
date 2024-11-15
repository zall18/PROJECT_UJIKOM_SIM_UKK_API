<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyStandard extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function competency_elements()
    {
        return $this->hasMany(CompetencyElement::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }
}
