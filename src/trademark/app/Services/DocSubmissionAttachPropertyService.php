<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\Repositories\DocSubmissionAttachmentRepository;
use App\Repositories\DocSubmissionAttachPropertyRepository;
use App\Services\BaseService;
use App\Repositories\DocSubmissionRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocSubmissionAttachPropertyService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository
     * @param DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository
     */
    public function __construct(
        DocSubmissionAttachPropertyRepository $docSubmissionAttachPropertyRepository,
        DocSubmissionAttachmentRepository $docSubmissionAttachmentRepository
    )
    {
        $this->repository = $docSubmissionAttachPropertyRepository;
        $this->docSubmissionAttachmentRepository = $docSubmissionAttachmentRepository;
    }

    /**
     * Delete property data
     *
     * @param int $docSubmissionAttachPropertyId
     * @return boolean
     */
    public function deletePropertyData(int $docSubmissionAttachPropertyId): bool
    {
        try {
            DB::beginTransaction();
            $docSubAttachProperty = $this->repository->find($docSubmissionAttachPropertyId);
            if (!$docSubAttachProperty) {
                return false;
            }

            $docSubmissionAttachments = $docSubAttachProperty->docSubmissionAttachments;
            $dataPathFile = $docSubmissionAttachments->pluck('attach_file');

            //delete data
            $docSubAttachProperty->docSubmissionAttachments()->delete();
            $docSubAttachProperty->delete();
            DB::commit();

            //delete files
            foreach ($dataPathFile as $path) {
                FileHelper::unlink($path);
            }

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw new \Exception($e->getMessage());

            return false;
        }
    }

    /**
     * @param int $docSubmissionAttachmentId
     * @return boolean
     */
    public function deleteSubmissionAttachment(int $docSubmissionAttachmentId): bool
    {
        try {
            DB::beginTransaction();
            $docSubmissionAttachment = $this->docSubmissionAttachmentRepository->find($docSubmissionAttachmentId);
            if (!$docSubmissionAttachment) {
                return false;
            }

            $dataPathFile = $docSubmissionAttachment->attach_file;

            //delete data
            $docSubmissionAttachment->delete();
            DB::commit();

            //delete files
            if ($dataPathFile) {
                FileHelper::unlink($dataPathFile);
            }

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw new \Exception($e->getMessage());

            return false;
        }
    }
}
