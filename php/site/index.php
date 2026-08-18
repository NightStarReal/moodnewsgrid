<?php

$url = parse_url($_SERVER["REQUEST_URI"]);

include __DIR__ . "/routes" . $url["path"] . "/index.php";
