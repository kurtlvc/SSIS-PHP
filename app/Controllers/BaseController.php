<?php

class BaseController
{
    protected $middleware;
    protected $db;
    protected $config;

    public function __construct()
    {
        $this->middleware = new Middleware();
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
    }

    protected function requireAuth()
    {
        return $this->middleware->auth();
    }

    protected function requireRole($roles)
    {
        return $this->middleware->role($roles);
    }

    protected function render($view, $data = [])
    {
        $data['config'] = $this->config;
        $data['session'] = $_SESSION;
        App::view($view, $data);
    }

    protected function getStudentId()
    {
        return $_SESSION['stud_id'] ?? null;
    }
}
