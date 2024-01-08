<?php

namespace App\Services\GMO;

use Illuminate\Support\Facades\Log;

class GMOService
{
    private string $uri;
    private string $shopID;
    private string $shopPassword;

    public function __construct()
    {
        $this->uri = config('gmo.uri');
        $this->shopID = config('gmo.shop_id');
        $this->shopPassword = config('gmo.shop_password');
    }

    /**
     * @param   string $url
     * @param   array  $data
     * @param   string $method
     * @return  array|false|string|string[]|null
     */
    private function sendRequest(string $url, array $data, string $method = 'POST')
    {

        $url = $this->uri . '/' . $url;

        $dataSubmit = http_build_query($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $dataSubmit,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
                "Accept: application/json;charset=UTF-8",
                "Accept-Charset: UTF-8",
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        return mb_convert_encoding($response, 'utf-8', 'shift-jis');
    }

    /**
     * Api to get AccessID & AccessPass required for settlement transactions and start trading
     * Docs: https://docs.mul-pay.jp/payment/credit/api
     *
     * @param   array $data
     * @return  array
     */
    public function creditEntryTran(array $data): array
    {
        try {
            $defaultData = [
                'ShopID' => $this->shopID,
                'ShopPass' => $this->shopPassword,
                'JobCd' => 'CHECK',
            ];
            $data = array_merge($defaultData, $data);

            $response = $this->sendRequest('payment/EntryTran.idPass', $data);

            if (GMOHelper::isError($response)) {
                Log::error('EntryTran Process Fail. OrderID: ' . $data['OrderID']);
                return [];
            }

            Log::info('EntryTran Process Success. OrderID: ' . $data['OrderID']);

            return GMOHelper::convertResponseIdPassToArray($response);
        } catch (\Exception $e) {
            Log::error($e);
            return [];
        }
    }

    /**
     * Api to get AccessID & AccessPass required for settlement transactions and start trading
     * Docs: https://docs.mul-pay.jp/payment/credit/api
     *
     * @param   array $entryTran
     * @param   array $data
     * @return  array
     */
    public function creditExecTran(array $entryTran, array $data): array
    {
        try {
            $defaultData = [
                'Method' => '1',
            ];
            $data = array_merge($entryTran, $defaultData, $data);

            $response = $this->sendRequest('payment/ExecTran.idPass', $data);

            if (GMOHelper::isError($response)) {
                Log::error('ExecTran Process Fail. OrderID: ' . $data['OrderID']);

                throw new \Exception($response);
            }

            Log::info('ExecTran Process Success. OrderID: ' . $data['OrderID']);

            return GMOHelper::convertResponseIdPassToArray($response);
        } catch (\Exception $e) {
            Log::error($e);

            return GMOHelper::convertResponseIdPassToArray($response);
        }
    }

    /**
     * Search Trade
     *
     * @param   array $data
     * @return  array
     */
    public function SearchTradeMulti(array $data): array
    {
        try {
            $defaultData = [
                'ShopID' => $this->shopID,
                'ShopPass' => $this->shopPassword,
            ];
            $data = array_merge($defaultData, $data);

            $response = $this->sendRequest('payment/SearchTradeMulti.idPass', $data);

            if (GMOHelper::isError($response)) {
                Log::error('SearchTradeMulti Process Fail. OrderID: ' . $data['OrderID']);
                return [];
            }

            return GMOHelper::convertResponseIdPassToArray($response);
        } catch (\Exception $e) {
            Log::error($e);
            return [];
        }
    }
}
