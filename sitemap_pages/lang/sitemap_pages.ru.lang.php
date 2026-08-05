<?php
/**
 * Russian Language File for Sitemap Pages Plugin
 *
 * Все текстовые строки, используемые плагином в интерфейсе Cotonti:
 * - название и описание плагина (info_name, info_desc)
 * - настройки в админ-панели (cfg_…)
 * - подсказки к полям (cfg_…_hint)
 * - значения для выпадающих списков (cfg_…_params)
 *
 * Filename: plugins/sitemap_pages/lang/sitemap_pages.ru.lang.php
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
// ИНФОРМАЦИЯ О ПЛАГИНЕ (АДМИНКА)
// ========================
$L['info_name']  = 'Sitemap Pages i18n';
$L['info_desc']  = 'Генерирует XML-карту сайта только для модуля Page с поддержкой плагина i18n - разных языков.';
$L['info_notes'] = 'После установки добавить/проверить правила в .htaccess и robots.txt. <strong>Подробности <a target="_blank" href="https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti/blob/main/README.md"> в документации README.md</a></strong>.';

// ========================
// ЗАГОЛОВКИ И ОПИСАНИЯ (где те же значения, вытягиваются другими ключами)
// ========================
$L['sitemap_pages_title']       = $L['info_name'];
$L['sitemap_pages_desc']        = $L['info_desc'];

// ========================
// ЧАСТОТЫ ОБНОВЛЕНИЯ (ОБЩИЕ)
// ========================
$sitemap_freqs = [
    'default' => 'По умолчанию',
    'always'  => 'Всегда',
    'hourly'  => 'Ежечасно',
    'daily'   => 'Ежедневно',
    'weekly'  => 'Еженедельно',
    'monthly' => 'Ежемесячно',
    'yearly'  => 'Ежегодно',
    'never'   => 'Никогда',
];

// ========================
// НАСТРОЙКИ ПЛАГИНА (АДМИНКА)
// ========================

// --- Кеш и частота по умолчанию ---
$L['cfg_cache_ttl']          = 'Время жизни кеша (секунд)';
$L['cfg_cache_ttl_hint']     = 'Через сколько секунд карта будет пересоздана при обращении.';
$L['cfg_freq']               = 'Частота изменения по умолчанию';
$L['cfg_freq_params']        = $sitemap_freqs;
$L['cfg_prio']               = 'Приоритет по умолчанию';

// --- Максимальное количество ссылок в одном файле ---
$L['cfg_perpage']            = 'Макс. ссылок на часть карты';
$L['cfg_perpage_hint']       = 'Если страниц больше, карта разбивается на несколько файлов.';

// --- Языки ---
$L['cfg_languages']          = 'Языки (через запятую)';
$L['cfg_languages_hint']     = 'Например: en,ru,pl,ua. Оставьте пустым – используются все активные языки из конфигурации плагина i18n.';
$L['cfg_default_lang']       = 'Язык по умолчанию (без префикса)';
$L['cfg_default_lang_hint']  = 'Для этого языка карта будет доступна по адресу /sitemap-pages.xml без указания языкового префикса.';

// --- Красивые URL ---
$L['cfg_use_pretty_urls']    = 'Использовать красивые URL для карт сайта';
$L['cfg_use_pretty_urls_hint'] = 'Если включено, адреса карт будут вида /sitemap-pages.xml и /en/sitemap-pages.xml. Иначе – прямые ссылки с index.php.';

// --- Настройки для страниц ---
$L['cfg_pageCategoryPagination']     = 'Включить пагинацию категорий';
$L['cfg_pageCategoryPagination_hint'] = 'Добавлять в карту страницы категорий с параметром ?d=2, ?d=3 и т.д.';
$L['cfg_page_freq']                  = 'Частота изменения страниц';
$L['cfg_page_freq_params']           = $sitemap_freqs;
$L['cfg_page_prio']                  = 'Приоритет страниц';
