<?php

$newsId = isset($_GET['news']) ? (int)$_GET['news'] : null;

$db = new SQLite3('news.db');


if ($newsId !== null) {
    $newsFound = $db->query("SELECT * FROM news WHERE id = {$newsId}")->fetchArray();

    if ($newsFound !== null) {
        include 'news.php';
        news($newsFound);
    } else {
        http_response_code(404);
        echo "<h1>404 — Новость не найдена</h1>";
        echo "<p><a href='/'>Вернуться на главную</a></p>";
    }
}
else {
    $query = $db->query('SELECT * FROM news ORDER BY id DESC');
    $newsData = [];
    while ($row = $query->fetchArray()) {
        $newsData[] = $row;
    }

    include 'main_page.php';
    main_page(newsData: $newsData);
}

?>
