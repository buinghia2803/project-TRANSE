<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('success', function ($action, $data = null) use ($factory) {
            $format = [
                'status' => true,
                'message' => 'The request was successful',
                'action' => $action,
                'timestamp' => time(),
                'timezone' => config('app.timezone')
            ];

            if ($data instanceof AnonymousResourceCollection) {
                $data = $data->response()->getData(true);
            } elseif ($data instanceof JsonResource) {
                // TODO: processing if need
            }

            if (isset($data['data'])) {
                $format['result'] = $data;
            } else {
                $format['result']['data'] = $data;
            }
            return $factory->make($format, 200);
        });

        $factory->macro('failure', function (string $action = '', string $message = '', $code = '', $status = 400) use ($factory) {
            $format = [
                'status' => false,
                'code' => $code,
                'message' => $message,
                'action' => $action,
                'timestamp' => time(),
                'timezone' => config('app.timezone'),
                'result' => null
            ];

            return $factory->make($format, $status);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
