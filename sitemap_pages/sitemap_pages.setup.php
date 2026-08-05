<?php
/* ====================
[BEGIN_COT_EXT]
Code=sitemap_pages
Name=Pages Sitemap (multilingual)
Category=seo
Description=XML sitemap for Pages module with multi-language support (only pages)
Version=1.1.1
Date=Aug 5, 2026
Author=webitproff
Copyright=Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
Notes=ReadMeMore https://abuyfile.com/ru/page/cotonti/plugs/sitemap-pages-xml-i18n
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=page
Recommends_plugins=i18n
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
cache_ttl=01:string::3600:Cache TTL (seconds)
freq=04:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Default change frequency
prio=07:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Default priority
perpage=10:string::50000:Max URLs per sitemap file
languages=15:string:::Languages (comma separated, e.g. en,ru,ua). Empty = all active
default_lang=18:string:::Default language (no prefix in URL)
add_default_prefix=19:radio::0:Add language prefix for default language in sitemap URLs (e.g. /ru/page)
use_pretty_urls=21:radio::0:Use pretty URLs for sitemap links (e.g. /en/sitemap-pages.xml)
pageCategoryPagination=30:radio::1:Include category pagination
page_freq=33:select:default,always,hourly,daily,weekly,monthly,yearly,never:weekly:Pages change frequency
page_prio=36:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Pages priority
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL');

/**
 * sitemap_pages.setup.php - Register data in $db_core and $db_config. Setup & Config File for the Plugin sitemap_pages
 * Filename: sitemap_pages.setup.php
 *
 * sitemap_pages plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/page/cotonti/plugs/sitemap-pages-xml-i18n
 * Support:          https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:           https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * 
 * Date: Aug 5, 2026
 * @package sitemap_pages
 * @version 1.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * @license BSD
 */
