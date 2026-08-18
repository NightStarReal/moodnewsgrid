<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /add-news?status=error&message=' . urlencode('Неверный метод запроса'));
    exit;
}

$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$full_text = isset($_POST['full_text']) ? trim($_POST['full_text']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$tag = isset($_POST['tag']) ? trim($_POST['tag']) : 'tech';

$errors = [];

if (empty($url)) {
    $errors[] = 'URL новости обязателен для заполнения';
} elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
    $errors[] = 'Введите корректный URL';
}

if (!empty($errors)) {
    $errorMessage = implode('. ', $errors);
    header('Location: /add-news?status=error&message=' . urlencode($errorMessage));
    exit;
}

// Check if news already added
$db = new SQLite3('news.db');
$stmt = $db->prepare("SELECT * FROM news WHERE source = :url");
$stmt->bindValue(':url', $url, SQLITE3_TEXT);
$news = $stmt->execute();

if ($news->fetchArray() !== false) {
    $errorMessage = 'Новость уже добавлена.';
    header('Location: /add-news?status=error&message=' . urlencode($errorMessage));
    exit;
}
else {
    include "api.php";
    $facts = api_factcheck($full_text);
    $http_code = $facts["http_code"];
    $response = json_decode($facts["response"], true);

    if ($http_code === 200 || $http_code === 201) {
        $stmt = $db->prepare("INSERT INTO news (tag, title, full_text, facts, source) VALUES (:tag, :title, :full_text, :facts, :source)");
        $stmt->bindValue(':tag', $tag, SQLITE3_TEXT);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':full_text', $full_text, SQLITE3_TEXT);
        $stmt->bindValue(':facts', $response["choices"][0]["message"]["content"], SQLITE3_TEXT);
        $stmt->bindValue(':source', $url, SQLITE3_TEXT);
        $result = $stmt->execute();

        $successMessage = 'Новость успешно создана!';
        header('Location: /add-news?status=success&message=' . urlencode($successMessage));
    } else {
        $errorMessage = 'Ошибка при отправке на API. Код: ' . $http_code;
        header('Location: /add-news?status=error&message=' . urlencode($errorMessage));
        exit;
    }
}
?>
