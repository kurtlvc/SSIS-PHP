<?php

class App
{
    private static $instance = null;
    private $config;
    private $router;
    private $db;

    private function __construct()
    {
        $this->config = require_once __DIR__ . '/../../config/config.php';
        session_start();
        $this->db = Database::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRouter()
    {
        if ($this->router === null) {
            $this->router = new Router();
        }
        return $this->router;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function run()
    {
        $router = $this->getRouter();
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $router->dispatch($method, $uri);
    }

    public static function redirect($path)
    {
        $config = self::getInstance()->getConfig();
        header("Location: {$config['site_url']}{$path}");
        exit;
    }

    public static function view($file, $data = [])
    {
        $config = self::getInstance()->getConfig();
        $viewFile = __DIR__ . '/../Views/' . $file . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "<h1>View not found: {$file}</h1>";
            return;
        }

        extract($data, EXTR_SKIP);

        $layoutFile = __DIR__ . '/../Views/layouts/app.php';
        $content = '';

        if (file_exists($layoutFile) && $file !== 'auth/login') {
            ob_start();
            require_once $viewFile;
            $content = ob_get_clean();
            require_once $layoutFile;
        } else {
            require_once $viewFile;
        }
    }

    public static function json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verify_csrf($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function flash($key, $message = null)
    {
        if ($message === null) {
            $message = $_SESSION['flash'][$key] ?? '';
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        $_SESSION['flash'][$key] = $message;
    }
}
