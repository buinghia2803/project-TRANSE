<?php

namespace App\Services\XMLProcedures;

class Procedure extends BaseClsProcedure
{
    public $document_type;
    public $computer_name;
    public $user_name;
    public $distinction_number;
    public RelationFile $relation_file;
    /**
     * This is not a spelling error.
     * It's because we are retrieving the tag from an XML file to map it with the system's class, and the tag in the XML file is named as such.
     */
    public ProcedureInfomation $procedure_infomation;
}
