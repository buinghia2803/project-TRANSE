<?php

namespace App\Services\XMLProcedures;

class ApplicationReference extends BaseClsProcedure
{
    public $registration_number;
    public $application_number;
    public $application_date;
    public $international_application_number;
    public $international_application_date;
    public $reference_id; // reference_id == trademarks.trademark_number
    public $appeal_reference_number;
    public $appeal_reference_date;
    public $number_of_annexation;
};
