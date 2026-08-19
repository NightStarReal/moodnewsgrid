<?php
// add_news.php — страница для добавления новости

// Получаем параметры из URL (для отображения статуса)
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';

// Устанавливаем заголовок для UTF-8
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Добавить новость — NewsGrid</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <div class="add-news-container">
            <a href="/" class="back-link">← Назад к списку новостей</a>

            <h1>Добавить новость</h1>
            <p class="subtitle">Заполните форму для добавления новой новости</p>

            <!-- Статусное сообщение -->
            <?php if ($status === 'success'): ?>
                <div class="status-message success">
                    <span class="icon">✅</span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php elseif ($status === 'error'): ?>
                <div class="status-message error">
                    <span class="icon">❌</span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form action="/add-news" method="POST" id="addNewsForm">
                <div class="form-group">
                    <label for="title">
                        Название<span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Название..."
                        required
                        autofocus
                    />
                    <span class="hint">Напишите название новости</span>
                </div>

                <div class="form-group">
                    <label for="full_text">
                        Полный текст новости <span class="required">*</span>
                    </label>
                    <textarea
                        id="full_text"
                        name="full_text"
                        class="full-text"
                        placeholder="Введите полный текст новости..."
                        required
                        rows="10"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="url">
                        Источник<span class="required">*</span>
                    </label>
                    <input
                        type="url"
                        id="url"
                        name="url"
                        placeholder="https://example.com/news/123"
                        required
                        autofocus
                    />
                    <span class="hint">Введите URL источника</span>
                </div>

                <div class="form-group">
                    <label for="tag">Категория</label>
                    <select id="tag" name="tag">
                        <option value="tech">Технологии</option>
                        <option value="world">Мир</option>
                        <option value="business">Бизнес</option>
                        <option value="health">Здоровье</option>
                        <option value="science">Наука</option>
                        <option value="sport">Спорт</option>
                        <option value="culture">Культура</option>
                    </select>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    ➕ Добавить новость
                </button>
            </form>
        </div>

        <footer class="footer">
            <p>© 2026 NewsGrid — демонстрационный шаблон с CSS Grid</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addNewsForm');
            const submitBtn = document.getElementById('submitBtn');
            const urlInput = document.getElementById('url');

            if (urlInput) {
                urlInput.focus();
            }

            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.textContent = '⏳ Отправка...';
            });

            if (urlInput) {
                urlInput.addEventListener('input', function() {
                    const url = this.value.trim();
                    if (url && !isValidUrl(url)) {
                        this.style.borderColor = '#ef4444';
                        this.setCustomValidity('Пожалуйста, введите корректный URL');
                    } else {
                        this.style.borderColor = '#22c55e';
                        this.setCustomValidity('');
                    }
                });
            }

            function isValidUrl(string) {
                try {
                    const url = new URL(string);
                    return url.protocol === 'http:' || url.protocol === 'https:';
                } catch (_) {
                    return false;
                }
            }
        });
    </script>
</body>
</html>
