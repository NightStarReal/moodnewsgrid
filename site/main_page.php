<?php
function main_page(array $newsData) {
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Новостной грид</title>
        <link rel="stylesheet" href="styles.css" />
    </head>
    <body>
        <div class="container">
            <header class="header">
                <div class="logo">
                    <h1>NewsGrid <span>• свежие новости</span></h1>
                </div>
                <div class="header-actions">
                    <button>Лента</button>
                    <button class="primary-btn">Подписаться</button>
                </div>
            </header>
            <div class="news-grid">
                <?php foreach ($newsData as $index => $news): ?>
                <?php
                    $featuredClass = ($index === 0) ? 'featured-card' : '';
                ?>
                <article class="news-card <?php echo $featuredClass; ?>">
                    <a href="?news=<?php echo $news['id']; ?>" style="text-decoration: none; color: inherit; display: contents;">
                        <div class="card-content">
                            <span class="card-tag"><?php echo htmlspecialchars($news['tag']); ?></span>
                            <h3 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p class="card-excerpt">
                                <?php
                                $excerpt = mb_strimwidth(
                                    $news['full_text'],
                                    0,
                                    100,
                                    '...',
                                    'UTF-8'
                                );
                                echo htmlspecialchars($excerpt);
                                ?>
                            </p>
                            <div class="card-meta">
                                <span class="author"><?php echo htmlspecialchars($news['source']); ?></span>
                            </div>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>

            <footer class="footer">
                <p>© 2026 NewsGrid — демонстрационный шаблон с CSS Grid</p>
            </footer>

        </div>
    </body>
</html>
<?php
}
