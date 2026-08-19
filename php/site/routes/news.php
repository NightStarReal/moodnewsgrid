<?php
function news(array $foundNews) {
    $mood = isset($_GET['mood']) ? $_GET['mood'] : null;

    $moodSettings = [
            'happy' => [
                'emoji' => ':)',
                'class' => 'mood-happy',
                'name' => 'счастливом'
            ],
            'sad' => [
                'emoji' => ':(',
                'class' => 'mood-sad',
                'name' => 'грустном',
            ],
            'neutral' => [
                'emoji' => ':|',
                'class' => 'mood-neutral',
                'name' => 'неитральном'
            ],
            'ironic' => [
                'emoji' => '(:',
                'class' => 'mood-ironic',
                'name' => 'ироничном'
            ],
            'boring' => [
                'emoji' => '',
                'class' => '',
                'name' => 'душном'
            ]
        ];
    if (!in_array($mood, array_keys($moodSettings))) {
        $mood = 'boring';
    }

    $full_text = "";
    // get a mood
    if ($mood != 'boring') {
        $db = new SQLite3('news.db');
        $stmt = $db->prepare("SELECT * FROM moods WHERE news_id = :news_id AND mood = :mood");
        $stmt->bindParam(":news_id", $foundNews["id"], SQLITE3_INTEGER);
        $stmt->bindParam(":mood", $mood, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray();

        if ($result === false) {
            include "api.php";
            $api_result = api_mooder(facts: $foundNews["facts"], news: $foundNews["full_text"], mood: $mood);
            $http_code = $api_result["http_code"];
            $response = json_decode($api_result["response"], true);

                if ($http_code === 200 || $http_code === 201) {
                    $full_text = $response["choices"][0]["message"]["content"];
                    $stmt = $db->prepare("INSERT INTO moods (news_id, mood, full_text) VALUES (:news_id, :mood, :full_text)");
                    $stmt->bindValue(':news_id', $foundNews["id"], SQLITE3_INTEGER);
                    $stmt->bindValue(':mood', $mood, SQLITE3_TEXT);
                    $stmt->bindValue(':full_text', $full_text, SQLITE3_TEXT);
                    $result = $stmt->execute();
                } else {
                    $full_text = "Произошла ошибка при задавании вопросов нейросети. Повторите попытку позже.";
                }
        }
        else {
            $full_text = $result["full_text"];
        }

    }
    else {
        $full_text = $foundNews["full_text"];
    }


    $currentMood = $moodSettings[$mood];

    function moodLink($moodValue) {
        $params = $_GET;
        if ($moodValue != 'boring') {
            $params['mood'] = $moodValue;
        }
        else {
            unset($params['mood']);
        }
        return '?' . http_build_query($params);
    }
    ?>
    <!DOCTYPE html>
    <html lang="ru">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title><?php echo htmlspecialchars($foundNews['title']); ?> — NewsGrid</title>
            <link rel="stylesheet" href="styles.css" />
        </head>
        <body>
            <div class="container">
                <div class="single-news">
                    <a href="/" class="back-link">← Назад к списку новостей</a>

                    <div class="mood-switcher">
                        <span class="mood-label">🎭 Настроение:</span>

                        <a href="<?php echo moodLink('happy'); ?>"
                           class="mood-btn happy <?php echo $mood === 'happy' ? 'active' : ''; ?>">
                            :) Радостное
                        </a>

                        <a href="<?php echo moodLink('sad'); ?>"
                           class="mood-btn sad <?php echo $mood === 'sad' ? 'active' : ''; ?>">
                            :( Грустное
                        </a>

                        <a href="<?php echo moodLink('neutral'); ?>"
                           class="mood-btn neutral <?php echo $mood === 'neutral' ? 'active' : ''; ?>">
                            :| Нейтральное
                        </a>

                        <a href="<?php echo moodLink('ironic'); ?>"
                           class="mood-btn ironic <?php echo $mood === 'ironic' ? 'active' : ''; ?>">
                            (: Ироничное
                        </a>

                        <a href="<?php echo moodLink('boring'); ?>"
                            class="mood-btn ironic <?php echo $mood === 'boring' ? 'active' : ''; ?>">
                            Душное
                        </a>

                        <span style="margin-left: auto; font-size: 0.85rem; color: #94a3b8;">
                            <?php echo $currentMood['emoji']; ?> Текущее
                        </span>
                    </div>

                    <div class="single-header">
                        <span class="single-tag"><?php echo htmlspecialchars($foundNews['tag']); ?></span>
                        <h1><?php echo htmlspecialchars($foundNews['title']); ?></h1>
                        <div class="single-meta">
                            <span><?php echo htmlspecialchars($foundNews['source']); ?></span>
                        </div>
                    </div>

                    <div class="single-content">
                        <p><?php echo nl2br(htmlspecialchars($full_text)); ?></p>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 0.9rem;">
                    <span style="font-size: 2rem; display: block; margin-bottom: 0.5rem;">
                        <?php echo $currentMood['emoji']; ?>
                    </span>
                    <span>Вы читаете новость в
                        <strong>
                            <?php echo $currentMood['name']; ?>
                        </strong> настроении
                    </span>
                </div>

                <footer class="footer">
                    <p>© 2026 NewsGrid — демонстрационный шаблон с CSS Grid</p>
                </footer>
            </div>
        </body>
    </html>
    <?php
}
?>
