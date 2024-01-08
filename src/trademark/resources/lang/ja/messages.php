﻿<?php

return [
    'required' => 'この項目は必須です。',
    'error' => 'エラーが発生しました。再度お試しください。',
    'create_success' => 'データの作成に成功しました。',
    'create_fail' => 'エラーが発生しました。再度お試しください。',
    'update_success' => 'データの更新に成功しました。',
    'update_fail' => 'エラーが発生しました。再度お試しください。',
    'delete_success' => 'データの更新に削除しました。',
    'delete_fail' => 'エラーが発生しました。再度お試しください。',
    'valid_password' => '大文字、小文字、数字を含める8文字以上32文字以内で入力して下さい。',
    'wrong_password' => '現在のパスワードは間違っています。',
    'valid_image_format' => 'イメージ画像はjpgまたはpng形式でアップロードしてください。',
    'max_filesize' => 'ファイルの最大容量は:attrです。',
    'login_denied' => 'ログイン情報が正しくありません。管理者に連絡してください。',
    'permission_denied' => 'この機能を実行する権限がありません。',
    'delete_role_denied' => 'このロールは使用中のため削除できません。',
    'email_not_exist' => 'あなたのメールアドレスは登録されていません。',
    'forgot_password_success' => 'パスワード再設定メールを送信しました。メールボックスをご確認ください。',
    'reset_password_success' => 'パスワードの再設定に成功しました。',
    'user_login' => 'でログイン中',
    'flug_role_seki' => '確認依頼済みです。',
    'save_draft' => '保存されました。',
    'not_data' => 'データがありません。',
    'is_confirm_a011s' => 'お客様へ送信しました。',
    'required_choose_product_all' => '商品・サービス内容が選択されていません。',
    'required_product' => '商品・サービス名が入力されていません。',
    'unique_product' => '同じIDの商品・サービス名があります。商品・サービス名情報を編集してください。',
    'error_not_is_decision_all_data' => '決定：商品・サービス名欄に何も記載されていません。',
    'error_unique_email_second' => '連絡用メールアドレス-2と連絡用メールアドレス-3は同じです。もう一度入力してください。',
    'withdraw_success' => '退会しました。',
    "check_all" => '全てチェックしてください',
    "you_can_enter_up_to_100_items" => '入力できるのは最大100個までです。',
    "payment_success" => '<span>お申込みありがとうございます。</span><br><span>お支払い完了後にお手続きを進めます。</span>',
    'signup' => [
        'form' => [
            'required' => '入力してください。',
            'max_length' => '半角255文字以内で入力してください。',
            'Register_U001_E006' => '全角30字以内で入力してください。',
            'email_format' => 'メールアドレスを正しく入力してください。',
            'exist_email' => 'メールアドレスはすでに登録されています。',
            'invalid_code' => '認証コードが正しくありません。再度入力してください。',
            'Common_E013' => '認証コードが正しくありません。再度入力してください。',
            'Common_E002' => 'メールアドレスを正しく入力してください。'
        ],
        'expired_authentication' => '認証コードが正しくありません。再度入力してください。',
        'expired_authentication_v2' => 'コードの期限が切れました。再度お手続きしてください。',
        'text_email' => 'ご入力いただいたアドレスが会員情報として登録されます。',
    ],

    'registrant_information' => [
        'Common_E001' => '入力してください。',
        'Common_E016' => '全角50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
        'Common_E020' => '全角100文字以内で入力してください。'
    ],

    'support_first_time' => [
        'support_U011_E008' => '「装飾文字/ロゴ絵柄の画像」でお申込みの場合は、プレチェックサービスは受けられません',
        'message_nothing_in_decision' => '決定欄に何も記載されていません。',
        'message_flag_role_seki_a011' => '確認依頼済みです。',
    ],

    'profile_edit' => [
        'validate' => [
            'memberinfo_Edit_U001_E001' => '会員IDはすでに登録されています。別の会員IDを入力してください。',
            'Register_U001_E002' => '会員パスワードと確認用パスワードが一致しません。もう一度入力してください。',
            'Register_U001_E003' => 'メールアドレスと確認用メールアドレスが一致しません。もう一度入力してください。',
            'Register_U001_E007' => '全角50文字以内で入力してください。',
            'Register_U001_E008' => '全角100文字以内で入力してください。',
            'Common_E001' => '入力してください。',
            'Common_E002' => 'メールアドレスを正しく入力してください。',
            'Common_E005' => 'パスワードは6~18（文字と数字）、半角を入力してください。',
            'Common_E006' => 'フォーマットが正しくありません。',
            'Common_E016' => '全角50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E018' => '全角ひらがな50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E019' => 'フォーマットが正しくありません。ハイフン（-）なし半角数字で入力してください。',
            'Common_E020' => '全角100文字以内で入力してください。',
            'Common_E021' => '半角255文字以内で入力してください。',
            'Common_E022' => '全角ひらがな255文字以内で入力してください。',
            'Common_E013' => '認証コードが正しくありません。再度入力してください。',
            'Common_E025' => '選択してください。',
            'Common_E014' => '半角数字のみ11文字以内で入力してください。',
        ],
        'Register_U001_E001' => 'メールアドレスはすでに登録されています。',
        'precheck_report_success' => 'プレチェックレポート：お申し込み完了・調査レポート待ち',
        'password_x' => '*********'
    ],

    'common' => [
        'errors' => [
            'Common_E001' => '入力してください。',
            'Common_E002' => 'メールアドレスを正しく入力してください。',
            'Common_E003' => 'メールアドレスが正しくありません。',
            'Common_E005' => 'パスワードは8~16（文字と数字）、半角を入力してください。',
            'Common_E006' => 'フォーマットが正しくありません。',
            'Common_E009' => '全角数字のみ:attr桁。',
            'Common_E011' => '全角のみ50文字まで。',
            'Common_E013' => '認証コードが正しくありません。再度入力してください。',
            'Common_E014' => '半角数字のみ11文字以内で入力してください。',
            'Common_E0021' => '半角255文字以内で入力してください。',
            'Common_E025' => '選択してください。',
            'Common_E026' => '1000文字以内、全角を入力してください。',
            'Common_E029' => ':attr文字以内、全角を入力してください。',
            'Common_E020' => '全角100文字以内で入力してください。',
            'Common_E010' => '全角のみ50文字まで。',
            'Common_E016' => '全角50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E018' => '全角ひらがな50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E021' => '半角255文字以内で入力してください。',
            'Common_E023' => '画像はJPG、GIF、またはPNG形式アップロードしてください（3MBまで）。',
            'Common_E031' => '255文字以内、全半角を入力してください。',
            'Common_E031_2' => ':attr文字以内、全半角を入力してください。',
            'Forgot_ID_U000_E002' => '入力が正しくありません。',
            'Common_E024' => '500文字以下、全角を入力してください。',
            'Register_U001_E006' => '全角30字以内で入力してください。',
            'support_U011_E001' => 'フォーマットが正しくありません。',
            'Register_U001_E002' => '会員パスワードと確認用パスワードが一致しません。もう一度入力してください。',
            'Register_U001_E007' => '全角50文字以内で入力してください。',
            'Search_AI_U000_E001' => '＜追加リスト＞に商品・サービス名を追加し、「AI検索スタート」を押してください。',
            'support_U011_E002' => '全角20字以内で入力してください。',
        ],
        'successes' => [
            'Common_S003' => 'メールに送信しました。',
            'Common_S008' => '保存しました。',
            'Common_E030' => 'お客様へ送信しました。',
            'Freerireki_S001' => 'フリー履歴：対応中止した。'
        ]
    ],

    'recover_id' => [
        'first_attention' => 'IDを忘れた方は、以下にご登録のメールアドレスを入力して「送信」を押してください。',
        'second_attention' => 'ご登録のメールアドレスに会員IDが送られます。',
        'require_email_password' => '以下にご登録のメールアドレスとパスワードを入力して次へ進んでください。',
        'please_choose_answer' => '以下の回答を入力し、「次へ」をクリックしてください。',
        'pls_using_id_login' => 'このIDとパスワードを使ってログインし、会員情報編集画面から、メールアドレスの変更をお願い致します。',
        'not_contact_after_receive' => 'この後の連絡が来なくなりますので、必ず連絡の取れるメールアドレスをご登録ください。',
        'Recover_ID_U000_S001' => '登録のメールアドレスに、IDを送信しました。'
    ],

    'agent' => [
        'delete_attention' => 'この弁理士は全ての組み合わせからも削除されますが、よろしいでしょうか？'
    ],

    'forgot_password' => [
        'Register_U001_E007' => '全角50文字以内で入力してください。',
        'Forgot_Password_U000_E009' => '入力が正しくありません。',
        'id_error' => 'フォーマットが正しくありません。',
        'Forgot_ID_U000_E001' => 'メールアドレスが正しくありません。',
        'Forgot_Password_U000_E005' => 'パスワードが正しくありません。',
        'Forgot_Password_U000_E006' => '認証コードが正しくありません。',
        'Forgot_Password_U000_E007' => '認証コードの有効期限が切れました。',
        'Forgot_Password_U000_E010' => '認証コードは正しくありません。',
        'Forgot_Password_U000_E008' => 'IDまたは登録メールアドレスが正しくありません。',
        'Forgot_Password_U000_S001' => 'パスワードが正常に変更されました ',
        'email_already_exists' => 'メールアドレスはすでに登録されています。',
        'password_confirm' => '会員パスワードと確認用パスワードが一致しません。もう一度入力してください。',
        'email_not_exists' => 'メールアドレスが正しくありません。',

        'password_length' => '大文字、小文字、数字を含める8文字以上32文字以内で入力して下さい。',
    ],
    'question_answers' => [
        'QA_U000_E001' => 'フォーマットは間違いました。1000文字限定、全角文字を入力してください。',
        'max_size_image' => '最大ファイル容量は3MBです。',
    ],

    'login_user' => [
        'ID_password_invalid' => 'IDまたはパスワードが正しくありません。再度入力してください。'
    ],

    'trademark_form_information' => [
        'errors' => [
            'trademark_name_invalid' => '全角30字以内で入力してください。',
            'trademark_image_invalid' => '画像はJPG、GIF、またはPNG形式アップロードしてください（3MBまで）。'
        ]
    ],

    'user_common_payment' => [
        'attention' => 'お申込み内容をご確認のうえ、「決定」ボタンを押してください。',
        'attention_2' => '※源泉徴収税率は実手数料が100万円までは10.21%、100万円を超えると100万円を超えた額に対して20.42％となります。',
        'attention_3' => '※いかなる理由に関わらず、お申込み後の返金は一切ございません。',
        'Payment_notice_U000_S001' => 'お申し込み完了・決済待ち',
        'Payment_notice_U000_S002' => '決済完了・調査レポート待ち',
        'thank_you' => 'ご利用ありがとうございます。請求書情報をご覧ください',
        'thank_you_v2' => 'お申込みありがとうございました。AMSからの連絡をお待ちください。',
        'back' => 'トップページへ戻る',
        'invoice_issue' => '請求書発行',
        'receipt_issue' => '領収書発行'
    ],

    'update_profile' => [
        'system_error' => 'システムエラー',
        'already_registered' => '会員IDはすでに登録されています。別の会員IDを入力してください。',
        'note_1' => 'AMSの各種サービスをご利用いただくには、会員登録（無料）が必要となります。',
        'note_2' => 'ご登録に必要なお客様情報を入力してください。',
        'note_dot_err' => '印の項目は必須です。',
        'note_name' => '※法人名（ふりがな含む）は、ご登録後の変更はできません。変更には別途ご申請が必要です。',
        'text_houjin_bangou' => '国税庁ホームページ',
        'note_houjin_bangou' => 'からお調べいただけます。',
        'value_info_postal_code' => '郵便番号から住所を入力',
        'note_info_address_second' => 'ひらがな・カタカナ・漢字・数字で入力してください（記号は全角ハイフンのみ使用可）。',
        'note_info_address_three' => '全角文字で入力してください。',
        'text_value_info_member_id' => 'ID重複確認',
        'note_info_member_id' => '※アルファベットと数字を混ぜた8文字以上30文字まで及び使用可能な記号（「-」「.」「_」「@」）。',
        'note_password' => '※アルファベットと数字を混ぜた8文字以上16文字まで。',
        'note_info_birthay_1' => '※ご登録後の変更できません。会員情報の変更を申請される際、',
        'note_info_birthay_2' => '本人確認として使用する場合がありますので正しい生年月日をご入力ください。',
        'note_info_answer' => '※パスワード復帰時に必要となります。',
        'note_email' => '※連絡用メールアドレス-2と3にもAMSからの連絡が同時に送られます。',
        'btn_submit' => '確認画面へ',
        'form' => [
            'Common_E001' => '入力してください。',
            'Common_E025' => '選択してください。',
            'Common_E016' => '全角50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E018' => '全角ひらがな50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
            'Common_E019' => 'フォーマットが正しくありません。ハイフン（-）なし半角数字で入力してください。',
            'Common_E020' => '全角100文字以内で入力してください。',
            'message_phone' => '半角数字のみ11文字以内で入力してください。',
            'Register_U001_E004' => '法人番号は半角数字13桁で入力してください。',
            'Common_E006' => 'フォーマットが正しくありません。',
            'Common_E005' => 'パスワードは8~16（文字と数字）、半角を入力してください。',
            'Register_U001_E002' => '会員パスワードと確認用パスワードが一致しません。もう一度入力してください。',
            'Register_U001_E008' => '全角100文字以内で入力してください。',
            'Register_U001_E007' => '全角50文字以内で入力してください。',
            'Common_E021' => '半角255文字以内で入力してください。',
            'Common_E002' => 'メールアドレスを正しく入力してください。',
            'Common_E022' => '全角ひらがな255文字以内で入力してください。',
            'Register_U001_E003' => 'メールアドレスと確認用メールアドレスが一致しません。もう一度入力してください。'
        ],
    ],

    "precheck" => [
        "errors" => [
            "U021_E001" => "商標名を入力してください",
            "U021_E002" => "類似群コードがまだない商品・サービス名があります。",
            "U021_E003" => "商標の識別力を評価してください",
            "U021_E004" => "商標の簡易調査を完了してください。",
            "U021_E005" => "「決定：識別力」列の評価結果をきめてください。",
            "U021_E006" => "「決定：類似調査」列の評価結果をきめてください。",
            "U021_E007" => "「確認＆ロック」にチェックしてください。"
        ],
        'precheck_u021n_note_1' => '商標名について、指定の商品・サービス名で全く同じ商標名があるかどうかのみ調べます。',
        'precheck_u021n_note_2' => '3商品名まで',
        'precheck_u021n_note_3' => ' 円、以降3商品名ごとに ',
        'precheck_u021n_note_4' => '商標名について、指定の商品・サービス名で同じ商標名（同一）や似ている商標名（類似）があるか、一般的に使われているかどうか（識別力）を調べます。',
        'precheck_u021n_note_5' => '3商品名まで ',
        'precheck_u021n_note_6' => ' 円、以降3商品名ごとに ',
        'success' => '保存されました。',
        'success_a011' => '確認依頼済みです。',
        'success_u203b02' => '担当者に確認依頼済みです。',
        'note_from_page_u020b' => '※装飾文字、ロゴ絵柄等でのプレチェックサービスは受けられません。<br>
        　戻って「出願申込へ進む」を選択してください。<br>',
        'send_message_precheck_to_user' => 'お客様へ送信しました。',
        'error_not_select_suitable_u021n' => '「確認＆ロック」にチェックしてください',
    ],

    'general' => [
        'Common_E001' => '入力してください。',
        'Common_E002' => 'メールアドレスを正しく入力してください。',
        'Common_S003' => 'メールに送信しました。',
        'Common_E003' => 'メールアドレスが正しくありません。',
        'Common_E004' => 'パスワードが正しくありません。再度入力してください。',
        'Common_E005' => 'パスワードは8~16（文字と数字）、半角を入力してください。',
        'Common_E006' => 'フォーマットが正しくありません。',
        'Common_E007' => 'フォーマットが正しくありません。',
        'Common_S008' => '保存しました。',
        'Common_E009' => '全角数字のみ9桁。',
        'Common_E010' => '全角のみ50文字まで。',
        'Common_E011' => '全角数字のみ6文字。',
        'Common_E013' => '認証コードが正しくありません。再度入力してください。',
        'Common_E014' => '半角数字のみ11文字以内で入力してください。',
        'Common_E015' => '全角ｘｘ文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。',
        'Common_E016' => '全角50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
        'Common_E017' => 'フォーマットが正しくありません。',
        'Common_E018' => '全角ひらがな50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
        'Common_E019' => 'フォーマットが正しくありません。ハイフン（-）なし半角数字で入力してください。',
        'Common_E020' => '全角100文字以内で入力してください。',
        'Common_E021' => '半角255文字以内で入力してください。',
        'Common_E022' => '全角ひらがな255文字以内で入力してください。',
        'Common_E023' => '画像はJPG、GIF、またはPNG形式アップロードしてください（3MBまで）。',
        'Common_E024' => '500文字以下、全角を入力してください。',
        'Common_E025' => '選択してください。',
        'Common_E026' => '1000文字以内、全角を入力してください。',
        'Common_E027' => 'エラーが発生しました。後でもう一度お試しください。',
        'Common_E028' => '1書類は3MBまでアップロードしてください。',
        'Common_E029' => '255文字以内、全角を入力してください。',
        'Common_E030' => 'お客様へ送信しました。',
        'Common_E031' => '255文字以内、全半角を入力してください。',
        'Common_E032' => 'データはありません。',
        'Common_E033' => ':attrの識別番号はすでに登録されています。',
        'Common_E034' => '確認依頼済みです。',
        'Common_E035' => 'お客様へ送信しました。',
        'Common_E036' => '最大 20 個のフリー履歴、新規作成できません',
        'Common_E037' => '最大3MBのPDFファイルをアップロードしてください。',
        'Common_E038' => '現在日以降の日付、拒絶通知対応期限日付以前の日付を入力してください。',
        'Common_E039' => '現在日以降の日付、特許庁への応答期限日付以前の日付を入力してください。',
        'Common_E040' => '最大3MBのXMLファイルをアップロードしてください。',
        'Common_S041' => '担当者に差し戻して、お知らせしました。',
        'Common_S042' => '現在日以降の日付を入力してください。',
        'Common_E043' => '依頼を送信しました。これ以上編集できません。',
        'Common_E044' => '必要資料提出を完了しました。これ以上編集できません。',
        'Common_E045' => '画像はJPG、GIF、BMP、またはPNG形式アップロードしてください（3MBまで）。',
        'Common_E046' => '500文字以内、全半角を入力してください。',
        'Common_E047' => 'データの更新に成功しました。',
        'Common_E048' =>  '確認依頼済みです。',
        'Common_E049' =>  '回答完了しました。',
        'Common_E050' =>  '方針案作成をリクエストしました。',
        'Common_E051' => '全角200文字以内で入力してください。',
        'Common_E052' => '50個以内で入力してください。',
        'Common_E053' => '「確認＆ロック」にチェックしてください。',
        'Common_E055' => '全半角1000文字以内を入力してください。',
        'Common_E056' => 'フォーマットが正しくありません。',
        'Common_E057' => ' お客様回答期限日は、特許庁への応答期限日付以前の日付を入力してください。',
        'Common_E059' => ' 利用中のサービスがあるため、退会できません。',
        'common_A058' => '全角ふりがな50文字以内で入力してください。記号はハイフン（－）と中黒（・）のみ使用できます。スペースは使用できません。',
        'Common_E058' => ' この方針案は選択できません。',
        'Common_E060' => '1書類は10MBまでアップロードしてください。',
        'Common_E060_1' => '1書類は3MBまでアップロードしてください。',
        'Forgot_ID_U000_E001' => 'メールアドレスが正しくありません。',
        'Forgot_ID_U000_E002' => '入力が正しくありません。',
        'Forgot_ID_U000_E003' => '50文字限定を入力してください。',
        'Forgot_Password_U000_E003' => '入力が正しくありません。',
        'Forgot_Password_U000_E004' => '入力が正しくありません。',
        'Forgot_Password_U000_E005' => 'パスワードが正しくありません。',
        'Forgot_Password_U000_E006' => '認証コードが正しくありません。',
        'Forgot_Password_U000_E007' => '認証コードの有効期限が切れました。',
        'Forgot_Password_U000_E008' => 'IDまたは登録メールアドレスが正しくありません。',
        'Forgot_Password_U000_E009' => '入力が正しくありません。',
        'Forgot_Password_U000_E010' => '認証コードは正しくありません。',
        'Forgot_Password_U000_S001' => 'パスワードが正常に変更されました ',
        'Register_U001_E001' => 'メールアドレスはすでに登録されています。',
        'Register_U001_E002' => '会員パスワードと確認用パスワードが一致しません。もう一度入力してください。',
        'Register_U001_E003' => 'メールアドレスと確認用メールアドレスが一致しません。もう一度入力してください。',
        'Register_U001_E004' => '法人番号は半角数字13桁で入力してください。',
        'Register_U001_E005' => '',
        'Register_U001_E006' => '全角30字以内で入力してください。',
        'Register_U001_E007' => '全角50文字以内で入力してください。',
        'Register_U001_E008' => '全角100文字以内で入力してください。',
        'Login_U001_E001' => 'IDまたはパスワードが正しくありません。再度入力してください。',
        'memberinfo_Edit_U001_E001' => '会員IDはすでに登録されています。別の会員IDを入力してください。',
        'withdrawal_U000_S001' => '退会用URLを記載したメールが送信されました。',
        'Import_A000_E001' => '1度にアップロードできるのは20ファイルまでです。',
        'Import_A000_E001_v2' => '1度にアップロードできるのは100ファイルまでです。',
        'Import_A000_E002' => '「確認」されていない案件があります。チェックのうえ「取り込み」を行ってください。',
        'Import_A000_S001' => '取り込み完了しました。',
        'Import_A000_E003' => 'エラーが発生しました。後でもう一度お試しください。',
        'Import_A000_E004' => 'はすでにシステムに存在します。',
        'Cancel_U201_S001' => '中止しました。',
        'Cancel_Error_U201_S001' => 'エラーが発生しました。',
        'correspondence_U201_E001' => '登録可能性評価のランクは評価してください。',
        'correspondence_A204_E001' => '「完了」にチェックしてください。',
        'correspondence_A204_E002' => '「対応策を完了」にチェックしてください。',
        'correspondence_A204_E003' => '必要資料を確認完了しました。再度資料依頼できません。',
        'question_A202_E001' => '質問を追加するか、事前質問は不要のチェックボックスにチェックしてください。',
        'question_A202_E002' => '質問欄が入力されていません。ご確認ください。',
        'correspondence_A204hann_E001' => '「確認」にチェックしててください。',
        'correspondence_A204hann_E002' => '「必要資料送付期日」を選択してください。',
        'correspondence_A204hann_E003' => '「この対応策を完了」にチェックしててください。',
        'correspondence_U204_E004' => 'ファイルはJPG、PDF形式アップロードしてください（3MBまで）。',
        'correspondence_U204_E005' => '必要資料をアップロードしてください。',
        'Question_U202_S001' => 'ご回答ありがとうございます。確認中ですので少々お待ちください。',
        'correspondence_A203_E001' => '方針策を入力してください。',
        'correspondence_A203_E002' => '同じ理由を選択できません。',
        'correspondence_A203_E003' => '理由を選択してください。',
        'correspondence_A203_E004' => '選択されていない理由があるので、商品テーブル編集へ進むことができません。',
        'correspondence_A205_E001' => '3文字以内、半角数字を入力してください。',
        'correspondence_A205_E002' => 'RGB、gif白黒外画像アップロードしてください（3MBまで）。',
        'correspondence_A205s_E001' => '「確認しました」にチェックしててください。',
        'correspondence_A201b_E001' => 'Common_E024',
        'support_U011_E001' => 'フォーマットが正しくありません。',
        'support_U011_E002' => '全角20字以内で入力してください。',
        'support_A011_E003' => 'フォーマットが正しくありません。「半角」、5桁NNXNN（Nは数字、Xはアルファベット）を入力してください。',
        'support_A011_E004' => '商品・サービス名を選択してから編集してください。',
        'support_A011_E005' => '修正：商品・サービス名欄に何も記載されていません。',
        'support_A011_E006' => '決定：商品・サービス名欄に何も記載されていません。',
        'support_U011_E007' => 'パックAの場合はお選びいただけません。',
        'support_U011_E008' => '「装飾文字/ロゴ絵柄の画像」でお申込みの場合は、プレチェックサービスは受けられません',
        'QA_U000_S001' => 'お問い合わせありがとうございます。確認中ですので少々お待ちください。',
        'QA_U000_E001' => 'フォーマットは間違いました。500文字限定、全角文字を入力してください。',
        'QA_U000_S002' => 'ご回答いただきありがとうございます。',
        'QA_U000_E002' => '確定する前に、回答を決定してください。',
        'QA_U000_E003' => '全ての内容を確定してください。',
        'QA_U000_E004' => 'ファイルをアップロードしてください。',
        'QA_U000_E005' => '1書類は最大3MBまでアップロードしてください。',
        'QA_U000_E006' => '確定する前に、質問を決定してください。',
        'Application_U031_E001' => '区分と商品・サービス名を選択してください',
        'Application_U031_E002' => '【区分、商品・サービス名】テーブルではデータがありません。出願を希望する商品・サービス名を入力してください。',
        'Application_U031_E003' => 'プランを選択してください',
        'Application_U031_E004' => '追加商品・サービス名を選択してください',
        'Register_trademark_A303_E001' => '現在の日付より古い日付を選択してください。',
        'Register_trademark_A303_E002' => '「発送日」以降の日付を選択してください。',
        'Register_trademark_A303_E003' => '現在より 40 日以上前の日付を選択してください。',
        'Register_trademark_A303_E004' => '登録日を選択してください。',
        'Register_trademark_A303_E005' => 'フォーマットが間違いました。半角、255数字以内で入力してください。',
        'Register_trademark_A303_E006' => '正しい日付を入力してください。',
        'Register_trademark_A303_E007' => '登録番号はすでに登録されています。別の登録番号を入力してください。',
        'Updateperiod_U302_E001' => '委任状の内容を確認してください',
        'Updateperiod_U302_E002' => '更新対象を選択してください',
        'Precheck_U021_E001' => '商標名を入力してください',
        'Precheck_U021_E002' => '類似群コードがまだない商品・サービス名があります。',
        'Precheck_U021_E003' => '商標の識別力を評価してください',
        'Precheck_U021_E004' => '商標の簡易調査を完了してください。',
        'Precheck_U021_E005' => '「決定：識別力」列の評価結果をきめてください。',
        'Precheck_U021_E006' => '「決定：類似調査」列の評価結果をきめてください。',
        'Precheck_U021_E007' => '「確認＆ロック」にチェックしてください。',
        'Hoshin_A203_S001' => 'お客様へ送信しました。',
        'Hoshin_A203_E001' => '「決定」の内容はありません。',
        'Hoshin_A203_E002' => '決定したら、「修正」で追加した区分、商品・サービス名が削除されます。本当に決定しますか。',
        'Freerireki_E001' => '1円から100000円までの半角数字を入力してください。',
        'Freerireki_E002' => '「お客様からの回答が必要」を選択してください。',
        'Freerireki_E003' => 'ファイルをアップロードしてください。',
        'Freerireki_E004' => 'お客様回答期限日を選択してください。',
        'Freerireki_E005' => '「お客様からの回答が必要」を選択しているため、「所内処理」ができません。',
        'Sentaku_U203_E001' => '選択できません。',
        'Ankentop_A000_E001' => '最大3MBのPDFファイルをアップロードしてください。',
        'Ankentop_U000_E001' => '振込待ち案件がある。処理してください。',
        'Payment_notice_U000_S001' => 'お申し込み完了・決済待ち',
        'Payment_notice_U000_S002' => '決済完了・調査レポート待ち',
        'Search_AI_U000_E001' => '＜追加リスト＞に商品・サービス名を追加し、「AI検索スタート」を押してください。',
        'Search_AI_select_product' => '商品・サービス名を選択してください。',
        'Recover_ID_U000_S001' => '登録のメールアドレスに、IDを送信しました。',
        'Recover_ID_U000_S002' => 'パスワードを正常に変更',
        'Send_Mail_Success' => 'メール送信成功',
        'QA_save' => '保存されました。',
        'tantou_send_to_seki' => '確認依頼済みです。',
        'tantou_question_to_seki' => '確認依頼済みです。',
        'seki_submit_to_user' => 'お客様へ送信しました。',
        'seki_submit_to_lawyer' => '担当弁理士へ送信しました。',
        'QA_save_seki' => 'お客様へ送信しました。',
        'Payment_notice_U000_E001' => '正しく入力してください。',
        'before_date_now' => '現在日以降の日付',
        'after_sending_noti_rejection_date' => '拒絶通知対応期限日付以前の日付を入力してください',
        'update_fail' => 'エラーが発生しました。再度お試しください。',
        'update_success' => 'データが更新されました。',
        'wrong_format' => '間違ったフォーマット',
        'requested_a_draft_policy' => '方針案作成をリクエストしました。',
        'Trademark_over_50_record' => 'お申し込み前の商標の件数は50件超えました。',
        'Hoshin_U203_S001' => '方針案選択した',
        'extend_U210_E001' => '銀行振込で支払い期限が過ぎているため更新できません',
        'extend_U210_E002' => 'クレジットカードで支払い期限が過ぎているため更新できません',
        'Hoshin_U203_S001' => '方針案選択した',
        'Hoshin_U203_S002' => '方針案再作成のリクエストを送信しました',
        'No_A_Judgment' => 'A判定はありません。',
        'withdraw_mail_title' => '【AMS】商標出願包括管理システム 退会手続きのご案内',
        'returned_unprocessed' => '未処理に戻しました',
        'send_to_tantou' => '担当者へ送信しました。',
        'blocker_popup_is_enabled' => 'ブロッカーポップアップが有効になっています。このサイトを例外リストに追加し、再度お試しください',
        'show_modal_success' => '保存しました。',
        'a000goods_master_detail_E0001' => 'フォーマットが正しくありません。「半角」、6桁（数字、アルファベット）を入力してください。',
        'a000goods_master_detail_E0002' => '親商品名No.は存在しました。もう一度試してください。',
        'max_length_255' => '255文字以内で入力してください。',
        'duplicate_product_name' => '同じ区分の同じ商品名は複数入力できません。',
        'import_app_trademark_cancel' => '商標は中止されました。インポートできません。',
        'import_app_trademark_error' => 'XML ファイルが正しくありません。もう一度確認してください。',
        'import_trademark_not_exist' => '商標は登録されていないため、インポートできません。',
        'import_application_number_error' => '商標は登録されていないため、インポートできません。',
        'max_length_25' => '全角25文字までです',
    ],
    'apply_trademark' => [
        'title_cancel' => '出願：中止',
        'back' => '戻る',
        'confirm' => '確認',
        'message_cancel' => '<h3 class="eol">ご依頼に基づき、本件のお手続きを中止致します。<br />
            中止の場合も、返金はございません。<br />
            よろしければ、「確認」ボタンを押してください。<br />
            <br />
            中止せずに、お手続きされたい場合は、「戻る」ボタンを押してください。</h3>',
    ],
    'payment' => [
        'send_mail_success' => 'メール送信済み'
    ],

    'free_histories' => [
        'max_20' => '最大 20 個のフリー履歴、新規作成できません。',
        'has_send_seki' => '確認依頼済みです。',
        'has_send_user' => 'お客様へ送信しました。',
        'message_type_1' => '事務担当へ送信しました。',
        'message_type_2' => 'フリー履歴追加完了。',
    ],

    'import_xml' => [
        'not_check_null' => 'インポート先のないXMLで「確認」されていない案件があります。チェックのうえ「取り込み」を行ってください。',
        'not_check_close' => 'クローズした案件で「確認」されていない案件があります。チェックのうえ「取り込み」を行ってください。',
        'not_check_duplicate' => '重複しているXMLで「確認」されていない案件があります。チェックのうえ「取り込み」を行ってください。',
        'system_error' => 'エラーが発生しました。再度お試しください',
        'file_error' => 'ファイルのアップロードにエラーが発生しました。再度お試しください',
        'duplicate_XML_files' => 'XML ファイルが重複しています。もう一度確認してください。'
    ],
    'u202' => [
        'content_messages_to_admin' => '責任者　拒絶理由通知対応：事前質問承認・再作成',
        'content_messages_to_user' => '拒絶理由通知対応：事前質問',
        'content_messages_attribute' => '所内処理',
    ],
    'a202n_s' => [
        'message_notice_content' => '責任者　拒絶理由通知対応：事前質問連絡済',
        'message_notice_attribute' => 'お客様',
    ],

    'plan' => [
        'register_date_null' => '登録可能性評価レポート申込を完了しました。',
    ],
    'a203s' => [
        'note_text' => '◎→かなり高い ○→高い △→低い ×→極めて困難'
    ],
    'a201' => [
        'mess_time' => '拒絶通知対応期限日を越えています。',
        'title1' => '理由の数を設定します。既に設定した内容がある場合、全て上書きされます。',
        'warning_dont_submit' => '理由の数は一致しません。再度お試しください。',
        'warning_duplicate_law_regulations' => '同じ法令が選択されています。',
        'warning_change_reason' => '入力した確度が全て削除されますが、よろしですか？',
    ],
    'u000taikai02' => [
        'check_please' => 'ご確認のうえ、チェックしてください。'
    ],
];
