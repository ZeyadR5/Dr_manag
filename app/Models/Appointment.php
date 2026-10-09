<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';

    protected $primaryKey = 'apid';

    public const CREATED_AT = 'postingDate';

    public const UPDATED_AT = 'updationDate';

    protected $fillable = [
        'patient_Name',
        'patient_Num',
        'typ',
        'appointmentDate',
        'appointmentTime',
        'paid',
        'over_paid',
        'statue',
        'commnet',
        'adrs',
    ];

    protected $casts = [
        'statue' => 'integer',
        'paid' => 'integer',
        'over_paid' => 'integer',
    ];

    public function rowTotal(): int
    {
        return (int) $this->paid + (int) $this->over_paid;
    }
}
