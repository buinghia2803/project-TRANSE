<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TrademarkService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewApplyController extends Controller
{
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(UserService $userService, TrademarkService $trademarkService)
    {
        $this->userService = $userService;
        $this->trademarkService = $trademarkService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.modules.new-apply.index');
    }

    /**
     * Redirect To Screens
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        switch ($request['submit_type']) {
            case REDIRECT_TO_SUPPORT_FIRST_TIME:
                return redirect()->route('user.sft.index');
            case REDIRECT_TO_SEARCH_AI:
                return redirect()->route('user.search-ai');
            case REDIRECT_TO_REGISTER_APPLY_TRADEMARK:
                return redirect()->route('user.apply-trademark-register');
            case REDIRECT_TO_MEMBER_REGISTER_PRE:
                return redirect()->route('auth.signup');
        }
    }
}
