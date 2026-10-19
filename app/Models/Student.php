<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['student_number', 'name', 'class', 'gender', 'date_of_birth', 'is_active'])]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'is_active' => 'boolean'];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SavingTransaction::class);
    }
}
