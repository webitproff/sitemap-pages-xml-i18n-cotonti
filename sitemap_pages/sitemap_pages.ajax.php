<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */
/**
 * Sitemap Pages — мультиязычная карта сайта для модуля Page (Cotonti)
 *
 * Этот файл является AJAX-обработчиком плагина. Именно через него
 * выполняются все запросы к картам сайта. В зависимости от переданных
 * параметров он:
 *   - генерирует индексный файл (sitemapindex), содержащий ссылки на
 *     карты всех активных языков;
 *   - генерирует (или берёт из кеша) карту для конкретного языка;
 *   - отдаёт клиенту готовый XML-ответ.
 *
 * Логика кеширования: если время жизни кеша не истекло, файлы карт
 * читаются напрямую из папки `datas/cache/sitemap_pages/`. Иначе
 * карты пересоздаются при первом обращении.
 *
 * Вся работа с URL, языками и данными модуля Page вынесена в файл
 * `inc/sitemap_pages.functions.php`.
 *
 * Filename:    plugins/sitemap_pages/sitemap_pages.ajax.php
 * Plugin URI:  https://abuyfile.com/ru/page/cotonti/plugs/sitemap-pages-xml-i18n
 * Support:     https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:      https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 *
 * Date:       Aug 3, 2026
 * @package    sitemap_pages
 * @version    1.0.0
 * @author     webitproff
 * @copyright  Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * @license    BSD
 */

defined('COT_CODE') or die('Wrong URL');

// ========================
// ИНИЦИАЛИЗАЦИЯ И ЗАЩИТА
// ========================

// Очищаем буфер вывода, чтобы исключить случайные пробелы или BOM
if (ob_get_level()) {
    ob_clean();
}
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

// Подключаем функции плагина и модуль Page
require_once cot_incfile('sitemap_pages', 'plug');
require_once cot_incfile('page', 'module');

// Снимаем ограничения на время выполнения и память
set_time_limit(0);
@ini_set('memory_limit', '256M');

// Устанавливаем правильный Content-Type для XML
header('Content-Type: application/xml; charset=utf-8');

// ========================
// ПАРАМЕТРЫ И КОНФИГУРАЦИЯ
// ========================

// Читаем GET-параметры
$a = cot_import('a', 'G', 'ALP') ?? '';   // 'index' – запрос индексного файла
$l = cot_import('l', 'G', 'ALP') ?? '';   // код языка (en, ru, pl, ua)
$d = cot_import('d', 'G', 'INT') ?? 0;    // номер части (если карта разбита на несколько файлов)

// Настройки плагина
$cfgPlug     = Cot::$cfg['plugin']['sitemap_pages'] ?? [];
$perpage     = (int)($cfgPlug['perpage'] ?? 50000);   // макс. URL в одном файле
$cache_ttl   = (int)($cfgPlug['cache_ttl'] ?? 3600);  // время жизни кеша в секундах
$defaultLang = $cfgPlug['default_lang'] ?? 'ua';       // язык по умолчанию

// Определяем запрошенный язык (если не передан – берём основной)
$reqLang = !empty($l) ? $l : $defaultLang;

// Путь к папке с кешированными картами
$cacheDir = rtrim(Cot::$cfg['cache_dir'] ?? 'datas/cache', '/') . '/sitemap_pages';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// ========================
// ГЕНЕРАЦИЯ ИНДЕКСНОГО ФАЙЛА
// ========================
if ($a === 'index') {
    $indexFile = $cacheDir . '/sitemap_pages_index.xml';
    $needRegen = true;

    // Проверяем, не просрочен ли кеш индексного файла
    if (file_exists($indexFile) && (Cot::$sys['now'] - filemtime($indexFile)) < $cache_ttl) {
        $needRegen = false;
    }

    if ($needRegen) {
        // Получаем список всех активных языков
        $languages = sitemap_pages_get_languages();

        // Генерируем (или обновляем) карту для каждого языка
        foreach ($languages as $lang) {
            sitemap_pages_generate_language($lang, $perpage, $cfgPlug, $defaultLang);
        }

        // Строим индексный XML
        $t = new XTemplate(cot_tplfile('sitemap_pages.index', 'plug'));
        foreach ($languages as $lang) {
            $t->assign([
                'SITEMAP_ROW_URL'  => sitemap_pages_index_url($lang),
                'SITEMAP_ROW_DATE' => sitemap_pages_date(time())
            ]);
            $t->parse('MAIN.SITEMAP_ROW');
        }
        $t->parse('MAIN');

        // Удаляем возможные BOM и лишние XML-заголовки из шаблона,
        // вставляем свой правильный заголовок
        $indexXml = preg_replace('/^[\s\xEF\xBB\xBF]+/', '', $t->text());
        $indexXml = preg_replace('/^<\?xml.*?\?>\s*/', '', $indexXml);
        $indexXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $indexXml;

        // Сохраняем индекс в кеш
        file_put_contents($indexFile, $indexXml);
    }

    // Отдаём готовый индекс
    echo file_get_contents($indexFile);
    ob_end_flush();
    exit;
}

// ========================
// ГЕНЕРАЦИЯ / ВЫВОД КАРТЫ КОНКРЕТНОГО ЯЗЫКА
// ========================

// Имя файла-счётчика для этого языка (содержит общее количество URL)
$countFile = $cacheDir . '/' . $reqLang . '.count';
$needRegen = true;
$items     = 0;

// Проверяем, нужно ли перегенерировать карту (по времени жизни счётчика)
if (file_exists($countFile) && filesize($countFile) > 0) {
    $mtime = filemtime($countFile);
    if ($mtime !== false && (Cot::$sys['now'] - $mtime) < $cache_ttl) {
        $needRegen = false;
        $items = (int)file_get_contents($countFile);
    }
}

// Если кеш просрочен – генерируем карту заново
if ($needRegen) {
    sitemap_pages_generate_language($reqLang, $perpage, $cfgPlug, $defaultLang);
    // После генерации снова читаем счётчик (функция обновила файл .count)
    if (file_exists($countFile)) {
        $items = (int)file_get_contents($countFile);
    } else {
        $items = 0;
    }
}

// Определяем, какой файл карты отдать (основной или часть с номером $d)
$suffix    = ($d > 0) ? '.' . $d : '';
$cacheFile = $cacheDir . '/' . $reqLang . $suffix . '.xml';

if (file_exists($cacheFile)) {
    $xml = file_get_contents($cacheFile);
    // Очистка от BOM и дублирующихся XML-заголовков (они не нужны, т.к. мы добавим свой)
    $xml = preg_replace('/^[\s\xEF\xBB\xBF]+/', '', $xml);
    $xml = preg_replace('/^<\?xml.*?\?>\s*/', '', $xml);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
} else {
    // Если файла нет (например, для языка без страниц) – отдаём пустой urlset
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
       . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
}

// Завершаем вывод
ob_end_flush();
exit;