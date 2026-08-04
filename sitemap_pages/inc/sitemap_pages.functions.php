<?php
/**
 * Sitemap Pages — мультиязычная карта сайта для модуля Page (Cotonti)
 *
 * Плагин генерирует отдельные XML‑файлы sitemap для каждого языка, 
 * указанного в настройках, и опционально один общий индексный файл 
 * (sitemap index). Для языков, отличных от основного, в карту попадают 
 * только те категории и страницы, для которых существуют переводы 
 * (таблицы `cot_i18n_structure` и `cot_i18n_pages`).
 *
 * Все сгенерированные карты кешируются в `datas/cache/sitemap_pages/`.
 *
 * Filename:    plugins/sitemap_pages/inc/sitemap_pages.functions.php
 * sitemap_pages plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/page/cotonti/plugs/sitemap-pages-xml-i18n
 * Support:          https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:           https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * 
 * Date: Aug 3, 2026
 * @package sitemap_pages
 * @version 1.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-pages-xml-i18n-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// ========================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ========================

/**
 * Удаляет из XML все символы табуляции, переноса строки и возврата каретки
 *
 * @param  string $xml Исходный XML
 * @return string       Сжатый XML (одна строка)
 */
function sitemap_pages_compress($xml)
{
    return str_replace(["\t", "\r", "\n"], '', $xml);
}

/**
 * Преобразует UNIX‑timestamp в формат W3C (ISO 8601) для тега <lastmod>
 *
 * @param  int    $timestamp Метка времени (0 – дата не задана)
 * @return string             Строка вида '2026-08-03T12:00:00+00:00' или пустая строка
 */
function sitemap_pages_date($timestamp)
{
    return $timestamp > 0 ? date('c', $timestamp) : '';
}

/**
 * Возвращает значение для <changefreq>, если оно отлично от 'default'
 *
 * @param  string $value Значение из конфигурации плагина
 * @return string         Пустая строка или значение (always, hourly, daily, weekly, monthly, yearly, never)
 */
function sitemap_pages_freq($value)
{
    return $value === 'default' ? '' : $value;
}

/**
 * Возвращает значение для <priority>, если оно отлично от 0.5 (значение по умолчанию)
 *
 * @param  string $value Значение из конфигурации плагина
 * @return string         Пустая строка или число от 0.0 до 1.0
 */
function sitemap_pages_prio($value)
{
    return $value == '0.5' ? '' : $value;
}

// ========================
// СОХРАНЕНИЕ КАРТЫ В КЕШ
// ========================

/**
 * Сохраняет готовый XML‑файл карты сайта в папку кеша.
 *
 * Имя файла формируется как `{lang}.xml` или `{lang}.{номер_части}.xml`,
 * если общее количество URL превышает лимит на один файл.
 *
 * @param string $xml  XML‑содержимое (urlset)
 * @param string $lang Код языка (ua, en, ru, pl)
 * @param int    $d    Номер части (0 – основная карта)
 */
function sitemap_pages_save($xml, $lang, $d = 0)
{
    $dir = Cot::$cfg['cache_dir'] . '/sitemap_pages';
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    $suffix   = ($d > 0) ? ".$d" : '';
    $filename = $dir . '/' . $lang . $suffix . '.xml';
    file_put_contents($filename, sitemap_pages_compress($xml));
}

// ========================
// ФОРМИРОВАНИЕ ОДНОЙ ЗАПИСИ <url>
// ========================

/**
 * Парсит один элемент (категорию или страницу) и добавляет его в XTemplate.
 *
 * Если текущий файл достиг лимита `$perpage`, он сохраняется и начинается новый.
 *
 * @param XTemplate $t       Объект шаблона
 * @param int       &$items  Счётчик добавленных URL (передаётся по ссылке)
 * @param array     $item    Ассоциативный массив с ключами:
 *                           'name'   - тип ссылки (обычно 'page'),
 *                           'params' - параметры для cot_url(),
 *                           'date'   - timestamp последнего изменения,
 *                           'freq'   - частота изменения,
 *                           'prio'   - приоритет
 * @param int       $perpage Максимальное количество URL в одном файле
 * @param string    $lang    Код языка
 */
function sitemap_pages_parse(&$t, &$items, $item, $perpage, $lang)
{
    // Если достигнут лимит, сохраняем текущий файл и сбрасываем шаблон
    if ($items > 0 && $items % $perpage == 0) {
        $d = (int)($items / $perpage) - 1;
        $t->parse();
        sitemap_pages_save($t->text(), $lang, $d);
        $t->reset();
    }

    // Формируем абсолютный URL с учётом языка
    $url = sitemap_pages_url($lang, $item['name'], $item['params']);

    // Заполняем шаблон
    $t->assign([
        'SITEMAP_ROW_URL'   => $url,
        'SITEMAP_ROW_DATE'  => sitemap_pages_date($item['date']),
        'SITEMAP_ROW_FREQ'  => sitemap_pages_freq($item['freq']),
        'SITEMAP_ROW_PRIO'  => sitemap_pages_prio($item['prio']),
    ]);
    $t->parse('MAIN.SITEMAP_ROW');
    $items++;
}

// ========================
// РАБОТА С ЯЗЫКАМИ
// ========================

/**
 * Возвращает массив языковых кодов, для которых нужно генерировать карты.
 *
 * Источники (в порядке приоритета):
 *   1. Настройка плагина `languages` (строка через запятую).
 *   2. Массив 
 *   3. 
 *
 * @return array Список языков, например ['en','ru','pl','ua']
 */

function sitemap_pages_get_languages()
{
    $cfg = Cot::$cfg['plugin']['sitemap_pages'];
    $defaultLang = $cfg['default_lang'] ?? 'ru';

    // 1. Языки явно указаны в настройках плагина sitemap_pages
    if (!empty($cfg['languages'])) {
        $langs = array_map('trim', explode(',', $cfg['languages']));
        $langs = array_filter($langs);
        if (!in_array($defaultLang, $langs)) {
            array_unshift($langs, $defaultLang);
        }
        return array_values($langs);
    }

    // 2. Языки не указаны — берём все локали из плагина i18n
    $i18nLocales = Cot::$cfg['plugin']['i18n']['locales'] ?? '';
    if (!empty($i18nLocales)) {
        $langs = [];
        $lines = preg_split('/\r?\n/', $i18nLocales);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $code = trim($parts[0] ?? '');
            if ($code !== '') {
                $langs[$code] = true;
            }
        }
        $langs = array_keys($langs);
        if (!in_array($defaultLang, $langs)) {
            array_unshift($langs, $defaultLang);
        }
        return $langs;
    }

    // 3. Если ничего не найдено — только язык по умолчанию
    return [$defaultLang];
}


/**
 * Строит абсолютный URL страницы с учётом языкового префикса.
 *
 * Для основного языка префикс не добавляется, для остальных – добавляется.
 * Кроме того, проверяется, не содержит ли путь уже языковой префикс 
 * (это возможно из‑за работы Cotonti) – если да, дублирование предотвращается.
 *
 * @param  string       $lang   Код языка
 * @param  string       $name   Тип URL (обычно 'page')
 * @param  string|array $params Параметры для cot_url()
 * @return string               Абсолютный URL, например:
 *                              https://yourdomain.com/page.php?id=1
 *                              https://yourdomain.com/en/page.php?id=1
 */
function sitemap_pages_url($lang, $name, $params)
{
    $cfg              = Cot::$cfg['plugin']['sitemap_pages'];
    $defaultLang      = $cfg['default_lang'] ?? 'ru';
    $addDefaultPrefix = !empty($cfg['add_default_prefix']); // 1 = добавляем префикс, 0 = не добавляем

    $relative = ltrim(cot_url($name, $params), '/');

    // Получаем список всех возможных префиксов языков
    $allLangs      = sitemap_pages_get_languages();
    $prefixPattern = '#^(' . implode('|', array_map('preg_quote', $allLangs)) . ')/#';

    // Убираем любой существующий языковой префикс из URL
    $relative = preg_replace($prefixPattern, '', $relative);

    // Добавляем префикс по правилам
    if ($lang === $defaultLang) {
        // Для языка по умолчанию — добавляем префикс только если add_default_prefix = 1
        if ($addDefaultPrefix) {
            $relative = $defaultLang . '/' . $relative;
        }
        // Если add_default_prefix = 0 — оставляем без префикса
    } else {
        // Для дополнительных языков — всегда добавляем префикс
        $relative = $lang . '/' . $relative;
    }

    return rtrim(COT_ABSOLUTE_URL, '/') . '/' . $relative;
}

/**
 * Формирует URL самой карты сайта (для использования в индексном файле).
 *
 * Если включены красивые URL (`use_pretty_urls`), то адрес будет вида
 *   - `https://yourdomain.com/sitemap-pages.xml` (основной язык)
 *   - `https://yourdomain.com/en/sitemap-pages.xml` (дополнительный язык)
 * Иначе – прямая ссылка с `index.php?r=sitemap_pages`.
 *
 * @param  string $lang Код языка
 * @return string        Полный URL карты сайта для данного языка
 */
function sitemap_pages_index_url($lang)
{
    $cfg         = Cot::$cfg['plugin']['sitemap_pages'];
    $defaultLang = $cfg['default_lang'] ?? 'ua'; // смотри строку выше это никак не связано с $cfg['defaultlang'] из datas/config.php
    $usePretty   = !empty($cfg['use_pretty_urls']);

    if ($usePretty) {
        $suffix = ($lang === $defaultLang)
            ? 'sitemap-pages.xml'
            : $lang . '/sitemap-pages.xml';
        return rtrim(COT_ABSOLUTE_URL, '/') . '/' . $suffix;
    } else {
        $query = ($lang === $defaultLang)
            ? 'r=sitemap_pages'
            : "r=sitemap_pages&l=$lang";
        //return COT_ABSOLUTE_URL . 'index.php?' . $query;
		return COT_ABSOLUTE_URL . 'index.php?' . str_replace('&', '&amp;', $query);
    }
}

// ========================
// ГЕНЕРАЦИЯ КАРТЫ ДЛЯ ОДНОГО ЯЗЫКА
// ========================

/**
 * Создаёт (или обновляет) кешированные XML‑файлы карты сайта для одного языка.
 *
 * Алгоритм:
 *  1. Если язык не является основным, загружаются списки переведённых категорий
 *     и страниц из таблиц i18n.
 *  2. Для каждой категории, доступной пользователю (и, если требуется, имеющей перевод),
 *     добавляются URL самой категории и страницы пагинации (если включены в настройках).
 *  3. Добавляются все активные страницы. Для неосновного языка – только те, у которых
 *     есть перевод.
 *  4. Полученный XML сохраняется в файл(ы) в `datas/cache/sitemap_pages/`.
 *  5. Вспомогательный файл `{lang}.count` запоминает общее количество URL для данного языка.
 *
 * @param string $lang        Код языка
 * @param int    $perpage     Максимальное число URL в одном файле
 * @param array  $cfgPlug     Настройки плагина sitemap_pages
 * @param string $defaultLang Основной язык (без префикса)
 */
function sitemap_pages_generate_language($lang, $perpage, $cfgPlug, $defaultLang)
{
    global $cacheDir, $db_x;   // определена в sitemap_pages.ajax.php

    $t     = new XTemplate(cot_tplfile('sitemap_pages', 'plug'));
    $items = 0;

    // ---------- Загрузка переводов (только для неосновных языков) ----------
    $translatedCats  = null;   // [code => true] – категории, имеющие перевод
    $translatedItems = null;   // [id   => true] – страницы, имеющие перевод



    if ($lang !== $defaultLang && cot_plugin_active('i18n')) {
        $i18n_structure = $db_x . 'i18n_structure';
        $i18n_pages     = $db_x . 'i18n_pages';

        $tableExists = Cot::$db->query("SHOW TABLES LIKE '$i18n_structure'")->rowCount() > 0;

        if ($tableExists) {
            // Категории с переводом
            $translatedCats = [];
            $res = Cot::$db->query(
                "SELECT istructure_code FROM $i18n_structure WHERE istructure_locale = ?",
                [$lang]
            );
            while ($row = $res->fetch()) {
                $translatedCats[$row['istructure_code']] = true;
            }
            $res->closeCursor();

            // Страницы с переводом
            $translatedItems = [];
            $res = Cot::$db->query(
                "SELECT ipage_id FROM $i18n_pages WHERE ipage_locale = ?",
                [$lang]
            );
            while ($row = $res->fetch()) {
                $translatedItems[(int)$row['ipage_id']] = true;
            }
            $res->closeCursor();
        }
    }

    // ---------- Категории ----------
    $auth_cache    = [];
    $category_list = Cot::$structure['page'] ?? [];

    foreach ($category_list as $c => $cat) {
        if (!is_array($cat) || $c === 'system') {
            continue;
        }
        // Если есть список переведённых категорий и текущая категория в нём отсутствует – пропускаем
/*         if ($translatedCats !== null && !isset($translatedCats[$c])) {
            continue;
        } */

        // Проверяем право на чтение
        $auth_cache[$c] = cot_auth('page', $c, 'R');
        if (empty($auth_cache[$c])) {
            continue;
        }

        // Определяем количество страниц пагинации для этой категории
        $catConfig        = Cot::$cfg['page']['cat_' . $c] ?? [];
        $defaultCatConfig = Cot::$cfg['page']['cat___default'] ?? [];
        $maxrowsperpage   = (int) max(1,
            $catConfig['maxrowsperpage'] ?? $defaultCatConfig['maxrowsperpage'] ?? 0
        );

        $catCount      = (int)($cat['count'] ?? 0);
        $usePagination = $cfgPlug['pageCategoryPagination'] ?? false;
        $subs          = ($usePagination && $maxrowsperpage > 0)
            ? (int)floor($catCount / $maxrowsperpage) + 1
            : 1;

        $easypagenav = Cot::$cfg['easypagenav'] ?? false;

        for ($pg = 1; $pg <= $subs; $pg++) {
            $offset = $easypagenav ? $pg : ($pg - 1) * $maxrowsperpage;
            $params = $pg > 1 ? "c=$c&d=$offset" : "c=$c";

            sitemap_pages_parse($t, $items, [
                'name'   => 'page',
                'params' => $params,
                'date'   => '',
                'freq'   => $cfgPlug['page_freq'] ?? 'weekly',
                'prio'   => $cfgPlug['page_prio'] ?? '0.5',
            ], $perpage, $lang);
        }
    }

    // ---------- Страницы ----------
    $where   = [];
    $where[] = 'p.page_state = 0';
    $where[] = 'p.page_begin <= ' . Cot::$sys['now'];
    $where[] = '(p.page_expire = 0 OR p.page_expire > ' . Cot::$sys['now'] . ')';

    // Если язык не основной – ограничиваем страницы только переведёнными
    if ($translatedItems !== null) {
        if (empty($translatedItems)) {
            // Нет ни одной переведённой страницы – сохраняем пустую карту
            $t->parse();
            sitemap_pages_save($t->text(), $lang, 0);
            file_put_contents($cacheDir . '/' . $lang . '.count', 0);
            return;
        }
        $ids     = implode(',', array_keys($translatedItems));
        $where[] = "p.page_id IN ($ids)";
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);
    $res = Cot::$db->query(
        "SELECT p.page_id, p.page_alias, p.page_cat, p.page_updated
         FROM " . Cot::$db->pages . " AS p
         $whereSql
         ORDER BY p.page_cat, p.page_id"
    );

    while ($row = $res->fetch()) {
        $cat = $row['page_cat'] ?? '';
        if (empty($cat) || empty($auth_cache[$cat])) {
            continue;
        }

        $params = ['c' => $cat];
        if (!empty($row['page_alias'])) {
            $params['al'] = $row['page_alias'];
        } else {
            $params['id'] = $row['page_id'];
        }

        sitemap_pages_parse($t, $items, [
            'name'   => 'page',
            'params' => $params,
            'date'   => $row['page_updated'] ?? 0,
            'freq'   => $cfgPlug['page_freq'] ?? 'weekly',
            'prio'   => $cfgPlug['page_prio'] ?? '0.5'
        ], $perpage, $lang);

        unset($row);
    }
    $res->closeCursor();

    // Сохраняем последнюю (или единственную) часть карты и записываем счётчик
    $d = $items > 0 ? (int)ceil($items / $perpage) - 1 : 0;
    $t->parse();
    sitemap_pages_save($t->text(), $lang, $d);
    file_put_contents($cacheDir . '/' . $lang . '.count', $items);
}