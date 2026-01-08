<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use flight\debug\tracy\TracyExtensionLoader;
use Tracy\Debugger;
use Ghostff\Session\Session;

/*********************************************
 *         FlightPHP Service Setup           *
 *********************************************
 * This file registers services and integrations
 * for your FlightPHP application. Edit as needed.
 *
 * @var array  $config  From config.php
 * @var Engine $app     FlightPHP app instance
 **********************************************/


/*********************************************
 *           Tracy Debugger Setup            *
 *********************************************
 * Tracy is a powerful error handler and debugger for PHP.
 * Docs: https://tracy.nette.org/
 *
 * Key Tracy configuration options:
 *   - Debugger::enable([mode], [ip]);
 *       - mode: Debugger::Development or Debugger::Production
 *       - ip: restrict debug bar to specific IP(s)
 *   - Debugger::$logDirectory: where error logs are stored
 *   - Debugger::$strictMode: show all errors (true/E_ALL), or filter out deprecated notices
 *   - Debugger::$showBar: show/hide debug bar (auto-detected, can be forced)
 *   - Debugger::$maxLen: max length of dumped variables
 *   - Debugger::$maxDepth: max depth of dumped structures
 *   - Debugger::$editor: configure clickable file links (see docs)
 *   - Debugger::$email: send error notifications to email
 *
 * Example Tracy setups:
 *   Debugger::enable(); // Auto-detects environment
 *   Debugger::enable(Debugger::Development); // Explicitly set environment
 *   Debugger::enable('23.75.345.200'); // Restrict debug bar to specific IPs
 *
 * For more options, see https://tracy.nette.org/en/configuration
 **********************************************/
Debugger::enable(); // Auto-detects environment
// Debugger::enable(Debugger::Development); // Explicitly set environment
// Debugger::enable('23.75.345.200'); // Restrict debug bar to specific IPs
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log'; // Log directory
Debugger::$strictMode = true; // Show all errors (set to E_ALL & ~E_DEPRECATED for less noise)
// Debugger::$maxLen = 1000; // Max length of dumped variables (default: 150)
// Debugger::$maxDepth = 5; // Max depth of dumped structures (default: 3)
// Debugger::$editor = 'vscode'; // Enable clickable file links in debug bar
// Debugger::$email = 'your@email.com'; // Send error notifications


/**********************************************
 *           Database Service Setup           *
 **********************************************/
// Uncomment and configure the following for your database:

// MySQL Example:
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['database'] . ';charset=utf8mb4';

// Register Flight::db() service
// In development, use PdoQueryCapture to log queries; in production, use PdoWrapper for performance.
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;
$app->register('db', $pdoClass, [ $dsn, $config['database']['username'] ?? null, $config['database']['password'] ?? null ]);

/**********************************************
 *         Third-Party Integrations           *
 **********************************************/
// Add more service registrations below as needed

// Utilisation du plugin Latte et remap de la fonction render sur celle de Latte
$Latte = new \Latte\Engine;
$Latte->setTempDirectory(__DIR__ . '/../cache/');
// PHP 8+
$Latte->addExtension(new Latte\Bridges\Tracy\TracyExtension);
$Latte->addFunction('route', function(string $alias, array $params = []) use ($app) {
    return $app->getUrl($alias, $params);
});
$app->map('render', function(string $templatePath, array $data = [], ?string $block = null) use ($app, $Latte) {
    // Add the username that's available in every template.
    $data += [
        'username' => $app->session()->getOrDefault('user', ''),
        'nonce' => $app->get('csp_nonce'),
        'pwa' => [
            'enable' => $app->get('pwa.enable'),
            'app_name' => $app->get('pwa.app_name'),
            'app_short_name' => $app->get('pwa.app_short_name')
        ]
    ];
    $templatePath = __DIR__ . '/../views/'. $templatePath;
    $Latte->render($templatePath, $data, $block);
});

// Utilisation du plugin Session
$app->register('session', \Ghostff\Session\Session::class, [
    [
        // si vous voulez stocker vos données de session dans une base de données (utile pour quelque chose comme, "me déconnecter de tous les appareils" fonctionnalité)
        Session::CONFIG_DRIVER        => Ghostff\Session\Drivers\MySql::class,
        Session::CONFIG_ENCRYPT_DATA  => true,
        Session::CONFIG_SALT_KEY      => hash('sha256', 'meteodbsalt'), // veuillez changer cela pour quelque chose d'autre
        Session::CONFIG_AUTO_COMMIT   => false, // ne le faites que si c'est nécessaire et/ou si c'est difficile de faire commit() sur votre session.
        // de plus, vous pourriez faire Flight::after('start', function() { Flight::session()->commit(); });
        Session::CONFIG_MYSQL_DS         => [
            'driver'    => 'mysql',             # Pilote de base de données pour PDO dns ex.(mysql:host=...;dbname=...)
            'host'      => $config['database']['host'],         # Hôte de la base de données
            'db_name'   => $config['database']['database'],   # Nom de la base de données
            'db_table'  => 'sessions',          # Table de la base de données
            'db_user'   => $config['database']['username'],              # Nom d'utilisateur de la base de données
            'db_pass'   => $config['database']['password'],                  # Mot de passe de la base de données
            'persistent_conn'=> false,          # Éviter le surcoût d'établir une nouvelle connexion à chaque fois qu'un script doit communiquer avec une base de données, ce qui accélère l'application web. TROUVEZ LE DESSUS VOUS-MÊME
        ]
    ]
    
]);

if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
    (new TracyExtensionLoader($app, [ 'session_data' => $app->session()->getAll()])); // Load FlightPHP Tracy extensions
}

