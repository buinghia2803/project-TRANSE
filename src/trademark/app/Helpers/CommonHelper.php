<?php

namespace App\Helpers;

use App\Models\MNation;
use App\Models\MPrefecture;
use App\Models\PrecheckResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommonHelper
{
    /**
     * Set language system
     *
     * @param string|null $language
     * @return void
     */
    public static function setLanguage(?string $language): void
    {
        // Get only the language specified in config
        if (!empty($language) && array_key_exists($language, config('app.language') ?? [])) {
            session(['locale' => $language]);
        }
        // Set language for every route
        App::setLocale(Session::get('locale') ?? App::getLocale());
    }

    /**
     * Get language for select form
     *
     * @return array|null
     */
    public static function langOption(): ?array
    {
        if (!empty(config('app.language')) && count(config('app.language')) > 0) {
            $lang = [];
            foreach (config('app.language') ?? [] as $key => $value) {
                $lang[$key] = __($value['label']);
            }
            return $lang;
        }
        return [];
    }

    /**
     * Export Csv
     * $data = ['file_name' => '', 'fields' => [], 'data' => []]
     *
     * @param array $data
     * @return StreamedResponse
     */
    public static function exportCSV(array $data): StreamedResponse
    {
        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Encoding" => 'UTF-8',
            "Content-Disposition" => "attachment; filename=" . $data['file_name'] . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, $data['fields']);

            if (isset($data['header'])) {
                fputcsv($file, $data['header']);
            }

            foreach ($data['data'] as $dataRow) {
                fputcsv($file, $dataRow);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Set alertMsg session
     *
     * @param   Request $request
     * @param   string  $type    MESSAGE_ERROR | MESSAGE_WARNING | MESSAGE_SUCCESS.
     * @param   string  $message
     * @return  void
     */
    public static function setMessage(Request $request, string $type, string $message)
    {
        $request->session()->put('alertMsg', view('compoments.messages', [
            'alertMsg' => true,
            'type' => $type,
            'message' => $message,
        ])->render());
    }

    /**
     * Get alertMsg session
     *
     * @param   Request $request
     * @return  string|null
     */
    public static function getMessage(Request $request): ?string
    {
        $message = null;
        if ($request->session()->has('alertMsg')) {
            $message = $request->session()->get('alertMsg');
            $request->session()->forget('alertMsg');
        }
        return $message;
    }

    /**
     * Format Phone Number
     *
     * @param   string $number
     * @return  string|null
     */
    public static function formatPhoneNumber(string $number): ?string
    {
        return substr($number, 0, 4) . "-" . substr($number, 4, 3) . "-" . substr($number, 7, 4);
    }

    /**
     * Format Postal Code
     *
     * @param   string $number
     * @return  string|null
     */
    public static function formatPostalCode(string $number): ?string
    {
        return substr($number, 0, 3) . "-" . substr($number, 3, 4);
    }

    /**
     * Format Price
     *
     * @param   mixed   $number
     * @param   string  $unit
     * @param   integer $decimal
     * @return  string|null
     */
    public static function formatPrice($number, string $unit = '', int $decimal = 0): ?string
    {
        if (empty($number)) {
            return 0;
        }

        return number_format(floor($number * 10 / 10), $decimal, '.', ',') . $unit;
    }

    /**
     * Format Time
     *
     * @param string|null $timestamp
     * @param string $format
     * @param string|null $defaultText
     * @return  string|null
     */
    public static function formatTime(?string $timestamp, string $format = 'Y年m月d日', string $defaultText = null): ?string
    {
        if (empty($timestamp)) {
            return $defaultText ?? null;
        }

        $japaneseEra = self::formatJapaneseEra($timestamp);
        $yearName = $japaneseEra['year_name'] ?? '';
        $yearEra = $japaneseEra['year_era'] ?? '';
        $format = str_replace('E', $yearName, $format);
        $format = str_replace('e', $yearEra, $format);

        return Carbon::parse($timestamp)->format($format);
    }

    /**
     * Format Japanese Era
     *
     * @param string $timestamp
     * @return  array
     */
    public static function formatJapaneseEra(string $timestamp): array
    {
        $timestamp = Carbon::parse($timestamp);
        $timestampYear = $timestamp->year;

        if ($timestampYear >= 2019) {
            $yearName = '令和'; // Reiwa
            $yearEra = $timestampYear - 2019 + 1;
        } elseif ($timestampYear >= 1989) {
            $yearName = '平成'; // Heisei
            $yearEra = $timestampYear - 1989 + 1;
        } elseif ($timestampYear >= 1926) {
            $yearName = '昭和'; // Shōwa
            $yearEra = $timestampYear - 1926 + 1;
        } elseif ($timestampYear >= 1912) {
            $yearName = '大正'; // Taisho
            $yearEra = $timestampYear - 1912 + 1;
        } else {
            $yearName = '明治'; // Meiji
            $yearEra = '';
        }

        return [
            'year_name' => $yearName,
            'year_era' => $yearEra,
        ];
    }

    /**
     * Generate Address JP
     * Get API yubinbango
     *
     * @param   string $postCode
     * @return  array|null
     */
    public static function generateAddressJP(string $postCode): ?array
    {
        try {
            $region = config('region.ja');

            $fileNumber = substr($postCode, 0, 3);
            $apiURL = 'https://yubinbango.github.io/yubinbango-data/data/' . $fileNumber . '.js';

            $response = Http::get($apiURL);
            if ($response->status() == CODE_SUCCESS_200) {
                $responseBody = $response->body();
                $data = json_decode(substr($responseBody, 7, -3), true);

                $postCodeData = $data[$postCode] ?? [];
                if (!empty($postCodeData)) {
                    $postCodeData[0] = $region[$postCodeData[0]] ?? '';
                    return [
                        'data' => $postCodeData,
                        'address' => implode('', $postCodeData),
                    ];
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

    /**
     * Get Prefecture Info
     *
     * @param integer|null $prefectureID
     * @param array $relation
     * @return  mixed
     */
    public static function getPrefecture(?int $prefectureID = null, array $relation = [])
    {
        $prefectures = MPrefecture::query();

        if (!empty($relation)) {
            $prefectures = $prefectures->with($relation);
        }

        if ($prefectureID !== null) {
            $prefectures = $prefectures->where('id', $prefectureID)->first();
        } else {
            $prefectures = $prefectures->get();
        }

        return $prefectures;
    }

    /**
     * Get Prefecture Info
     *
     * @param int|null $nationID
     * @param array $relation
     * @return  mixed
     */
    public static function getNation(?int $nationID = null, array $relation = [])
    {
        $nations = MNation::query();

        if (!empty($relation)) {
            $nations = $nations->with($relation);
        }

        if ($nationID !== null) {
            $nations = $nations->where('id', $nationID)->first();
        } else {
            $nations = $nations->get();
        }

        return $nations;
    }

    /**
     * Get Prefecture Info
     *
     * @param int $nationID
     * @param int $prefectureID
     * @param string $addressSecond
     * @param string $addressThree
     * @return array
     */
    public static function getInfoAddress(int $nationID, int $prefectureID, string $addressSecond = '', string $addressThree = '')
    {
        if ($nationID == NATION_JAPAN_ID) {
            $prefecture = self::getPrefecture($prefectureID);

            $prefectureName = ($prefecture != null) ? $prefecture->name : null;

            return [
                'prefectureName' => $prefectureName ?? '-',
                'addressSecond' => $addressSecond ?? '-',
                'addressThree' => $addressThree ?? '-',
            ];
        } else {
            $nation = self::getNation($nationID);

            $nationName = ($nation != null) ? $nation->name : null;

            return [
                'nation' => $nationName ?? '-',
                'addressThree' => $addressThree ?? '-',
            ];
        }
    }

    /**
     * Get result detail precheck
     *
     * @param mixed $identification
     * @param mixed $similar
     * @return string
     */
    public static function getResultDetailPrecheck($identification, $similar): string
    {
        $key = $identification;
        if ($identification < $similar) {
            $key = $similar;
        }

        return PrecheckResult::listRanking()[$key];
    }

    /**
     * Paginate for Collection
     *
     * @param Collection $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public static function paginate(Collection $items, int $perPage = 15, $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        return (new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options))->withPath('');
    }

    /**
     * Set session back url
     *
     * @param string|null $currenUrl
     * @param string $urlBackDefault
     * @return string
     */
    public static function setBackUrl(string $urlBackDefault, string $currenUrl = null): string
    {
        $backUrl = url()->previous();
        $backUrlNotParam = explode('?', $backUrl)[0];

        if (empty($currenUrl)) {
            $currenUrl = url()->current();
        }

        if ($backUrlNotParam == $currenUrl) {
            $backUrl = Session::get(BACK_URL, $urlBackDefault);
        }

        Session::put(BACK_URL, $backUrl);

        return Session::get(BACK_URL);
    }

    /**
     * Soft Collection
     *
     * @param Collection $data
     * @param string $softField
     * @param string $softType
     * @return Collection
     */
    public static function softCollection(Collection $data, string $softField, string $softType): Collection
    {
        if ($softType == SORT_TYPE_DESC) {
            $data = $data->sortByDesc($softField);
        } else {
            $data = $data->sortBy($softField);
        }

        return $data;
    }

    /**
     * Minify Html
     *
     * @param string $string
     * @return Collection
     */
    public static function minifyHtml(string $string): string
    {
        $replace = array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/"                  => '<?php ',
            "/\n([\S])/"                => '$1',
            "/\r/"                      => '',
            "/\n/"                      => '',
            "/\t/"                      => '',
            "/ +/"                      => ' ',
        );
        $string = preg_replace(array_keys($replace), array_values($replace), $string);

        return $string;
    }

    /**
     * Convert Number to Fullwidth
     *
     * @param int $input
     * @return string
     */
    public static function convertNumberToFullwidth(int $input)
    {
        $output = '';
        $stringToArr = str_split($input);
        foreach ($stringToArr as $unit) {
            switch ($unit) {
                case 0:
                    $output .= '０';
                    break;
                case 1:
                    $output .= '１';
                    break;
                case 2:
                    $output .= '２';
                    break;
                case 3:
                    $output .= '３';
                    break;
                case 4:
                    $output .= '４';
                    break;
                case 5:
                    $output .= '５';
                    break;
                case 6:
                    $output .= '６';
                    break;
                case 7:
                    $output .= '７';
                    break;
                case 8:
                    $output .= '８';
                    break;
                case 9:
                    $output .= '９';
                    break;
            }
        }

        return $output;
    }

    /**
     * Export Zip
     *
     * @param string $outputPath
     * @param string $zipDir
     * @return void
     */
    public static function exportZip(string $outputPath, string $zipDir)
    {
        if (file_exists($outputPath)) {
            unlink($outputPath);
        }

        $zip = new \ZipArchive();
        if ($zip->open($outputPath, \ZipArchive::CREATE) === true) {
            $allFiles = Storage::disk('local')->allFiles($zipDir);

            foreach ($allFiles as $file) {
                $filePath = public_path($file);
                $name = str_replace($zipDir, '', '/' . $file);

                $zip->addFile($filePath, $name);
            }
            $zip->close();
        }
    }
}
