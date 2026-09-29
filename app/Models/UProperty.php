<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UProperty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'energy',
        'energy_max_regen',
        'energy_max_stock',
        'credit_free',
        'credit_paid',
        'credit_max_stock',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'energy' => 'integer',
        'energy_max_regen' => 'integer',
        'energy_max_stock' => 'integer',
        'credit_free' => 'integer',
        'credit_paid' => 'integer',
        'credit_max_stock' => 'integer',
    ];
}
