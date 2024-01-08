<?php

namespace App\Services;

use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\MPriceList;
use App\Models\Setting;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseService
{
    /**
     * @var     BaseRepository $repository
     */
    protected BaseRepository $repository;

    /**
     * Get status
     *
     * @return  array
     */
    public function statusTypes(): array
    {
        return $this->repository->statusTypes();
    }

    /**
     * Generate quote for user.
     *
     * Format: yymmnnnnS
     *         yy: Last 2 digits of current year
     *         mm: Current month
     *         nnnn: Starting from 0051~9999
     *         S: default
     *
     * @param string $trademark_number
     * @param string $type
     *
     * @return string
     */
    public function generateQIR(string $trademark_number = '', string $type = 'quote')
    {
        $code = null;
        switch ($type) {
            case 'quote':
                $max = (Payment::where('quote_number', 'LIKE', "%$trademark_number%")->count() ?? 0) + 1;
                $code = $trademark_number . "M" . ($max > 10 ? $max : '0' . $max);
                break;
            case 'invoice':
                $countInvoiceNumber = Payment::where('invoice_number', 'LIKE', '%S')->count();
                $max = ($countInvoiceNumber > 0 ? $countInvoiceNumber + START_INVOICE_NUMBER : START_INVOICE_NUMBER) + 1;
                $code = now()->format('ym') . str_pad($max, 4, 0, STR_PAD_LEFT) . DEFAULT_INVOICE_NUMBER;
                break;
            case 'receipt':
                $countReceiptNumber = Payment::where('receipt_number', 'LIKE', '%R')->count();
                $max = ($countReceiptNumber > 0 ? $countReceiptNumber + START_INVOICE_NUMBER : START_INVOICE_NUMBER) + 1;
                $code = now()->format('ym') . str_pad($max, 4, 0, STR_PAD_LEFT) . DEFAULT_RECEIPT_NUMBER;
                break;
        }

        return $code;
    }

    /**
     * Get setting
     *
     * @return Setting
     */
    public function getSetting(): Setting
    {
        return $this->repository->getSetting();
    }

    /**
     * Get system fee.
     *
     * @param int $service_type
     * @param string $package_type
     *
     * @return array
     */
    public function getSystemFee(int $service_type, string $package_type): array
    {
        $defaultPrice = MPriceList::where([
            'service_type' => $service_type,
            'package_type' => $package_type,
        ])->first();

        $tax = Setting::where('key', 'tax')->first()->value ?? 0;

        return [
            'cost_service_base' => $defaultPrice->base_price + ($defaultPrice->base_price * $tax / 100),
            'subtotal' => $defaultPrice->base_price + ($defaultPrice->base_price * $tax / 100),
            'commission' => $defaultPrice->base_price,
            'tax' => $defaultPrice->base_price * $tax / 100,
            'pof_1st_distinction_5yrs' => $defaultPrice->pof_1st_distinction_5yrs,
            'pof_1st_distinction_10yrs' => $defaultPrice->pof_1st_distinction_5yrs,
            'pof_2nd_distinction_5yrs' => $defaultPrice->pof_1st_distinction_5yrs,
            'pof_2nd_distinction_10yrs' => $defaultPrice->pof_1st_distinction_5yrs,
        ];
    }

    /**
     * Get period registration.
     *
     * @param int MPriceList::REGISTRATION
     * @param string MPriceList::REGISTRATION_TERM_CHANGE
     * @return MPriceList
     */
    public function getPeriodRegistrationRepository(
        int $serviceType = MPriceList::REGISTRATION,
        string $packageType = MPriceList::REGISTRATION_TERM_CHANGE
    )
    {
        return $this->repository->getPeriodRegistrationRepository($serviceType, $packageType);
    }

    /**
     * Get list.
     *
     * @param   array $conditions
     * @param   array $relations
     * @param   array $relationCounts
     * @param   array $selects
     * @return  Collection
     */
    public function list(array $conditions, array $relations = [], array $relationCounts = [], array $selects = ['*']): Collection
    {
        return $this->repository->queryCollection($conditions, $relations, $relationCounts, $selects);
    }

    /**
     * Get list.
     *
     * @param   array $conditions
     * @param   array $relations
     * @param   array $relationCounts
     * @param   array $selects
     * @return  LengthAwarePaginator
     */
    public function listPaginate(array $conditions, array $relations = [], array $relationCounts = [], array $selects = ['*']): LengthAwarePaginator
    {
        return $this->repository->queryPaginate($conditions, $relations, $relationCounts, $selects);
    }

    /**
     * Create model.
     *
     * @param   array $data
     * @return  Model
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Update model.
     *
     * @param   Model $entity
     * @param   array $data
     * @return  Model
     */
    public function update(Model $entity, array $data = []): Model
    {
        $this->repository->update($entity, $data);

        return $entity;
    }

    /**
     * Update By ID.
     *
     * @param   int $id
     * @param   array $data
     *
     * @return  Model
     */
    public function updateById(int $id, array $data = []): Model
    {
        return $this->repository->updateById($id, $data);
    }

    /**
     * Delete model.
     *
     * @param   Model $entity
     * @return  boolean|null
     */
    public function destroy(Model $entity): ?bool
    {
        return $this->repository->delete($entity);
    }

    /**
     * Get model detail.
     *
     * @param   Model $entity
     * @param   array $relations
     * @return  Model
     */
    public function show(Model $entity, array $relations = []): Model
    {
        return $this->repository->show($entity, $relations);
    }

    /**
     * Update or create model.
     *
     * @param array $condition
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $condition = [], array $data = []): Model
    {
        return $this->repository->updateOrCreate($condition, $data);
    }

    /**
     * Synchro model relation with data.
     *
     * @param   Model $entity
     * @param   mixed $relation
     * @param   array $data
     * @return void
     */
    public function sync(Model $entity, $relation, array $data = [])
    {
        $this->repository->sync($entity, $relation, $data);
    }

    /**
     * Insert multiple values.
     *
     * @param   mixed $data
     * @return  integer
     */
    public function insert($data): int
    {
        return $this->repository->insert($data);
    }

    /**
     * Find model by id.
     *
     * @param   mixed $id
     * @param   array $relations
     * @return  Model
     */
    public function findOrFail($id, array $relations = []): Model
    {
        return $this->repository->findOrFail($id, $relations);
    }

    /**
     * Find model by id.
     *
     * @param   mixed $id
     * @param   array $relations
     *
     * @return  Model|null
     */
    public function find($id, array $relations = []): ?Model
    {
        return $this->repository->find($id, $relations);
    }

    /**
     * Find many model by id.
     *
     * @param   array $ids
     * @param   array $relations
     *
     * @return  Collection
     */
    public function findMany(array $ids, array $relations = []): Collection
    {
        return $this->repository->findMany($ids, $relations);
    }

    /**
     * Find by condition .
     *
     * @param   mixed $condition
     * @param   array $relations
     *
     * @return  object $entities
     */
    public function findByCondition($condition, array $relations = []): object
    {
        return $this->repository->findByCondition($condition, $relations);
    }

    /**
     * Get model's fillable attribute.
     *
     * @return  array
     */
    public function getFillable(): array
    {
        return $this->repository->getFillable();
    }

    /**
     * Batch update.
     *
     * @param   array $condition
     * @param   array $data
     * @return  mixed
     */
    public function batchUpdate(array $condition, array $data)
    {
        return $this->repository->batchUpdate($condition, $data);
    }

    /**
     * Check Duplicate Data
     *
     * @param   mixed  $data
     * @param   string $column
     * @param   mixed  $model
     * @return  boolean
     */
    public function checkDuplicateData($data, string $column, $model): bool
    {
        $exitsData = $model->where($column, $data)->exists();

        // check case update
        if ((array) $model) {
            return !($model->$column === $data || !$exitsData);
        }
        return $exitsData;
    }

    /**
     * Get tax withholding
     *
     * @param string $payerType
     * @param string $subTotal
     * @return string
     */
    public function getTaxWithHolding(PayerInfo $payerInfo, string $subTotal): string
    {
        return $this->repository->getTaxWithHolding($payerInfo, $subTotal);
    }
}
