<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MailTemplate\UpdateRequest;
use App\Models\MailTemplate;
use App\Services\MailTemplateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MailTemplateController extends BaseController
{
    /**
     * @var MailTemplateService $mailTemplateService
     */
    public MailTemplateService $mailTemplateService;

    /**
     * Constructor
     *
     * @param   MailTemplateService $mailTemplateService
     * @return  void
     */
    public function __construct(MailTemplateService $mailTemplateService)
    {
        parent::__construct();

        $this->mailTemplateService = $mailTemplateService;

        // Check permission
        $this->middleware('permission:mail-template.index')->only(['index']);
        $this->middleware('permission:mail-template.update')->only(['update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $currentType = $request->type ?? MailTemplate::MAIL_TEMPLATES_PASSWORD_RESET;

        $templateTypes = $this->mailTemplateService->types();

        $mailTemplate = $this->mailTemplateService->findByCondition([
            'type' => $currentType,
            'lang' => $request->lang ?? \App::getLocale(),
        ])->first();

        $mailTemplateType = collect($templateTypes)->where('type', $currentType)->first();

        return view('admin.modules.mail-templates.index', compact(
            'currentType',
            'templateTypes',
            'mailTemplate',
            'mailTemplateType',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param   UpdateRequest $request
     * @param   integer       $id
     * @return  RedirectResponse
     */
    public function update(UpdateRequest $request, int $id): RedirectResponse
    {
        $result = $this->mailTemplateService->updateRecord($id, $request->all());

        if ($result) {
            return redirect()->back()->with('message', __('messages.update_success'))->withInput();
        }

        return redirect()->back()->with('error', __('messages.update_fail'))->withInput();
    }
}
