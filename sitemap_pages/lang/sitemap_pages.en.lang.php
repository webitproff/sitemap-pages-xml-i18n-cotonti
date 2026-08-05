<?php
/**
 * English Language File for Sitemap Pages Plugin
 *
 * All text strings used by the plugin in the Cotonti interface:
 * - plugin name and description (info_name, info_desc)
 * - admin settings (cfg_…)
 * - field hints (cfg_…_hint)
 * - dropdown values (cfg_…_params)
 *
 * Filename: plugins/sitemap_pages/lang/sitemap_pages.en.lang.php
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
// PLUGIN INFO (ADMIN PANEL)
// ========================
$L['info_name']  = 'Sitemap Pages i18n';
$L['info_desc']  = 'Generates an XML sitemap only for the Page module with support for the i18n plugin – multiple languages.';
$L['info_notes'] = 'After installation, add/check the rules in .htaccess and robots.txt. <strong>Details <a target="_blank" href="https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti/blob/main/README.md">in the README.md documentation</a></strong>.';

// ========================
// TITLES AND DESCRIPTIONS (same values, pulled by other keys)
// ========================
$L['sitemap_pages_title']       = $L['info_name'];
$L['sitemap_pages_desc']        = $L['info_desc'];

// ========================
// CHANGE FREQUENCIES (COMMON)
// ========================
$sitemap_freqs = [
    'default' => 'Default',
    'always'  => 'Always',
    'hourly'  => 'Hourly',
    'daily'   => 'Daily',
    'weekly'  => 'Weekly',
    'monthly' => 'Monthly',
    'yearly'  => 'Yearly',
    'never'   => 'Never',
];

// ========================
// PLUGIN SETTINGS (ADMIN PANEL)
// ========================

// --- Cache and default frequency ---
$L['cfg_cache_ttl']          = 'Cache lifetime (seconds)';
$L['cfg_cache_ttl_hint']     = 'After how many seconds the sitemap will be regenerated upon request.';
$L['cfg_freq']               = 'Default change frequency';
$L['cfg_freq_params']        = $sitemap_freqs;
$L['cfg_prio']               = 'Default priority';

// --- Maximum number of links per file ---
$L['cfg_perpage']            = 'Max items per sitemap page';
$L['cfg_perpage_hint']       = 'If there are more pages, the sitemap is split into multiple files.';

// --- Languages ---
$L['cfg_languages']          = 'Languages (comma separated)';
$L['cfg_languages_hint']     = 'Example: en,ru,pl,ua. Leave empty to use all active languages from the i18n plugin configuration.';
$L['cfg_default_lang']       = 'Default language (without prefix)';
$L['cfg_default_lang_hint']  = 'For this language, the sitemap will be available at /sitemap-pages.xml without a language prefix.';

// --- Clean URLs ---
$L['cfg_use_pretty_urls']    = 'Use clean URLs for the sitemap';
$L['cfg_use_pretty_urls_hint'] = 'If enabled, sitemap addresses will look like /sitemap-pages.xml and /en/sitemap-pages.xml. Otherwise, direct links with index.php are used.';

// --- Page settings ---
$L['cfg_pageCategoryPagination']     = 'Include category pagination';
$L['cfg_pageCategoryPagination_hint'] = 'Add category pages with parameters ?d=2, ?d=3, etc. to the sitemap.';
$L['cfg_page_freq']                  = 'Page change frequency';
$L['cfg_page_freq_params']           = $sitemap_freqs;
$L['cfg_page_prio']                  = 'Page priority';
