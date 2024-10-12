<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverrideModel extends Model
{
    protected $table = 'overrides';
    protected $fillable = [
        'transaction_date',
        'description',
        'amount',
        'override',
        'notes'
    ];

    public string $transaction_date;
    public string $description;
    public float $amount;
    public string $override;
    public string $notes;
}