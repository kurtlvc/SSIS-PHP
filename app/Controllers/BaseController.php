<?php

class BaseController
{
    protected $middleware;
    protected $db;
    protected $config;
    protected $auditService;

    public function __construct()
    {
        $this->middleware = new Middleware();
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
        $this->auditService = new AuditService();
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

    /**
     * Log CREATE operation
     */
    protected function auditCreate(string $entityType, int $entityId, array $newValues): int
    {
        return $this->auditService->logCreate($entityType, $entityId, $newValues);
    }

    /**
     * Log UPDATE operation
     */
    protected function auditUpdate(string $entityType, int $entityId, array $oldValues, array $newValues): int
    {
        return $this->auditService->logUpdate($entityType, $entityId, $oldValues, $newValues);
    }

    /**
     * Log DELETE operation
     */
    protected function auditDelete(string $entityType, int $entityId, array $oldValues): int
    {
        return $this->auditService->logDelete($entityType, $entityId, $oldValues);
    }

    /**
     * Log generic action
     */
    protected function auditLog(string $action, string $entityType, ?int $entityId = null, array $oldValues = [], array $newValues = []): int
    {
        return $this->auditService->log('AUDIT', $entityType, $entityId, $oldValues, $newValues);
    }
}
