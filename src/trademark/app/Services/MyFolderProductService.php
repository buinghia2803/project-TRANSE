<?php

namespace App\Services;

use App\Repositories\MyFolderProductRepository;

class MyFolderProductService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param MyFolderProductRepository $myFolderProductRepository
     */
    public function __construct(MyFolderProductRepository $myFolderProductRepository)
    {
        $this->repository = $myFolderProductRepository;
    }
}
