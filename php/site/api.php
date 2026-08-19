<?php
function api(array $data): array {
    $api_url = 'https://api.z.ai/api/paas/v4/chat/completions';
    $api_key_file = fopen(getenv('API_KEY_FILE'), 'r');
    $api_key = trim(fread($api_key_file, filesize(getenv('API_KEY_FILE'))));
    $headers = [
        "Authorization: Bearer {$api_key}",
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        "response" => $response,
        "http_code" => $http_code
    ];
}

function api_factcheck(string $news): array {
    $data = [
        'model' => 'glm-4.5-flash',
        'messages' => [
            [
                "role" => "system",
                "content" => "You are a fact finder"
            ],
            [
                "role" => "user",
                "content" => "Get all the facts from this text. Facts are: names, dates, numbers, places, quotes. Answer the question in Russian. Text: " . $news
            ]
        ]
    ];
    return api(data: $data);
}

function api_mooder(string $news, string $facts, string $mood): array {
    $data = [
        'model' => 'glm-4.5-flash',
        'messages' => [
            [
                "role" => "system",
                "content" => "You are a writer"
            ],
            [
                "role" => "user",
                "content" => "White the text in a set mood. You must preserve the facts, listed below. Answer the question in Russian. Mood: $mood.\nFacts: $facts.\nText: $news"
            ]
        ]
    ];
    return api(data: $data);
}
