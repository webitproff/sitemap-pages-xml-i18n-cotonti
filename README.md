
# Sitemap Pages – multilingual sitemap for the Page module (Cotonti)

The **Sitemap Pages** plugin generates individual XML sitemap files for each language specified in the settings and, optionally, a single index file (sitemap index). For languages other than the primary one, only categories and pages that have translations in the standard `i18n` plugin (tables `cot_i18n_structure` and `cot_i18n_pages`) are included in the map.

<img width="1536" height="1024" alt="Sitemap Pages – мультиязычная карта сайта для модуля Page (Cotonti)" src="https://github.com/user-attachments/assets/739353a8-5e50-4c94-8837-18ce5927c297" />



---

## Table of Contents

1. [System Requirements and Dependencies](#system-requirements-en-md)
2. [Plugin Structure](#plugin-structure-en-md)
3. [Installation](#installation-en-md)
4. [Admin Settings](#admin-settings-en-md)
5. [`.htaccess` Configuration](#htaccess-setup-en-md)
6. [`robots.txt` Configuration](#robots-setup-en-md)
7. [Browser Check](#browser-check-en-md)
8. [How to Submit to Google Search Console](#google-search-console-en-md)
9. [Caching and Cache Clearing](#caching-en-md)
10. [Troubleshooting](#troubleshooting-en-md)
11. [Additional Notes](#additional-notes-en-md)
12. [Which Is Better: One Huge File or Separate Maps by Module and Language?](#comparison-main-en-md)
    - [Advantages of Separate Maps](#advantages-en-md)
    - [Disadvantages of Separate Maps](#disadvantages-en-md)
    - [What Would Have Happened with One Big File](#old-monolith-en-md)
    - [Conclusion](#conclusion-en-md)
13. [Notes on Sitemap Pages Plugin Setup and Operation](#note-setup-en-md)
    - [“Languages” Setting (languages) – Do You Need to Include the Default Language?](#language-setting-en-md)
    - [How Settings Affect the Index File](#how-settings-affect-index-en-md)
    - [Why the File at `?r=sitemap_pages&a=index` Is Needed](#index-file-purpose-en-md)
      - [What the Index Must Contain](#index-must-have-en-md)
      - [What Must Not Be Present](#index-must-not-have-en-md)
      - [Example of a Correct Index](#index-example-en-md)
    - [Adding a Rewrite Rule for the Index File](#rewrite-index-rule-en-md)

---

## <a id="system-requirements-en-md"></a>System Requirements and Dependencies

- **Cotonti** version 1.0+ (tested with PHP 8.5+ and MySQL 8.4).
- The **`page` module** – must be installed and active.
- The **`i18n` plugin** (Content Internationalization) – must be installed and active. The plugin uses its tables (`cot_i18n_structure` and `cot_i18n_pages`) to determine which categories and pages have translations for a specific language.
- mod_rewrite in Apache (or an equivalent in Nginx) must be configured for clean URLs to work.

---

## <a id="plugin-structure-en-md"></a>Plugin Structure

The plugin is located in the folder `plugins/sitemap_pages/` and contains the following files:

```
sitemap_pages/
├── sitemap_pages.setup.php           # Header, metadata and plugin settings
├── sitemap_pages.ajax.php            # Main request handler (AJAX)
├── inc/
│   └── sitemap_pages.functions.php   # Functions: map generation, URL and language handling
├── lang/
│   ├── sitemap_pages.ru.lang.php     # Russian setting labels
│   └── sitemap_pages.en.lang.php     # English setting labels
├── tpl/
│   ├── sitemap_pages.tpl             # Template for a regular urlset
│   └── sitemap_pages.index.tpl       # Template for the index file (sitemapindex)
└── setup/
    └── sitemap_pages.install.php     # Automatically adds a sitemap link to robots.txt on install
```

The templates already contain the correct XML markup and do not require manual editing.

---

## <a id="installation-en-md"></a>Installation

1. Download the `sitemap_pages` folder and copy it to the `plugins/` directory of your Cotonti site.
2. Go to the admin panel: **Extensions → Plugins**.
3. Find `Pages Sitemap (multilingual)` in the list and click **Install**.
4. During installation, the plugin automatically adds the line `Sitemap: https://yoursite/sitemap-pages.xml` to `robots.txt` (if the file is writable). Any previous mentions of `sitemap-pages.xml` are removed to avoid duplicates.

After installation, the plugin is ready for configuration.

---

## <a id="admin-settings-en-md"></a>Admin Settings

Go to **Extensions → Plugins → Pages Sitemap (multilingual) → Configuration**.

| Parameter | Default Value | Description |
|---|---|---|
| **Languages (languages)** | *empty* | A comma-separated list of languages, e.g., `en,ru,ua`. If left empty, all active languages from the Cotonti configuration (`Cot::$cfg['plugin']['i18n']['locales']`) are used. |
| **Default language (default_lang)** | `ua` | The language for which no prefix is added to the URL (e.g., `/page.php?id=1` instead of `/ua/page.php?id=1`). |
| **Use clean URLs (use_pretty_urls)** | `0` (disabled) | If enabled (`1`), map URLs will look like `/sitemap-pages.xml` and `/en/sitemap-pages.xml`. If disabled, direct links with `index.php?r=sitemap_pages` are used. It is recommended to enable after setting up `.htaccess`. |
| **Include category pagination (pageCategoryPagination)** | `1` (enabled) | If a category has multiple pages of entries, URLs with parameters `?d=2`, `?d=3`, etc. will be added to the map. |
| **Page change frequency (page_freq)** | `weekly` | The value of the `<changefreq>` tag for pages. |
| **Page priority (page_prio)** | `0.5` | The value of the `<priority>` tag for pages. |
| **Max URLs per map part (perpage)** | `50000` | If the total number of URLs exceeds this number, the map will be split into multiple files (usually not necessary). |
| **Cache time to live (cache_ttl)** | `3600` | The period (in seconds) after which the cached maps are considered stale and will be regenerated on the next request. |

After changing settings, click **Save**.

---

## <a id="htaccess-setup-en-md"></a>`.htaccess` Configuration

To enable clean URLs (if you have turned on `use_pretty_urls`), add the following lines to the root `.htaccess` **right after** the rule `RewriteRule ^sitemap\.xml$ ...`:

```apache
RewriteRule ^sitemap-pages\.xml$ index.php?r=sitemap_pages [QSA,L]
RewriteRule ^(en|ru|pl|ua)/sitemap-pages\.xml$ index.php?r=sitemap_pages&l=$1 [QSA,L]
```

> **Important:** the `[QSA]` (Query String Append) flag is required, because Cotonti’s language rule may already add a `?l=en` parameter, and without `QSA` it will be lost.

Make sure these rules are placed **before** the line `# All the rest goes through standard rewrite gateway`.

---

## <a id="robots-setup-en-md"></a>`robots.txt` Configuration

During installation, the plugin automatically adds the following line:

```
Sitemap: https://yoursite/sitemap-pages.xml
```

If the automatic addition fails (for example, `robots.txt` is not writable), add this line manually at the end of the file.

If you want to list all language maps directly, you can add:

```
Sitemap: https://yoursite/sitemap-pages.xml
Sitemap: https://yoursite/en/sitemap-pages.xml
Sitemap: https://yoursite/ru/sitemap-pages.xml
Sitemap: https://yoursite/pl/sitemap-pages.xml
```

However, it is preferable to use the index file (see below) and specify only that.

---

## <a id="browser-check-en-md"></a>Browser Check

Right after installation and configuration, you can open the following addresses (the cache will be created automatically on first access):

- **Default language (ua):** `https://yoursite/sitemap-pages.xml`
- **English version:** `https://yoursite/en/sitemap-pages.xml`
- **Russian version:** `https://yoursite/ru/sitemap-pages.xml`
- **Polish version:** `https://yoursite/pl/sitemap-pages.xml`
- **Index file (list of all languages):** `https://yoursite/index.php?r=sitemap_pages&a=index`

Each of these links will show an XML document with page URLs (and categories, if pagination is enabled). If you see an empty `<urlset>`, check whether there are translated pages for that language in the `cot_i18n_pages` table and whether the `i18n` plugin is enabled with correct locales.

---

## <a id="google-search-console-en-md"></a>How to Submit to Google Search Console

1. Sign in to **Google Search Console** ([https://search.google.com/search-console](https://search.google.com/search-console)).
2. Select the desired property (website).
3. In the left menu, go to: **Index → Sitemaps**.
4. Click **Add a new sitemap**.
5. Paste the index file URL: `https://yoursite/index.php?r=sitemap_pages&a=index`
6. Click **Submit**.

Google will read the index file and automatically add all the language sub-maps listed in it. You can also submit each language map individually, but it is not necessary.

After submission, check the status — it should show “Success” and the number of discovered pages.

---

## <a id="caching-en-md"></a>Caching and Cache Clearing

The plugin saves generated XML files in the folder `datas/cache/sitemap_pages/`. Inside you will see:

- `ua.xml` – map for the default language.
- `en.xml`, `ru.xml`, `pl.xml` – maps for other languages.
- `*.count` – auxiliary files with record counts.
- `sitemap_pages_index.xml` – the index file (if it was requested).

If you change the plugin settings or modify the code, **delete all files from this folder manually** (via FTP or a file manager). The cache will be regenerated automatically on the next request to any map. In everyday use, clearing is not required – only when changes are made.

---

## <a id="troubleshooting-en-md"></a>Troubleshooting

- **All language URLs show only `ua.xml`.**  
  Cause: missing the `[QSA]` flag in the `.htaccess` rules (see section `.htaccess` Configuration). Add `QSA` to both lines.
- **The URL contains a duplicated language prefix (`/en/en/page.php?id=1`).**  
  Cause: the `sitemap_pages_url` function does not remove the already added prefix. Make sure that `inc/sitemap_pages.functions.php` uses the correct version of the function (it cleans the path of known prefixes).
- **Empty file for some languages.**  
  This means there are no records in the `cot_i18n_pages` (or `cot_i18n_structure`) table with the corresponding locale. Check the translations in the `i18n` plugin.
- **The map does not update after changing settings.**  
  Clear the cache manually (delete the contents of `datas/cache/sitemap_pages/`).
- **Error “XML declaration allowed only at the start of the document” in the browser.**  
  Make sure that the `sitemap_pages.tpl` and `sitemap_pages.index.tpl` templates do not contain the line `<?xml version="1.0" encoding="UTF-8"?>`, and that the script itself starts with the correct XML declaration (it is added programmatically). Also check that the files do not have a BOM or extra whitespace at the beginning.
- **Google Search Console does not accept the map.**  
  Make sure the file conforms to the Sitemap Protocol standard. Use an XML sitemap validator to check, for example:  
  [https://www.xml-sitemaps.com/validate-xml-sitemap.html](https://www.xml-sitemaps.com/validate-xml-sitemap.html)

---

## <a id="additional-notes-en-md"></a>Additional Notes

- The `sitemap_pages.index.tpl` template is only used for the page with `?a=index` and generates a `<sitemapindex>`. If you do not plan to use the index, you can leave this template as is.
- The plugin is completely independent from the old `sitemap` plugin. You can continue using both simultaneously for different modules.
- When adding a new language, make sure it is present in:
  - the plugin settings (`languages`);
  - the `.htaccess` rules (the `(en|ru|pl|ua)` list);
  - the prefix removal function (`$knownLangs` in `sitemap_pages_url`).
- All questions, suggestions, and bug reports can be sent to support:  
  [https://abuyfile.com/forums/cotonti/custom/plugs/](https://abuyfile.com/forums/cotonti/custom/plugs/)

---

## <a id="comparison-main-en-md"></a>Which Is Better: One Huge File or Separate Maps by Module and Language?

**Direct answer:** separate maps by module and language are **better** for my specific project. One huge file creates more problems than it solves.

Here is a comparison on key points.

### <a id="advantages-en-md"></a>✅ Advantages of Separate Maps (My Approach)

1. **Generation performance**  
   Each map is generated only for its own module (market / page) and language. This is faster, uses less memory, and does not block the site. A huge combined file would have to be completely rebuilt even if only one page changed.
2. **Flexible caching**  
   Each module has its own cache TTL (Time To Live – the cache lifetime in seconds). Products might update daily, while pages update weekly. A single file lacks this flexibility – everything would share the same TTL.
3. **Fault tolerance**  
   If the product map generation breaks, the page map continues to work. In a monolithic file, one error breaks everything.
4. **Ease of debugging**  
   You can immediately see which module/language caused the problem. The logic of each module is isolated.
5. **Alignment with Cotonti architecture**  
   Cotonti is modular – plugins like `sitemap_market` and `sitemap_pages` are independent, they can be installed and updated separately. A monolith would break this concept.
6. **SEO transparency**  
   Search engines get a clear structure: a separate map for products, a separate one for pages, each in multiple language versions. This simplifies indexing and coverage analysis in Search Console.
7. **Avoiding limits**  
   Google accepts files up to 50,000 URLs and 50 MB. By splitting by module and language, you avoid hitting the limit without additional fragmentation.

### <a id="disadvantages-en-md"></a>❌ Disadvantages of Separate Maps

1. **More entries in robots.txt**  
   Instead of a single `Sitemap` line, you either need to list several maps or add an index file (`sitemapindex`) that points to the others. This is not a problem, just a minor complication.
2. **Slightly more requests from search engines**  
   Instead of one file, the bot will fetch several. But this is negligible load, and Google handles multiple sitemap files without issues.

### <a id="old-monolith-en-md"></a>🤔 What Would Have Happened with One Big File

- Generation would take many times longer, using more memory.
- Every update to any module would require the entire file to be rebuilt from scratch.
- It would be harder to stay within the 50,000 URL limit – you would still need to split into parts, losing the point of a monolith.
- Debugging would become more complex: any error in one module would break the entire map.

In the old monolithic plugin, all modules (market, page, forums, users) were gathered into one XML file (or several parts if the limit was exceeded). In that setup:

- Settings like `market_freq`, `page_freq`, `forums_freq`, `users_freq` only set the `<changefreq>` tag inside the XML – they told the search engine how often the content **supposedly** changes. But **they did not control the actual regeneration** of the map.
- The actual regeneration frequency was determined by a **single parameter `cache_ttl`** for the whole plugin. As soon as the cache expired (more seconds than specified in `cache_ttl` had passed since the last generation), **data from all modules was collected anew** – even if products update every 5 minutes and pages once a month.

Thus, adding a single new product forced the entire giant file to be rebuilt: market categories and products, pages, forums, users. The more modules were enabled, the longer and more resource-intensive each regeneration became.

In the new approach (separate plugins), each module has its **own `cache_ttl`**, so:

- The product map can regenerate every 30 minutes,
- The page map – once a day,
- The forum map – once a week.

When a product is updated, only the product map is rebuilt; the page and forum maps remain in cache and consume no resources. That is the flexibility the monolith lacked.

### <a id="conclusion-en-md"></a>Conclusion

**Conclusion:** the current approach — separate maps by module and language — is optimal for Cotonti. It scales well, runs faster, and better matches the system’s modular architecture. One huge file only wins in the minimalism of robots.txt, but that advantage is neutralized by using an index file (`sitemapindex`), which you can easily enable.

---

## <a id="note-setup-en-md"></a>Notes on Sitemap Pages Plugin Setup and Operation

### <a id="language-setting-en-md"></a>“Languages” Setting (`languages`) – Do You Need to Include the Default Language?

**In short:** now it is **not necessary** to include the default language in the `languages` field. The `sitemap_pages_get_languages()` function **always forcibly adds** the default language (`default_lang`) to the list if it is missing.

**How it works with different input scenarios:**

- **The `languages` field is filled** (e.g., `en,ru,pl`)  
  The plugin takes the specified languages and adds `default_lang` to them (if not already in the list). As a result, the index will contain all listed languages + the default language. This guarantees that the primary language is never omitted, even if the admin forgets to include it.
- **The `languages` field is empty**  
  The plugin retrieves all active locales from the `Cot::$cfg['plugin']['i18n']['locales']` setting (e.g., `ua, en, ru, pl`) and also adds `default_lang` if it happens to be missing. Thus, the index includes all available site languages without the need to list them manually.

**Recommendation:**  
If you want all languages supported by the i18n plugin to appear in the sitemap, simply leave the `languages` field empty. If you need to limit the set of languages (for example, exclude a language), list the desired codes separated by commas — the default language will be added automatically.

---

### <a id="how-settings-affect-index-en-md"></a>How Settings Affect the Index File (`?r=sitemap_pages&a=index`)

The index file is built strictly based on the language array returned by the `sitemap_pages_get_languages()` function. Therefore:

- Regardless of the **configuration** (empty or a list), the index **always contains the default language**.
- If additional languages are listed in the `languages` field, they also appear in the index.
- If the field is empty, the index includes **all locales** registered in the i18n plugin.

As a result, you get a complete index where each language has a separate `<sitemap>` entry pointing to the corresponding language map (e.g., `/sitemap-pages.xml` for the default language and `/en/sitemap-pages.xml` for English).

---

### <a id="index-file-purpose-en-md"></a>Why the File at `?r=sitemap_pages&a=index` Is Needed and What It Should Contain

This is a **Sitemap Index file** — a standard element of the Sitemaps protocol designed to combine multiple sitemaps into a single entry point.

**Purpose:**

- Allows you to submit **one URL** to search engines, which automatically reveals all language versions of the maps.
- Complies with search engine limits (no more than 50,000 URLs or 50 MB per file) — splitting by language removes these limits.
- Simplifies management: when adding a new language, you don’t need to modify `robots.txt`, just update the plugin settings.

#### <a id="index-must-have-en-md"></a>What the Index Must Contain

- A root `<sitemapindex>` element with the correct namespace.
- For each active language — a separate `<sitemap>` block containing:
  - `<loc>` — the absolute URL of the language map (for the primary language – without a prefix, for others – with a prefix like `/en/`).
  - `<lastmod>` — the last modification date of that map.
- All languages for which at least one page or category exists.

#### <a id="index-must-not-have-en-md"></a>What Must Not Be Present

- `<url>` elements (they are allowed only inside regular sitemaps, not in an index).
- Missing languages (the primary language must always be present).
- Duplicate links to the same language map.
- Broken links to non-existent map files.
- Languages for which there are no translated pages or translated categories (they can still be present, but then the map will be empty — this is allowed but not optimal; it is better to exclude them if no translation is planned).

#### <a id="index-example-en-md"></a>Example of a Correct Index for Four Languages (`ua`, `en`, `ru`, `pl`)

```xml
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://yourproject.com/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/en/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/ru/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/pl/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
</sitemapindex>
```

This is the file you should submit to Google Search Console — it will ensure the indexing of all language versions of your site.

---

### <a id="rewrite-index-rule-en-md"></a>Adding a Rewrite Rule for the Index File

If you want to create a rewrite rule for the sitemap index URL `https://yourproject.com/index.php?r=sitemap_pages&a=index` so that it becomes a clean URL:

Add the following rule to the root `.htaccess` **right after the lines**

```apache
RewriteRule ^sitemap-pages\.xml$ index.php?r=sitemap_pages [QSA,L]
RewriteRule ^(en|ru|pl|ua)/sitemap-pages\.xml$ index.php?r=sitemap_pages&l=$1 [QSA,L]
```

and **before the line**

```apache
# All the rest goes through standard rewrite gateway
```

this directive:

```apache
RewriteRule ^sitemap-pages-index\.xml$ index.php?r=sitemap_pages&a=index [QSA,L]
```

After that, the index file will be available at the clean URL:  
`https://yourproject.com/sitemap-pages-index.xml`

Do not remove the existing rule for `?r=sitemap_pages&a=index` — it will work in parallel. But for Google Search Console, now use specifically `https://yourproject.com/sitemap-pages-index.xml`.



---

## Оглавление

1. [Системные требования и зависимости](#system-requirements)
2. [Структура плагина](#plugin-structure)
3. [Установка](#installation)
4. [Настройка плагина в админке](#admin-settings)
5. [Настройка `.htaccess`](#htaccess-setup)
6. [Настройка `robots.txt`](#robots-setup)
7. [Проверка в браузере](#browser-check)
8. [Как отправить в Google Search Console](#google-search-console)
9. [Кеширование и его очистка](#caching)
10. [Устранение неполадок](#troubleshooting)
11. [Дополнительные замечания](#additional-notes)
12. [Что лучше: один огромный файл или раздельные карты по модулям и языкам?](#comparison-main)
    - [Преимущества раздельных карт](#advantages)
    - [Недостатки раздельных карт](#disadvantages)
    - [Что было бы с одним большим файлом](#old-monolith)
    - [Вывод](#conclusion)
13. [Заметка по настройке и работе плагина Sitemap Pages](#note-setup)
    - [Настройка «Языки» (languages) — нужно ли указывать язык по умолчанию?](#language-setting)
    - [Как настройки влияют на индексный файл](#how-settings-affect-index)
    - [Зачем нужен файл по ссылке `?r=sitemap_pages&a=index`](#index-file-purpose)
      - [Что должно быть в индексе](#index-must-have)
      - [Что не должно присутствовать](#index-must-not-have)
      - [Пример корректного индекса](#index-example)
    - [Добавление правила перезаписи для индексного файла](#rewrite-index-rule)

---

## <a id="system-requirements"></a>Системные требования и зависимости

- **Cotonti** версии 1.0+ (протестировано с PHP 8.5+ и MySQL 8.4).
- **Модуль `page`** – должен быть установлен и активен.
- **Плагин `i18n`** (Content Internationalization) – должен быть установлен и активен. Именно из его таблиц (`cot_i18n_structure` и `cot_i18n_pages`) плагин узнаёт, для каких категорий и страниц есть перевод на конкретный язык.
- Для работы красивых URL требуется настроенный mod_rewrite в Apache (или аналог в Nginx).

---

## <a id="plugin-structure"></a>Структура плагина

Плагин размещается в папке `plugins/sitemap_pages/` и содержит следующие файлы:

```
sitemap_pages/
├── sitemap_pages.setup.php           # Заголовок, метаданные и настройки плагина
├── sitemap_pages.ajax.php            # Основной обработчик запросов (AJAX)
├── inc/
│   └── sitemap_pages.functions.php   # Функции: генерация карт, работа с URL и языками
├── lang/
│   ├── sitemap_pages.ru.lang.php     # Русские подписи к настройкам
│   └── sitemap_pages.en.lang.php     # Английские подписи к настройкам
├── tpl/
│   ├── sitemap_pages.tpl             # Шаблон для обычного urlset
│   └── sitemap_pages.index.tpl       # Шаблон для индексного файла (sitemapindex)
└── setup/
    └── sitemap_pages.install.php     # Автоматическое добавление ссылки в robots.txt при установке
```

Шаблоны уже содержат правильную XML-разметку и не требуют ручной правки.

---

## <a id="installation"></a>Установка

1. Скачайте папку `sitemap_pages` и скопируйте её в директорию `plugins/` вашего сайта Cotonti.
2. Перейдите в админку: **Расширения → Плагины**.
3. Найдите в списке `Pages Sitemap (multilingual)` и нажмите **Установить**.
4. При установке плагин автоматически добавит строку `Sitemap: https://вашсайт/sitemap-pages.xml` в `robots.txt` (если файл доступен для записи). Старые упоминания `sitemap-pages.xml` будут удалены, чтобы избежать дублирования.

После установки плагин готов к настройке.

---

## <a id="admin-settings"></a>Настройка плагина в админке

Перейдите в **Расширения → Плагины → Pages Sitemap (multilingual) → Настройки**.

| Параметр | Значение по умолчанию | Описание |
|----------|------------------------|----------|
| **Языки (languages)** | *пусто* | Список языков через запятую, например: `en,ru,ua`. Если оставить пустым, используются все активные языки из конфигурации Cotonti (`Cot::$cfg['plugin']['i18n']['locales']`). |
| **Язык по умолчанию (default_lang)** | `ua` | Язык, для которого в URL не добавляется префикс (например, `/page.php?id=1` вместо `/ua/page.php?id=1`). |
| **Использовать красивые URL (use_pretty_urls)** | `0` (выключено) | Если включено (`1`), адреса карт будут иметь вид `/sitemap-pages.xml` и `/en/sitemap-pages.xml`. Если выключено, используются прямые ссылки с `index.php?r=sitemap_pages`. Рекомендуется включить после настройки `.htaccess`. |
| **Включать пагинацию категорий (pageCategoryPagination)** | `1` (включено) | Если у категории несколько страниц материалов, в карту будут добавлены URL с параметрами `?d=2`, `?d=3` и т.д. |
| **Частота изменения страниц (page_freq)** | `weekly` | Значение тега `<changefreq>` для страниц. |
| **Приоритет страниц (page_prio)** | `0.5` | Значение тега `<priority>` для страниц. |
| **Макс. ссылок на часть карты (perpage)** | `50000` | Если общее количество URL превышает это число, карта будет разбита на несколько файлов (в большинстве случаев это не требуется). |
| **Время жизни кеша (cache_ttl)** | `3600` | Период (в секундах), через который кеш карт будет считаться устаревшим и пересоздастся при следующем обращении. |

После изменения настроек нажмите **Сохранить**.

---

## <a id="htaccess-setup"></a>Настройка `.htaccess`

Для работы красивых URL (если вы включили `use_pretty_urls`) добавьте в корневой `.htaccess` следующие строки **сразу после** правила `RewriteRule ^sitemap\.xml$ ...`:

```apache
RewriteRule ^sitemap-pages\.xml$ index.php?r=sitemap_pages [QSA,L]
RewriteRule ^(en|ru|pl|ua)/sitemap-pages\.xml$ index.php?r=sitemap_pages&l=$1 [QSA,L]
```

> **Важно:** флаг `[QSA]` (Query String Append) обязателен, потому что языковое правило Cotonti уже может добавить параметр `?l=en`, и без `QSA` он будет потерян.

Убедитесь, что эти правила расположены **до** строки `# All the rest goes through standard rewrite gateway`.

---

## <a id="robots-setup"></a>Настройка `robots.txt`

Плагин при установке автоматически добавляет строку:

```
Sitemap: https://вашсайт/sitemap-pages.xml
```

Если автоматическое добавление не сработало (например, файл `robots.txt` недоступен для записи), добавьте эту строку вручную в конец файла.

Если вы хотите перечислить все языковые карты напрямую, можно добавить:

```
Sitemap: https://вашсайт/sitemap-pages.xml
Sitemap: https://вашсайт/en/sitemap-pages.xml
Sitemap: https://вашсайт/ru/sitemap-pages.xml
Sitemap: https://вашсайт/pl/sitemap-pages.xml
```

Однако предпочтительнее использовать индексный файл (см. ниже) и указывать только его.

---

## <a id="browser-check"></a>Проверка в браузере

Сразу после установки и настройки можно открыть следующие адреса (кеш создастся автоматически при первом обращении):

- **Основной язык (ua):** `https://вашсайт/sitemap-pages.xml`
- **Английская версия:** `https://вашсайт/en/sitemap-pages.xml`
- **Русская версия:** `https://вашсайт/ru/sitemap-pages.xml`
- **Польская версия:** `https://вашсайт/pl/sitemap-pages.xml`
- **Индексный файл (список всех языков):** `https://вашсайт/index.php?r=sitemap_pages&a=index`

Каждая из этих ссылок покажет XML‑документ с URL‑адресами страниц (и категорий, если включена пагинация). Если вы видите пустой `<urlset>`, проверьте, есть ли для данного языка переведённые страницы в таблице `cot_i18n_pages`, и включён ли плагин `i18n` с корректными локалями.

---

## <a id="google-search-console"></a>Как отправить в Google Search Console

1. Войдите в **Google Search Console** (https://search.google.com/search-console).
2. Выберите нужный ресурс (сайт).
3. В левом меню перейдите: **Индекс → Файлы Sitemap**.
4. Нажмите **Добавить новый файл Sitemap**.
5. Вставьте URL индексного файла: `https://вашсайт/index.php?r=sitemap_pages&a=index`
6. Нажмите **Отправить**.

Google прочитает индексный файл и автоматически добавит все языковые подкарты, которые в нём перечислены. Также можно отправить каждую языковую карту отдельно, но это не обязательно.

После отправки проверьте статус — должно появиться «Успешно» и количество обнаруженных страниц.

---

## <a id="caching"></a>Кеширование и его очистка

Плагин сохраняет сгенерированные XML‑файлы в папке `datas/cache/sitemap_pages/`. Внутри вы увидите:

- `ua.xml` – карта для языка по умолчанию.
- `en.xml`, `ru.xml`, `pl.xml` – карты для других языков.
- `*.count` – вспомогательные файлы с количеством записей.
- `sitemap_pages_index.xml` – индексный файл (если он был запрошен).

Если вы изменили настройки плагина или внесли правки в код, **удалите все файлы из этой папки вручную** (через FTP или файловый менеджер). При следующем обращении к любой карте кеш пересоздастся автоматически. В повседневной работе очистка не требуется – только при изменениях.

---

## <a id="troubleshooting"></a>Устранение неполадок

- **Все языковые URL показывают только `ua.xml`.**  
  Причина: отсутствует флаг `[QSA]` в правилах `.htaccess` (см. раздел 5). Добавьте `QSA` в обе строки.

- **В URL дублируется языковой префикс (`/en/en/page.php?id=1`).**  
  Причина: функция `sitemap_pages_url` не удаляет уже добавленный префикс. Убедитесь, что в `inc/sitemap_pages.functions.php` используется правильная версия функции (она очищает путь от известных префиксов).

- **Для некоторых языков пустой файл.**  
  Значит, в таблице `cot_i18n_pages` (или `cot_i18n_structure`) нет записей с соответствующей локалью. Проверьте переводы в плагине `i18n`.

- **После изменения настроек карта не обновляется.**  
  Очистите кеш вручную (удалите содержимое `datas/cache/sitemap_pages/`).

- **Ошибка «XML declaration allowed only at the start of the document» в браузере.**  
  Убедитесь, что в шаблонах `sitemap_pages.tpl` и `sitemap_pages.index.tpl` нет строки `<?xml version="1.0" encoding="UTF-8"?>`, а сам скрипт начинается с правильной XML-декларации (она добавляется программно). Также проверьте, что в начале файлов нет BOM или лишних пробелов.

- **Google Search Console не принимает карту.**  
  Убедитесь, что файл соответствует стандарту Sitemap Protocol. Для проверки используйте валидатор XML-карт, например: https://www.xml-sitemaps.com/validate-xml-sitemap.html

---

## <a id="additional-notes"></a>Дополнительные замечания

- Шаблон `sitemap_pages.index.tpl` используется только для страницы с `?a=index` и формирует `<sitemapindex>`. Если вы не планируете использовать индекс, этот шаблон можно не трогать.
- Плагин полностью независим от старого плагина `sitemap`. Вы можете продолжать использовать их одновременно для разных модулей.
- При добавлении нового языка убедитесь, что он присутствует:
  - в настройках плагина (`languages`);
  - в правилах `.htaccess` (список `(en|ru|pl|ua)`);
  - в функции удаления префиксов (`$knownLangs` в `sitemap_pages_url`).
- Все вопросы, предложения и сообщения об ошибках можно направлять в поддержку: https://abuyfile.com/forums/cotonti/custom/plugs/

---

## <a id="comparison-main"></a>Что лучше: один огромный файл или раздельные карты по модулям и языкам?

**Прямой ответ:** раздельные карты по модулям и языкам — **лучше** для моего конкретного проекта. Один огромный файл создаёт больше проблем, чем решает.

Вот сравнение по ключевым пунктам.

---

### <a id="advantages"></a>✅ Преимущества раздельных карт (мой подход)

1. **Производительность генерации**  
   Каждая карта генерируется только для своего модуля (market / page) и языка. Это быстрее, потребляет меньше памяти и не блокирует сайт. Огромный общий файл пришлось бы пересобирать целиком даже при изменении одной страницы.

2. **Гибкое кеширование**  
   У каждого модуля свой TTL кеша (Time To Live — это время жизни кеша в секундах). Товары могут обновляться ежедневно, а страницы — раз в неделю. В общем файле такой гибкости нет.

3. **Устойчивость к ошибкам**  
   Если сломается генерация карты товаров, карта страниц продолжит работать. В монолитном файле одна ошибка ломает всё.

4. **Удобство отладки**  
   Сразу видно, какой модуль/язык вызвал проблему. Логика каждого модуля изолирована.

5. **Соответствие архитектуре Cotonti**  
   Cotonti модульный — плагины `sitemap_market` и `sitemap_pages` независимы, их можно устанавливать и обновлять отдельно. Монолит нарушил бы эту идею.

6. **SEO‑прозрачность**  
   Поисковики получают чёткую структуру: отдельная карта для товаров, отдельная для страниц, каждая в нескольких языковых версиях. Это упрощает индексацию и анализ покрытия в Search Console.

7. **Обход лимитов**  
   Google принимает файлы до 50 000 URL и 50 МБ. Разделяя по модулям и языкам, вы избегаете превышения лимита без дополнительного дробления.

---

### <a id="disadvantages"></a>❌ Недостатки раздельных карт

1. **Больше записей в robots.txt**  
   Вместо одной строки `Sitemap` нужно либо перечислить несколько карт, либо добавить индексный файл (`sitemapindex`), который ссылается на остальные. Это не проблема, а небольшое усложнение.

2. **Чуть больше запросов от поисковиков**  
   Вместо одного файла бот загрузит несколько. Но это копеечная нагрузка, и Google нормально обрабатывает множество sitemap‑файлов.

---

### <a id="old-monolith"></a>🤔 Что было бы с одним большим файлом

- Генерация занимала бы в разы больше времени, использовала больше памяти.
- При каждом обновлении любого модуля пришлось бы пересобирать весь файл целиком.
- Труднее соблюсти лимит в 50 000 URL — пришлось бы всё равно дробить на части, теряя смысл монолитности.
- Отладка стала бы сложнее: любая ошибка в одном модуле рушила бы всю карту.

В старом монолитном плагине все модули (market, page, forums, users) собирались в один XML‑файл (или несколько частей, если превышен лимит). При этом:

- Настройки **`market_freq`**, **`page_freq`**, **`forums_freq`**, **`users_freq`** задавали только тег `<changefreq>` внутри XML – они сообщали поисковику, как часто **предположительно** меняется контент. Но **они не управляли реальной перегенерацией** карты.
- Реальная частота перегенерации определялась **единственным параметром `cache_ttl`** для всего плагина. Как только кеш устаревал (с момента последней генерации проходило больше секунд, чем указано в `cache_ttl`), **собирались заново данные всех модулей сразу** – даже если товары обновляются каждые 5 минут, а страницы – раз в месяц.

Таким образом, добавление одного нового товара заставляло пересобирать весь гигантский файл: категории и товары market, страницы, форумы, пользователей. Чем больше модулей было включено, тем дольше и ресурсоёмчее была каждая регенерация.

В новом подходе (раздельные плагины) каждый модуль имеет **собственный `cache_ttl`**, поэтому:

- Карта товаров может перегенерироваться каждые 30 минут,
- Карта страниц – раз в сутки,
- Карта форумов – раз в неделю.

Когда обновляется товар, пересобирается только карта товаров, а карты страниц и форумов остаются в кеше и не тратят ресурсы. Это и есть гибкость, которой не хватало монолиту.

### <a id="conclusion"></a>Вывод

Текущий подход — раздельные карты по модулям и языкам — оптимален для Cotonti. Он масштабируется, быстрее работает и лучше соответствует модульной архитектуре системы. Один огромный файл выигрывает только в минимализме robots.txt, но это преимущество нивелируется использованием индексного файла (`sitemapindex`), который вы можете легко включить.

---

## <a id="note-setup"></a>Заметка по настройке и работе плагина Sitemap Pages

### <a id="language-setting"></a>1. Настройка «Языки» (`languages`) — нужно ли указывать язык по умолчанию?

**Кратко:** теперь **не обязательно** указывать язык по умолчанию в поле `languages`. Функция `sitemap_pages_get_languages()` в любом случае **принудительно добавляет** язык по умолчанию (`default_lang`) в список, если он там отсутствует.

**Как работает при разных вариантах заполнения:**

- **Поле `languages` заполнено** (например, `en,ru,pl`)  
  Плагин берёт указанные языки и добавляет к ним `default_lang` (если его нет в списке). В результате в индексе будут все перечисленные языки + язык по умолчанию. Это гарантирует, что основной язык никогда не будет пропущен, даже если администратор забыл его вписать.

- **Поле `languages` пустое**  
  Плагин извлекает все активные локали из настройки `Cot::$cfg['plugin']['i18n']['locales']` (например, `ua, en, ru, pl`) и также добавляет `default_lang`, если его вдруг нет. Таким образом, в индекс попадают все доступные языки сайта без необходимости перечислять их вручную.

**Рекомендация:**  
Если вы хотите, чтобы в карту сайта попали все языки, поддерживаемые плагином i18n, просто оставьте поле `languages` пустым. Если нужно ограничить набор языков (например, исключить какой-то язык), перечислите нужные коды через запятую — язык по умолчанию будет добавлен автоматически.

---

### <a id="how-settings-affect-index"></a>2. Как настройки влияют на индексный файл (`?r=sitemap_pages&a=index`)

Индексный файл формируется строго на основе массива языков, возвращаемого функцией `sitemap_pages_get_languages()`. Поэтому:

- При **любом** варианте настройки (пусто или список) в индексе **всегда присутствует язык по умолчанию**.
- Если в поле `languages` перечислены дополнительные языки, они также попадают в индекс.
- Если поле пустое, в индекс попадают **все локали**, зарегистрированные в плагине i18n.

В результате вы получаете полный индекс, где для каждого языка есть отдельная запись `<sitemap>`, ссылающаяся на соответствующую языковую карту (например, `/sitemap-pages.xml` для языка по умолчанию и `/en/sitemap-pages.xml` для английского).

---

### <a id="index-file-purpose"></a>3. Зачем нужен файл по ссылке `?r=sitemap_pages&a=index` и что в нём должно быть

Это **индексный файл Sitemap Index** — стандартный элемент протокола Sitemaps, предназначенный для объединения нескольких карт сайта в одну точку входа.

**Назначение:**

- Позволяет отправить в поисковые системы **один URL**, который автоматически раскрывает все языковые версии карт.
- Соответствует ограничениям поисковиков (не более 50 000 URL или 50 МБ на один файл) — разделение по языкам снимает эти лимиты.
- Упрощает управление: при добавлении нового языка не нужно менять `robots.txt`, достаточно обновить настройки плагина.

#### <a id="index-must-have"></a>Что должно быть в индексе

- Корневой элемент `<sitemapindex>` с правильным namespace.
- Для каждого активного языка — отдельный блок `<sitemap>`, содержащий:
  - `<loc>` — абсолютный URL языковой карты (для основного языка — без префикса, для остальных — с префиксом вида `/en/`).
  - `<lastmod>` — дата последней генерации этой карты.
- Все языки, для которых существует хотя бы одна страница или категория.

#### <a id="index-must-not-have"></a>Что не должно присутствовать

- Элементы `<url>` (они допустимы только внутри обычных карт, а не в индексе).
- Пропущенные языки (основной язык должен быть обязательно).
- Дублирующиеся ссылки на одну и ту же языковую карту.
- Битые ссылки на несуществующие файлы карт.
- Языки, для которых нет ни переведённых страниц, ни переведённых категорий (они всё равно могут присутствовать, но тогда карта будет пустой — это допустимо, но не оптимально; лучше исключить их, если не планируется перевод).

#### <a id="index-example"></a>Пример корректного индекса для четырёх языков (`ua`, `en`, `ru`, `pl`)

```xml
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://yourproject.com/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/en/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/ru/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://yourproject.com/pl/sitemap-pages.xml</loc>
    <lastmod>2026-08-04T09:02:52+00:00</lastmod>
  </sitemap>
</sitemapindex>
```

Именно этот файл следует отправлять в Google Search Console — он обеспечит индексацию всех языковых версий вашего сайта.

---

### <a id="rewrite-index-rule"></a>Добавление правила перезаписи для индексного файла

Если нужно создать правило перезаписи для URL-адреса `https://yourproject.com/index.php?r=sitemap_pages&a=index` индекса карты сайта, чтобы он был красивым:

Добавьте в корневой `.htaccess` следующее правило **сразу после строк**

```apache
RewriteRule ^sitemap-pages\.xml$ index.php?r=sitemap_pages [QSA,L]
RewriteRule ^(en|ru|pl|ua)/sitemap-pages\.xml$ index.php?r=sitemap_pages&l=$1 [QSA,L]
```

и **перед строкой**

```apache
# All the rest goes through standard rewrite gateway
```

вот эту директиву:

```apache
RewriteRule ^sitemap-pages-index\.xml$ index.php?r=sitemap_pages&a=index [QSA,L]
```

После этого индексный файл будет доступен по красивому URL:  
`https://yourproject.com/sitemap-pages-index.xml`

Уже существующее правило для `?r=sitemap_pages&a=index` не удаляйте — оно будет работать параллельно. Но для Google Search Console теперь используйте именно `https://yourproject.com/sitemap-pages-index.xml`.

---

Теперь мультиязычные карты страниц вашего сайта будут автоматически обновляться и индексироваться поисковыми системами.
