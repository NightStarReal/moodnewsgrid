<?php

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    include __DIR__ . "/add_news.php";
}
else {
    include __DIR__ . "/process_news.php";
}
