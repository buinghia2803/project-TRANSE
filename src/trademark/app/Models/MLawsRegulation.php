<?php

namespace App\Models;

class MLawsRegulation extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rank',
    ];

    // Rank
    const RANK_A = 'A';
    const RANK_B = 'B';
    const RANK_C = 'C';
    const RANK_C_D = 'C~D';
    const RANK_D = 'D';
    const RANK_E = 'E';

    const ARRAY_CONTENT_DEFAULT = [
        '1' => '指定した商品・サービスが広範囲に及んでいるため、本当にそれらの業務を行うかどうか疑わしいという理由で拒絶されました。',
        '2' => '指定した商品・サービスが国家資格等がないと提供できないものであるため、本当にそれらの業務を行うかどうか疑わしい等の理由で拒絶されました。',
        '3' => '商標が、指定した商品・サービスについて、一般的なものである又は品質を誤認させるから登録すべきでないという理由で拒絶されました。',
        '4' => '商標が、公益的な理由から登録すべきでないとして拒絶されました。',
        '5' => '商標が、他人の権利と抵触するため登録すべきでないという理由で拒絶されました。',
        '6' => '商標が、同じ日に出願された他人の商標と類似するため登録すべきでないという理由で拒絶されました。',
        '7' => '商標が、過去に不正に使用されたものであるため登録すべきでないという理由で拒絶されました。',
        '8' => '出願人が、日本に住所を有しない外国人であるため登録すべきでないという理由で拒絶されました。',
        '9' => '商品・サービスの内容が不明確である又は区分が誤っているという理由で拒絶されました。',
        '10' => '上記に記載の理由以外で拒絶されました。'
    ];
}
