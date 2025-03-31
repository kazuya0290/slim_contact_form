<?php declare(strict_types=1);

if (!function_exists('old')) {
    function old($key, $default = null) {
        global $old;
        return $old[$key] ?? $default;
    }
}

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\PhpRenderer;
use Respect\Validation\Validator as v;

return function (App $app) {
    // デフォルトのルート
    $app->get('/', function (Request $request, Response $response) {
        $html = '<h1>お問い合わせフォームへ</h1>';
        $html .= '<p><a href="/contact">こちらをクリック</a> してお問い合わせフォームへ移動してください。</p>';
        
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });
    
    $app->get('/contact', function (Request $request, Response $response) {
    // 必要な変数を設定
    $old = []; // フォームの古い値
    
    // カテゴリー情報を設定（例として）
    $categories = [
        (object)['id' => 1, 'content' => '1.商品のお届けについて'],
        (object)['id' => 2, 'content' => '2.商品の交換について'],
        (object)['id' => 3, 'content' => '3.商品トラブル'],
        (object)['id' => 4, 'content' => '4.ショップへのお問い合わせ'],
        (object)['id' => 5, 'content' => '5.その他']
    ];
    
    $csrf_token = bin2hex(random_bytes(16)); // CSRFトークンを生成
    $base_url = "http://localhost/"; // ベースURL
    
    // PHPRendererを使ってテンプレートをレンダリング
    $renderer = new PhpRenderer(__DIR__ . '/../views/');
    return $renderer->render($response, 'contact_form.php', [
        'old' => $old,
        'categories' => $categories,
        'csrf_token' => $csrf_token,
        'base_url' => $base_url
    ]);
});

    
    $app->post('/confirm', function (Request $request, Response $response) {
    $params = (array)$request->getParsedBody();
    $errors = [];

    if (empty($params['first_name'])) {
        $errors['first_name'] = 'お名前（姓）の入力は必須です。';
    } elseif (!preg_match('/^[ぁ-んァ-ヶー一-龠a-zA-Z]+$/u', $params['first_name'])) {
    $errors['first_name'] = 'お名前（姓）は文字で入力してください。';
}
    
    if (empty($params['last_name'])) {
        $errors['last_name'] = 'お名前（名）の入力は必須です。';
    } elseif (!preg_match('/^[ぁ-んァ-ヶー一-龠a-zA-Z]+$/u', $params['last_name'])) {
    $errors['last_name'] = 'お名前（姓）は文字で入力してください。';
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($_POST['gender'])) {
        $errors['gender'] = '性別を選択してください。';
        }
    }

    if (empty($params['email'])) {
        $errors['email'] = 'メールアドレスの入力は必須です。';
    }elseif (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'メールアドレスは「ユーザー@ドメイン」形式で入力してください。';
    }

    if (empty($params['tel_1'])) {
        $errors['tel_1'] = '電話番号1の入力は必須です。';
    } elseif (!ctype_digit($params['tel_1'])) { 
    $errors['tel_1'] = '電話番号1は半角の数値で入力してください。';
    }

    if (empty($params['tel_2'])) {
        $errors['tel_2'] = '電話番号2の入力は必須です。';
    }elseif (!ctype_digit($params['tel_2'])) { 
    $errors['tel_2'] = '電話番号2は半角の数値で入力してください。';
    }

    if (empty($params['tel_3'])) {
        $errors['tel_3'] = '電話番号3の入力は必須です。';
    }elseif (!ctype_digit($params['tel_3'])) { 
    $errors['tel_3'] = '電話番号3は半角の数値で入力してください。';
    }
    
    if (empty($params['address'])) {
        $errors['address'] = '住所の入力は必須です。';
    }elseif (!is_string($params['address'])) {
        $errors['address'] = '住所は文字で入力してください。';
    }

    if (empty($params['category_id'])) {
        $errors['category_id'] = 'お問い合わせの種類を選択してください。';
    }
    
    if (empty($params['detail'])) {
        $errors['detail'] = 'お問い合わせ内容を記入してください。';
    }

    if (count($errors) > 0) {
        // エラーがあればフォームに戻す
        $categories = [
            (object)['id' => 1, 'content' => '1.商品のお届けについて'],
            (object)['id' => 2, 'content' => '2.商品の交換について'],
            (object)['id' => 3, 'content' => '3.商品トラブル'],
            (object)['id' => 4, 'content' => '4.ショップへのお問い合わせ'],
            (object)['id' => 5, 'content' => '5.その他']
        ];

        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, 'contact_form.php', [
            'errors' => $errors,
            'old' => $params,
            'categories' => $categories,  // カテゴリーデータを再度渡す
            'csrf_token' => bin2hex(random_bytes(16)), // CSRFトークンを再生成
            'base_url' => "http://localhost/" // ベースURL
        ]);
    }

    $renderer = new PhpRenderer(__DIR__ . '/../views/');
    return $renderer->render($response, 'confirm.php', [
        'first_name' => $params['first_name'],
        'last_name' => $params['last_name']
    ]);
});


    // 送信処理を行うルート
$app->post('/send', function (Request $request, Response $response) {
    // フォームから送信されたデータを取得
    $data = $request->getParsedBody();
    
    // CSRFトークンの検証
    
    // データベースへの保存処理
    try {
        $pdo = new PDO('mysql:host=slim-db;dbname=slim_db;charset=utf8mb4', 'slim_user', 'slim_pass');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // ここで必要なデータを保存
        $stmt = $pdo->prepare("INSERT INTO contacts (first_name, last_name, gender, email, tel, address, building, category_id, detail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['gender'] ?? '',
            $data['email'] ?? '',
            $data['tel'] ?? '',
            $data['address'] ?? '',
            $data['building'] ?? '',
            $data['category_id'] ?? '',
            $data['detail'] ?? ''
        ]);
        
        // 完了画面にリダイレクト
        return $response->withHeader('Location', '/complete')->withStatus(302);
    } catch(PDOException $e) {
        // エラー処理
        $response->getBody()->write('エラーが発生しました: ' . $e->getMessage());
        return $response->withStatus(500);
    }
});

// 完了画面を表示するルート
$app->get('/complete', function (Request $request, Response $response) {
    $renderer = new PhpRenderer(__DIR__ . '/../views/');
    return $renderer->render($response, 'complete.php');
});
    
    // CSSファイルを提供するルート
    $app->get('/public/css/{filename}', function ($request, $response, $args) {
        $filename = $args['filename'];
        $filepath = __DIR__ . '/../public/css/' . $filename;
        
        if (file_exists($filepath)) {
            $response->getBody()->write(file_get_contents($filepath));
            return $response->withHeader('Content-Type', mime_content_type($filepath));
        }
        
        return $response->withStatus(404);
    });
};