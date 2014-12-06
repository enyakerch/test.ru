<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи и ABSPATH. Дополнительную информацию можно найти на странице
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется скриптом для создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения вручную.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wordpress');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '19920821');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '#gh0,G*Be[*>t@G7_l}RwBHfQ>_i#{g60]6w0+fq{if$Sl%i2iG+;z<)]vJqx1lE');
define('SECURE_AUTH_KEY',  'Hzx~^sY3d|K{q_Q6gZl1IA%e9@6hU:=R$_Ig4zX*sul%wEYU~ ^U1(c7@|ONRGqY');
define('LOGGED_IN_KEY',    '9&J-SN8/~ #R:[yKcvW@e6vpK@|dTz9#YX7AB3hA&W@=a++0|&{yLA.]+,v{<E]C');
define('NONCE_KEY',        'mxF-q zxCf~2T{es%&-qN)i?Y1-f8E)71@a2Y0gm[BpQi}^Q>@^=c-7nOB5Vf<+_');
define('AUTH_SALT',        '[3wE|L~L4dcO^O^R^|X+s-7~-[WYEM#<[JL,GZUJh]gc3aP:Yt`?Eck~@iNLOXgT');
define('SECURE_AUTH_SALT', 'am,A$ ,R6PY{do+`%E.^,RUn:mA2xuy)YE?mzWeJ|$yP(x!:Gv.+rk4h.C4MtIw-');
define('LOGGED_IN_SALT',   '$q=A<:Hj}$+=CrFENO/=+p[FQZz}-9+|zfbQ@wGrA?[W~Iu6]m+W`V$E|3K_i34-');
define('NONCE_SALT',       'B{l~|x<g6+MI*o6i{Q^]8P!<P9Kl:Ug:>Kns]fEHFUG5(IMVBxdrM|H|OLVBN/v%');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
