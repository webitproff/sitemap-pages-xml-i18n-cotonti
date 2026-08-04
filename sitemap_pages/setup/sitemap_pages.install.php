<?php
/**
 * Sitemap Pages – установка: добавление ссылки на карту сайта в robots.txt
 *
 * Этот файл выполняется при установке плагина через админку Cotonti.
 * Он автоматически находит текущий файл robots.txt, удаляет из него все
 * старые упоминания sitemap-pages.xml (если они были) и добавляет одну
 * актуальную строку `Sitemap: https://вашсайт/sitemap-pages.xml`.
 *
 * Filename:    plugins/sitemap_pages/setup/sitemap_pages.install.php
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

// Путь к robots.txt относительно корня сайта
$robotsFile = './robots.txt';

// Проверяем, что файл существует и доступен для записи
if (file_exists($robotsFile) && is_writable($robotsFile)) {
    // Читаем текущее содержимое robots.txt построчно
    $lines = file($robotsFile);
    $newLines = [];

    // Формируем URL карты сайта на основе главного URL сайта
    $sitemapUrl = Cot::$cfg['mainurl'] . '/sitemap-pages.xml';

    // Удаляем все старые строки, содержащие sitemap-pages.xml
    // (это позволяет избежать дублирования при повторных установках)
    foreach ($lines as $line) {
        if (stripos($line, 'sitemap-pages.xml') === false) {
            $newLines[] = $line;
        }
    }

    // Добавляем новую (или обновлённую) строку Sitemap
    $newLines[] = "\nSitemap: " . $sitemapUrl . "\n";

    // Записываем обновлённое содержимое обратно в robots.txt
    file_put_contents($robotsFile, implode('', $newLines));
}