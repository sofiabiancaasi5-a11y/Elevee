<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone_number',
        'street_address',
        'apartment',
        'city',
        'province_state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $line1 = $this->apartment ? "{$this->apartment}, {$this->street_address}" : $this->street_address;
        return "{$line1}, {$this->city}, {$this->province_state} {$this->postal_code}, {$this->country}";
    }
}
