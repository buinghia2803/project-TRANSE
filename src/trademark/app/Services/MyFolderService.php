<?php

namespace App\Services;

use App\Models\MyFolder;
use App\Models\MyFolderProduct;
use App\Models\User;
use App\Repositories\MyFolderRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MyFolderService extends BaseService
{
    private MyFolderProductService $myFolderProductService;

    /**
     * Initializing the instances and variables
     *
     * @param MyFolderRepository $myFolderRepository
     */
    public function __construct(
        MyFolderRepository $myFolderRepository,
        MyFolderProductService $myFolderProductService
    )
    {
        $this->repository = $myFolderRepository;
        $this->myFolderProductService = $myFolderProductService;
    }

    /**
     * Generate Folder Number
     *
     * @return string
     */
    public function generateFolderNumber(): string
    {
        $user = Auth::guard('web')->user()->user_number ?? 0;
        $year = date('y');
        $randomNumber = Str::padLeft(rand(0, 9999), 4, 0);
        return $user . $year . $randomNumber;
    }

    /**
     * Format Data before Create Folder
     *
     * @param   Request $request
     * @return  array
     */
    public function formatData(Request $request): array
    {
        // Format keyword
        $keyword = $request->keyword ?? '';
        $keyword = explode(',', $keyword);
        $keyword = json_encode(array_values(array_filter($keyword)), JSON_UNESCAPED_UNICODE);

        // Data My Folder
        $dataMyFolder = [
            'user_id' => Auth::guard('web')->id(),
            'folder_number' => $this->generateFolderNumber(),
            'keyword' => $keyword,
            'type_trademark' => $request->type_trademark,
            'name_trademark' => $request->name_trademark,
            'image_trademark' => $request->image_trademark ?? null,
        ];

        // All Product
        $allProduct = [];

        // Create Folder Product Additional
        $prodAdditionalIds = $request->prod_additional_ids ?? '';
        $prodAdditionalIds = (!empty($prodAdditionalIds)) ? explode(',', $prodAdditionalIds) : [];
        $prodAdditionals = [];
        foreach ($prodAdditionalIds as $m_product_id) {
            $allProduct[] = $prodAdditionals[] = [
                'm_product_id' => $m_product_id,
                'type' => MyFolderProduct::TYPE_ADDITIONAL,
                'is_additional_search' => true,
            ];
        }

        // Create Folder Product Suggest
        $prodSuggestIds = $request->prod_suggest_ids ?? '';
        $prodSuggestIds = (!empty($prodSuggestIds)) ? explode(',', $prodSuggestIds) : [];
        $prodSuggests = [];
        foreach ($prodSuggestIds as $m_product_id) {
            $allProduct[] = $prodSuggests[] = [
                'm_product_id' => $m_product_id,
                'type' => MyFolderProduct::TYPE_EXIST,
                'is_additional_search' => false,
            ];
        }

        return [
            'dataMyFolder' => $dataMyFolder,
            'prodAdditionals' => $prodAdditionals,
            'prodSuggests' => $prodSuggests,
            'allProduct' => $allProduct,
        ];
    }

    /**
     * Create Folder
     *
     * @param   array $myFolderData
     * @param   array $folderProductData
     * @return  Model
     */
    public function createFolder(array $myFolderData, array $folderProductData = []): Model
    {
        // If my folder of user > 5 => Delete First
        $folders = $this->findByCondition([
            'user_id' => Auth::guard('web')->id(),
        ])->get();
        if ($folders->count() >= IS_MAX_FOLDER) {
            $firstFolder = $folders->first();
            $firstFolder->myFolderProduct()->delete();
            $firstFolder->delete();
        }

        // Create New
        $myFolderData['name_trademark'] = !empty($myFolderData['name_trademark']) ? $myFolderData['name_trademark'] : '';
        $folder = $this->create($myFolderData);

        // Create Folder Product
        foreach ($folderProductData as $item) {
            $folder->myFolderProduct()->create($item);
        }

        return $folder;
    }

    /**
     * Update Folder
     *
     * @param   int   $folderId
     * @param   array $myFolderData
     * @param   array $folderProductData
     * @return  Model
     */
    public function updateFolder(int $folderId, array $myFolderData, array $folderProductData = []): Model
    {
        $folder = $this->find($folderId);

        $myFolderData['name_trademark'] = !empty($myFolderData['name_trademark']) ? $myFolderData['name_trademark'] : '';
        $folder->update($myFolderData);

        // Delete Old and create New Product
        $folder->myFolderProduct()->delete();
        foreach ($folderProductData as $item) {
            $folder->myFolderProduct()->create($item);
        }

        return $folder;
    }

    /**
     * Create Folder And Product Search Ai Report
     *
     * @param $request
     * @param $refererData
     * @return boolean
     */
    public function createFolderSearchAiReport($request, $refererData)
    {
        // If my folder of user > 5 => Delete First
        $folders = $this->findByCondition([
            'user_id' => Auth::guard('web')->id(),
        ])->get();
        if ($folders->count() >= IS_MAX_FOLDER) {
            $firstFolder = $folders->first();
            $firstFolder->myFolderProduct()->delete();
            $firstFolder->delete();
        }
        $dataCreateMyFolder = $this->dataMyFolder($request, $refererData);
        $dataAllProduct = $this->dataAllProduct($request);
        $myFolder = $this->repository->create($dataCreateMyFolder);
        $countDataAllProduct = count($dataAllProduct);
        for ($i = 0; $i < $countDataAllProduct; $i++) {
            if ($dataAllProduct[$i]['m_product_id'] != '') {
                $dataAllProduct[$i]['my_folder_id'] = $myFolder->id;
                $this->myFolderProductService->create($dataAllProduct[$i]);
            }
        }

        return true;
    }

    /**
     * Create Folder And Product Search Ai Report
     *
     * @param $request
     * @param $refererData
     * @return array
     */
    public function dataMyFolder($request, $refererData)
    {
        $user = User::where('id', 1)->first();
        $numberUser = $user->user_number;
        $year = date('y');
        $randomNumber = random_int(1000, 9999);
        $folderNumber = $numberUser . $year . $randomNumber;
        // Create My Folder
        $condition = [
            'user_id' => Auth::user()->id ?? 1,
            'folder_number' => $folderNumber,
            'keyword' => $request['keyword'] ?? '',
            'type_trademark' => $request['type_trademark'],
            'name_trademark' => $request['name_trademark'] ?? '',
        ];

        if (empty($refererData)) {
            return redirect()->back();
        };
        // Check Session
        $referer = $refererData['referer'] ?? '';
        if ($referer == FROM_SUPPORT_FIRST_TIME) {
            $condition['target_id'] = $refererData['support_first_time'];
            $condition['type'] = MyFolder::TYPE_SFT;
        } elseif ($referer == FROM_PRECHECK) {
            $condition['target_id'] = $refererData['precheck_id'];
            $condition['type'] = MyFolder::TYPE_PRECHECK;
        } else {
            $condition['target_id'] = null;
            $condition['type'] = MyFolder::TYPE_OTHER;
        }

        return $condition;
    }

    /**
     * Create Folder And Product Search Ai Report
     *
     * @param $request
     * @return array
     */
    public function dataAllProduct($request)
    {
        $productIds = explode(',', $request['prod_suggest_ids']);
        $productAddIds = explode(',', $request['prod_additional_ids']);
        $countProductId = count($productIds) ?? 0;
        $countProductAddId = count($productAddIds) ?? 0;
        // All Product
        $allProduct = [];
        // Create Product Exist
        for ($i = 0; $i < $countProductId; $i++) {
            $allProduct[] = $conditionFolderProduct[] = [
                'm_product_id' => $productIds[$i],
                'type' => MyFolderProduct::TYPE_EXIST,
                'is_additional_search' => false,
            ];
        }
        // Create Product Add
        for ($i = 0; $i < $countProductAddId; $i++) {
            $allProduct[] = $conditionFolderProductAdd[] = [
                'm_product_id' => $productAddIds[$i],
                'type' => MyFolderProduct::TYPE_ADDITIONAL,
                'is_additional_search' => true,
            ];
        }

        return $allProduct;
    }
}
