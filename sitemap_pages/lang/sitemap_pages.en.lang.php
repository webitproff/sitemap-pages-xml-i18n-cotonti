<?php
/**
 * English Language File for Sitemap Pages Plugin
 *
 * All text strings used by the plugin in the Cotonti interface:
 * - plugin name and description (info_name, info_desc)
 * - admin panel settings (cfg_…)
 * - field hints (cfg_…_hint)
 * - dropdown list values (cfg_…_params)
 *
 * Filename: plugins/sitemap_pages/lang/sitemap_pages.en.lang.php
 *
 * Plugin URI:  https://abuyfile.com/ru/page/cotonti/plugs/sitemap-pages-xml-i18n
 * Source:      https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 *
 * Date: Aug 3, 2026
 * @package sitemap_pages
 * @version 1.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// ========================
// PLUGIN INFO (ADMIN PANEL)
// ========================
$L['info_name']  = 'Sitemap Pages (multilingual pages sitemap)';
$L['info_desc']  = 'Generates XML sitemap only for Page module with multi-language support.';
$L['info_notes'] = 'After installation, add rules to .htaccess and specify Sitemap in robots.txt (see documentation for details).';

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

// --- Maximum URLs per file ---
$L['cfg_perpage']            = 'Max URLs per sitemap file';
$L['cfg_perpage_hint']       = 'If there are more pages, the sitemap is split into multiple files.';

// --- Languages ---
$L['cfg_languages']          = 'Languages (comma separated)';
$L['cfg_languages_hint']     = 'E.g.: en,ru,pl,ua. Leave empty to use all active languages from site configuration.';
$L['cfg_default_lang']       = 'Default language (no prefix)';
$L['cfg_default_lang_hint']  = 'For this language the sitemap will be available at /sitemap-pages.xml without a language prefix.';

// --- Pretty URLs ---
$L['cfg_use_pretty_urls']    = 'Use pretty URLs for sitemaps';
$L['cfg_use_pretty_urls_hint'] = 'If enabled, sitemap addresses will look like /sitemap-pages.xml and /en/sitemap-pages.xml. Otherwise, direct links with index.php will be used.';

// --- Page settings ---
$L['cfg_pageCategoryPagination']     = 'Include category pagination';
$L['cfg_pageCategoryPagination_hint'] = 'Add category pages with parameters like ?d=2, ?d=3, etc. to the sitemap.';
$L['cfg_page_freq']                  = 'Page change frequency';
$L['cfg_page_freq_params']           = $sitemap_freqs;
$L['cfg_page_prio']                  = 'Page priority';