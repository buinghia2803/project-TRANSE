<?php

namespace App\Helpers;

class FlashMessageHelper
{
    /**
     * @param $request
     * @param $type : ERROR|WARNING|SUCCESS
     * @param $messages
     * @throws \Throwable
     */
    public static function setMessage($request, $type, $messages)
    {
        $request->session()->flash('alertMsg', view('errors.message')->with([
            'type' => $type,
            'messages' => $messages,
        ])->render());
    }

    /**
     * @param $request
     * @return $message
     */
    public static function getMessage($request)
    {
        $message = null;
        if ($request->session()->has('alertMsg')) {
            $message = $request->session()->get('alertMsg');
            $request->session()->forget('alertMsg');
        }
        return $message;
    }
}
