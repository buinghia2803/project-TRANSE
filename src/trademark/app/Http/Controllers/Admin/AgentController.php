<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\AgentService;
use App\Services\AgentGroupService;
use App\Services\HistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;

use App\Services\TrademarkService;
use Illuminate\Contracts\View\View;

class AgentController extends Controller
{
    /**
     * @var     AgentService $agentService
     */
    protected $agentService;

    /**
     * @var TrademarkService $trademarkService
     */
    protected $trademarkService;
    /**
     * @var     AgentGroupService $agentGroupService
     */
    protected $agentGroupService;

    /**
     * @var     HistoryService $historyService
     */
    protected $historyService;

    /**
     * Constructor
     *
     * @param   AgentService $agentService
     * @param   TrademarkService $trademarkService
     * @param   AgentGroupService $agentGroupService
     * @param   HistoryService $historyService
     *
     * @return  void
     */
    public function __construct(
        AgentService $agentService,
        TrademarkService $trademarkService,
        AgentGroupService $agentGroupService,
        HistoryService $historyService
    )
    {
        $this->agentService = $agentService;
        $this->trademarkService = $trademarkService;
        $this->agentGroupService = $agentGroupService;
        $this->historyService = $historyService;

        $this->middleware('permission:agents.index')->only(['index']);
        $this->middleware('permission:agents.updateOrCreate')->only(['updateOrCreate']);
        $this->middleware('permission:agents.showSettingSet')->only(['showSettingSet']);
        $this->middleware('permission:agents.crudSettingSet')->only(['crudSettingSet']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     * @return  View
     */
    public function index(Request $request)
    {
        $params = [
            'sort' => 'id',
            'sortType' => SORT_TYPE_ASC,
        ];
        $agents = $this->agentService->list($params);
        $agentsJson = json_encode($agents->toArray());

        return view('admin.modules.agents.setting', compact('agents', 'agentsJson'));
    }

    /**
     * Destroy agent
     *
     * @param Agent
     */
    public function destroy(Agent $agent, Request $request)
    {
        $this->agentService->destroy($agent);

        return redirect()->route('admin.agent.index');
    }

    /**
     * Update or create agent.
     *
     * @param Request request
     *
     * @return View|RedirectResponse
     */
    public function updateOrCreate(Request $request)
    {
        if ($request->has('agents_data') && $request->agents_data) {
            $agentsData = json_decode($request->agents_data);
            $admin = Auth::user();
            $newAgent = [];
            try {
                foreach ($agentsData as $idx => $agent) {
                    $newAgent[$idx] = [];
                    foreach ($agent as $key => $value) {
                        if (empty($value)) {
                            if (str_contains($key, 'deposit_account_number')) {
                                $arr = explode('_', $key);
                                if ($agent->{'deposit_type_' . $arr[count($arr) - 1]} == 1) {
                                    throw new \Exception(__('messages.common.errors.Common_E001'));
                                }
                            } else {
                                throw new \Exception(__('messages.common.errors.Common_E001'));
                            }
                        }
                        if (str_contains($key, 'name')) {
                            preg_match('/^[ぁ-んァ-ン一-龥－・]*$/u', $value, $matches);
                            if (!count($matches)) {
                                throw new \Exception(__('messages.common.errors.Common_E010'));
                            }
                            $newAgent[$idx]['name'] = $value;
                        } elseif (str_contains($key, 'identification_number')) {
                            preg_match('/^[０-９]*$/u', $value, $matches);
                            if (!count($matches)) {
                                throw new \Exception(__('messages.common.errors.Common_E009'));
                            }
                            $newAgent[$idx]['identification_number'] = $value;
                        } elseif (str_contains($key, 'deposit_account_number')) {
                            preg_match('/^[０-９]*$/u', $value, $matches);
                            if (!count($matches)) {
                                throw new \Exception(__('messages.common.errors.Common_E009'));
                            }
                            $newAgent[$idx]['deposit_account_number'] = $value;
                        } elseif (str_contains($key, 'deposit_type')) {
                            $newAgent[$idx]['deposit_type'] = $value;
                        } else {
                            $newAgent[$idx][$key] = $value;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error($e);

                return redirect()->route('admin.agent.index')->with([
                    'error' => $e->getMessage(),
                ]);
            }
            try {
                DB::beginTransaction();
                // Get agent was deleted
                $identNumbers = array_column($newAgent, 'identification_number');
                $agentDeleted = Agent::onlyTrashed()->whereIn('identification_number', $identNumbers);
                $exist = $agentDeleted->count();

                $agentTrashedIds = $agentDeleted->select('id', 'identification_number')->get()->groupBy('identification_number')->toArray();
                if ($exist) {
                    // Restore agent was deleted.
                    $agentDeleted->pluck('id')->toArray();
                    AgentGroupMap::onlyTrashed()->whereIn('agent_id', $agentDeleted->pluck('id')->toArray())->restore();
                    $agentDeleted->restore();
                }
                $duplicate = array_diff_assoc($identNumbers, array_unique($identNumbers));
                if (count($duplicate)) {
                    return redirect()->route('admin.agent.index')->with([
                        'error' => __('messages.general.Common_E033', ['attr' => implode('、', array_unique($duplicate))]),
                    ]);
                }
                foreach ($newAgent as $key => $agent) {
                    $conds = [];
                    if (isset($agent['id'])) {
                        $conds['id'] = $agent['id'];
                    } else {
                        $conds = [
                            'deposit_type' => $agent['deposit_type'],
                            'deposit_account_number' => $agent['deposit_account_number'],
                            'identification_number' => $agent['identification_number'],
                            'name' => $agent['name'],
                            'admin_id' => $admin->id
                        ];
                    }

                    // Set id in condition to update or create
                    if (isset($agentTrashedIds[$agent['identification_number']])) {
                        $conds = [];
                        $conds['id'] = $agentTrashedIds[$agent['identification_number']][0]['id'];
                    }

                    $agentData = $this->agentService->updateOrCreate($conds, $agent);

                    $this->historyService->create([
                        'admin_id' => $admin->id,
                        'target_id' => $agentData->id,
                        'page' => '/a000dairinin.html',
                        'action' => HISTORY_ACTION_MODIFY ,
                        'type' => HISTORY_TYPE['crud_dairinin_set']
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);

                return redirect()->route('admin.agent.index')->with([
                    'error' => __('messages.common.errors.Common_E025'),
                ]);
            }
        }

        if ($request->has('agents_data_delete') && $request->agents_data_delete) {
            $agentsDataDelete = json_decode($request->agents_data_delete);
            $this->agentService->findByCondition(['ids' => $agentsDataDelete])->delete();
        }

        if ($request->has('redirect') && $request->redirect && $request->redirect != 'admin.agent.index') {
            return redirect()->route($request->redirect);
        }

        return redirect()->route('admin.agent.index')->with([
            'success' => __('messages.common.successes.Common_S008'),
        ]);
    }

    /**
     * Delete agent with Ajax
     *
     * @param string $id
     */
    public function deleteAgent($id)
    {
        try {
            DB::beginTransaction();
            AgentGroupMap::where('agent_id', $id)->delete();
            $agent = $this->agentService->find($id)->delete();
            if ($agent) {
                DB::commit();

                return response()->json(['status' => true], 200);
            }

            return response()->json(['status' => false], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json(['status' => false], 400);
        }
    }

    /**
     * Showing setting set screen of dairinin.
     *
     * @param Request $request
     */
    public function showSettingSet(Request $request)
    {
        $params = [
            'sort' => 'id',
            'sortType' => SORT_TYPE_ASC,
        ];
        $agentGroups = $this->agentGroupService->list($params, [
        'collectAgent' => function ($query) {
            return $query->orderBy('type', 'asc');
        },
        ]);
        $agents = $this->agentService->list($params, [], [], ['id', 'name', 'identification_number', 'deposit_account_number', 'deposit_type'])
            ->mapWithKeys(function ($item, $key) {
                return [$item['id'] => $item['name'] . ($item['deposit_type'] == Agent::ADVANCE_PAYMENT ? '（予納）' : '（指定立替納付）') ];
            })->all();

        ksort($agents);

        return view('admin.modules.agents.setting-set', compact('agents', 'agentGroups'));
    }

    /**
     * Create or update or delete setting set
     *
     * @param Request $request
     * @return View
     */
    public function crudSettingSet(Request $request)
    {
        $admin = Auth::user();
        $params = $request->except('_method', '_token');
        $redirect = $request->redirect ?? null;
        unset($params['redirect']);
        $data = [];
        foreach ($params as $key => $value) {
            if ($value) {
                $arrKey = explode('_', $key);
                $lenArr = count($arrKey);
                if (str_contains($key, 'select_agent')) {
                    $data[$arrKey[2]]['agent_ids'][] = [
                       'agent_id' => $value[0],
                       'type' => AGENT_SELECTION_TYPE
                    ];
                }
                if (str_contains($key, 'select_appoint_agent_')) {
                    foreach ($value as $key => $agentId) {
                        $data[$arrKey[3]]['agent_ids'][] = [
                            'agent_id' => $agentId,
                            'type' => APPOINTED_AGENT_SELECTION_TYPE
                         ];
                    }
                }
                switch ($arrKey[0]) {
                    case 'id':
                        $data[$arrKey[$lenArr - 1]]['id'] = $value;
                        break;
                    case 'actionType':
                        $data[$arrKey[$lenArr - 1]]['actionType'] = $value == 'edit';
                        break;
                    case 'name':
                        $data[$arrKey[$lenArr - 1]]['name'] = $value;
                        break;
                    case 'statusChoice':
                        $data[$arrKey[$lenArr - 1]]['status_choice'] = $value ? AgentGroup::STATUS_CHOICE_TRUE : AgentGroup::STATUS_NOT_CHOICE_TRUE;
                        break;
                    default:
                        break;
                }
                $data[$arrKey[$lenArr - 1]]['admin_id'] = $admin->id;
            }
        }
        try {
            foreach ($data as $key => $value) {
                if (!array_key_exists('name', $value) || !array_key_exists('agent_ids', $value)) {
                    throw new \Exception('Data invalid');
                }
                if (isset($value['actionType']) && $value['actionType']) {
                    if (!isset($value['status_choice'])) {
                        $value['status_choice'] = AgentGroup::STATUS_NOT_CHOICE_TRUE;
                    }

                    $agentGroup = $this->agentGroupService->updateOrCreate(['id' => $value['id'] ?? 0], $value);
                } else {
                    $agentGroup = $this->agentGroupService->create($value);
                }
                $syncData = [];
                foreach ($value['agent_ids'] as $key => $item) {
                    $syncData[$item['agent_id']] = [ 'type' => $item['type']];
                }
                $agentGroup->agents()->sync($syncData);

                $this->historyService->create([
                    'admin_id' => $admin->id,
                    'target_id' => $agentGroup->id,
                    'page' => '/a000dairinin_set.html',
                    'action' => $agentGroup->wasRecentlyCreated ? HISTORY_ACTION_ADD : HISTORY_ACTION_MODIFY,
                    'type' => HISTORY_TYPE['crud_dairinin_set']
                ]);
            }

            if ($redirect) {
                return redirect()->route($redirect)->with([
                    'message' => __('messages.general.Common_S008'),
                ]);
            }

            return redirect()->route('admin.agent.crud-setting-set')->with([
                'success' => 'Update or create success!',
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->route('admin.agent.crud-setting-set', compact('params'))->withErrors(['error' => 'Data invalid']);
        }
    }
}
