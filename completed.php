<?php
if ($_POST['entryPlan'] == 'entry') {
  $mail_body = <<<EOT
-----
利用内容： 申し込み（依頼）
お名前： {$_POST['person_name']}
お名前ふりがな： {$_POST['furigana']}
貴社名：{$_POST['company_name']}
E-Mail：{$_POST['mail']}
電話番号：{$_POST['tel']}
都道府県：{$_POST['prefecture']}
メーカー：{$_POST['brand']}
お問い合わせ内容：{$_POST['memo']}
-----
EOT;
} else {
  $mail_body = <<<EOT
-----
利用内容： 問い合わせ
お名前： {$_POST['person_name']}
お名前ふりがな： {$_POST['furigana']}
貴社名：{$_POST['company_name']}
E-Mail：{$_POST['mail']}
電話番号：{$_POST['tel']}
お問い合わせ内容：{$_POST['memo']}
-----
EOT;
}

$client_mail_body = "--__BOUNDARY__\n";
$client_mail_body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
$client_mail_body .= <<<EOT
お問い合わせありがとうございました。

この度は医療ベッド買取.comへのお問い合わせありがとうございました。
以下の内容で送信いたしました。

$mail_body

2営業日以内に、担当者よりご連絡いたします。

よろしくお願いいたします。

================================
医療ベッド買取.com
Email:info@iryoubed-kaitori.com
Web: https://iryoubed-kaitori.com
TEL: 0120-502-319
================================
--__BOUNDARY__
EOT;

$service_mail_body = "--__BOUNDARY__\n";
$service_mail_body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
$service_mail_body .= <<<EOT
次の内容でお問い合わせがありました。

$mail_body

================================
医療ベッド買取.com
Email:info@iryoubed-kaitori.com
Web: https://iryoubed-kaitori.com
TEL: 0120-502-319
================================
--__BOUNDARY__\n
EOT;

for ($i = 0; $i < 3; $i++) {
  if ($_FILES['attachment_file']['size'][$i] > 0) {
    // ファイルを添付
    $service_mail_body .= "Content-Type: application/octet-stream; name=\"{$_FILES['attachment_file']['name'][$i]}\"\n";
    $service_mail_body .= "Content-Disposition: attachment; filename=\"{$_FILES['attachment_file']['name'][$i]}\"\n";
    $service_mail_body .= "Content-Transfer-Encoding: base64\n";
    $service_mail_body .= "\n";
    $service_mail_body .= chunk_split(base64_encode(file_get_contents($_FILES['attachment_file']['tmp_name'][$i])));
    $service_mail_body .= "--__BOUNDARY__\n";
  }
}

// メール送信
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// 送信者情報の設定
$header = '';
$header .= "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
$header .= "Return-Path: info@iryoubed-kaitori.com \r\n";
$header .= "From: 医療ベッド買取.com<info@iryoubed-kaitori.com> \r\n";
$header .= "Sender: 医療ベッド買取.com \r\n";
$header .= "Reply-To: info@iryoubed-kaitori.com \r\n";
$header .= "Organization: 医療ベッド買取.com \r\n";
$header .= "X-Sender: info@iryoubed-kaitori.com \r\n";
$header .= "X-Priority: 3 \r\n";

$send_mail_completed = true;

// クライアントにメール送信
if(!mb_send_mail($_POST['mail'], "医療ベッド買取.comへのお問い合わせありがとうございました", $client_mail_body, $header)){
  $send_mail_completed = false;
};
 
// サービス担当者にメール送信
// "okasei.kaigobed@gmail.com"
if(!mb_send_mail("yamasaki0406@gmail.com", "医療ベッド買取.comへのお問い合わせがありました", $service_mail_body, $header)){
  $send_mail_completed = false;
};

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>お問い合わせ</title>
    <link rel="stylesheet" href="./completed/style.css" />
</head>

<body>
    <?php if ($send_mail_completed): ?>
    <section class="completed">
        <div class="wrap">
            <h1 class="heading">送信完了しました</h1>
            <div class="text-wrap">
                <p class="text">折り返し、確認メール（自動返信メール）を送らせていただいております。</p>
                <p class="sub-text">二日営業日以内に担当者から電話またはメールにてご連絡させて頂きます。</p>
                <p class="sub-text-2">その他、ご不明な点がございましたらお気軽にお問い合わせ下さい。</p>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="failed">
        <div class="wrap">
            <h1 class="heading">エラーが発生しました</h1>
            <div class="text-wrap">
                <p class="text">内誤入力容のご確認の上、再度送信して下さい。</p>
                <p class="sub-text">その他、ご不明な点がございましたらお気軽にお問い合わせ下さい。</p>
            </div>
        </div>
    </section>
    <?php endif; ?>
</body>
</html>
