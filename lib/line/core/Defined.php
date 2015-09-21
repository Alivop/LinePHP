<?php
//system dir
define('LP_DS', DIRECTORY_SEPARATOR);
define('LP_CORE_PATH', __DIR__ . LP_DS);
define('LP_PATH', dirname(LP_CORE_PATH) . LP_DS);
define('LP_LIBRARY_PATH', dirname(LP_PATH) . LP_DS);
define('LP_ROOT', dirname(LP_LIBRARY_PATH) . LP_DS);
define('LP_CONF_PATH', LP_PATH . 'conf' . LP_DS);
define('LP_LANG_PATH', LP_PATH . 'i18n' . LP_DS);
define('LP_IO_PATH', LP_PATH . 'io' . LP_DS);
define('LP_DB_PATH', LP_PATH . 'db' . LP_DS);
define('LP_LOG_PATH', LP_PATH . 'logger' . LP_DS);
define('LP_CORE_CONF', LP_CORE_PATH . 'config' . LP_DS);
define('LP_CORE_LINE', LP_CORE_PATH . 'linephp' . LP_DS);
define('LP_CORE_ABSTRACT', LP_CORE_PATH . 'abstract' . LP_DS);
define('LP_IO_LINE', LP_IO_PATH . 'linephp' . LP_DS);
define('LP_DB_LINE', LP_DB_PATH . 'linephp' . LP_DS);

//system error code
define('ERROR_500', 500);
define('ERROR_400', 400);