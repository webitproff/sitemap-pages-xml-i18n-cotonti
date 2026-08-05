<?php
/**
 * Ukrainian Language File for Sitemap Pages Plugin
 *
 * Усі текстові рядки, що використовуються плагіном в інтерфейсі Cotonti:
 * - назва та опис плагіна (info_name, info_desc)
 * - налаштування в адмін-панелі (cfg_…)
 * - підказки до полів (cfg_…_hint)
 * - значення для випадаючих списків (cfg_…_params)
 *
 * Filename: plugins/sitemap_pages/lang/sitemap_pages.ua.lang.php
 *
 * Plugin URI:  https://abuyfile.com/ru/market/cotonti/plugs/sitemap-pages-xml-i18n
 * Support:     https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:      https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 *
 * Date: Aug 5, 2026
 * @package sitemap_pages
 * @version 1.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// ========================
// ІНФОРМАЦІЯ ПРО ПЛАГІН (АДМІНКА)
// ========================
$L['info_name']  = 'Sitemap Pages i18n';
$L['info_desc']  = 'Генерує XML-карту сайту тільки для модуля Page з підтримкою плагіна i18n – різних мов.';
$L['info_notes'] = 'Після встановлення додайте/перевірте правила в .htaccess та robots.txt. <strong>Подробиці <a target="_blank" href="https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti/blob/main/README.md"> у документації README.md</a></strong>.';

// ========================
// ЗАГОЛОВКИ ТА ОПИСИ (ті самі значення, підтягуються іншими ключами)
// ========================
$L['sitemap_pages_title']       = $L['info_name'];
$L['sitemap_pages_desc']        = $L['info_desc'];

// ========================
// ЧАСТОТИ ОНОВЛЕННЯ (ЗАГАЛЬНІ)
// ========================
$sitemap_freqs = [
    'default' => 'За замовчуванням',
    'always'  => 'Завжди',
    'hourly'  => 'Щогодини',
    'daily'   => 'Щодня',
    'weekly'  => 'Щотижня',
    'monthly' => 'Щомісяця',
    'yearly'  => 'Щороку',
    'never'   => 'Ніколи',
];

// ========================
// НАЛАШТУВАННЯ ПЛАГІНА (АДМІНКА)
// ========================

// --- Кеш і частота за замовчуванням ---
$L['cfg_cache_ttl']          = 'Час життя кешу (секунд)';
$L['cfg_cache_ttl_hint']     = 'Через скільки секунд карта буде перезгенерована при зверненні.';
$L['cfg_freq']               = 'Частота змін за замовчуванням';
$L['cfg_freq_params']        = $sitemap_freqs;
$L['cfg_prio']               = 'Пріоритет за замовчуванням';

// --- Максимальна кількість посилань в одному файлі ---
$L['cfg_perpage']            = 'Макс. посилань на частину карти';
$L['cfg_perpage_hint']       = 'Якщо сторінок більше, карта розбивається на декілька файлів.';

// --- Мови ---
$L['cfg_languages']          = 'Мови (через кому)';
$L['cfg_languages_hint']     = 'Наприклад: en,ru,pl,ua. Залиште порожнім – використовуються всі активні мови з конфігурації плагіна i18n.';
$L['cfg_default_lang']       = 'Мова за замовчуванням (без префікса)';
$L['cfg_default_lang_hint']  = 'Для цієї мови карта буде доступна за адресою /sitemap-pages.xml без вказівки мовного префікса.';

// --- ЧПУ (зрозумілі URL) ---
$L['cfg_use_pretty_urls']    = 'Використовувати зрозумілі URL для карт сайту';
$L['cfg_use_pretty_urls_hint'] = 'Якщо увімкнено, адреси карт матимуть вигляд /sitemap-pages.xml та /en/sitemap-pages.xml. Інакше – прямі посилання з index.php.';

// --- Налаштування для сторінок ---
$L['cfg_pageCategoryPagination']     = 'Увімкнути пагінацію категорій';
$L['cfg_pageCategoryPagination_hint'] = 'Додавати до карти сторінки категорій з параметром ?d=2, ?d=3 тощо.';
$L['cfg_page_freq']                  = 'Частота змін сторінок';
$L['cfg_page_freq_params']           = $sitemap_freqs;
$L['cfg_page_prio']                  = 'Пріоритет сторінок';