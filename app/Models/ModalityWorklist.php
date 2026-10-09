<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalityWorklist extends Model
{
    protected $table = 'modality_worklist';

    public const CREATED_AT = 'CreatedAt';

    public const UPDATED_AT = null;

    protected $fillable = [
        'PatientID',
        'PatientName',
        'Modality',
        'ScheduledDate',
        'ScheduledTime',
        'AccessionNumber',
        'StudyInstanceUID',
        'RequestedProcedureDescription',
        'PatientBirthDate',
        'ScheduledProcedureStepStartDate',
        'ScheduledProcedureStepStartTime',
        'StudyDescription',
    ];
}
