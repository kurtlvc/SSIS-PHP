<?php

class AuditService
{
    private $db;
    private $currentUserId;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->currentUserId = $_SESSION['account_id'] ?? null;
    }

    /**
     * Log a CRUD operation
     */
    public function log(string $action, string $entityType, ?int $entityId = null, array $oldValues = [], array $newValues = []): int
    {
        $meta = [
            'old' => !empty($oldValues) ? $oldValues : null,
            'new' => !empty($newValues) ? $newValues : null,
            'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ];

        $data = [
            'actor_id' => $this->currentUserId,
            'action' => $action, // CREATE, READ, UPDATE, DELETE, LOGIN, LOGOUT, etc.
            'entity' => $entityType, // students, enrolments, payments, accounts, etc.
            'entity_id' => $entityId !== null ? (string)$entityId : '',
            'meta' => json_encode($meta),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('audit_log', $data);
    }

    /**
     * Log CREATE operation
     */
    public function logCreate(string $entityType, int $entityId, array $newValues): int
    {
        return $this->log('CREATE', $entityType, $entityId, [], $newValues);
    }

    /**
     * Log UPDATE operation
     */
    public function logUpdate(string $entityType, int $entityId, array $oldValues, array $newValues): int
    {
        return $this->log('UPDATE', $entityType, $entityId, $oldValues, $newValues);
    }

    /**
     * Log DELETE operation
     */
    public function logDelete(string $entityType, int $entityId, array $oldValues): int
    {
        return $this->log('DELETE', $entityType, $entityId, $oldValues, []);
    }

    /**
     * Log LOGIN/LOGOUT
     */
    public function logAuth(string $action): int
    {
        return $this->log($action, 'auth', $this->currentUserId, [], []);
    }

    /**
     * Get audit logs with filters
     */
    public function getLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['actor_id'])) {
            $where .= " AND al.actor_id = :actor_id";
            $params['actor_id'] = $filters['actor_id'];
        }

        if (!empty($filters['action'])) {
            $where .= " AND al.action = :action";
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['entity_type'])) {
            $where .= " AND al.entity = :entity_type";
            $params['entity_type'] = $filters['entity_type'];
        }

        if (!empty($filters['entity_id'])) {
            $where .= " AND al.entity_id = :entity_id";
            $params['entity_id'] = $filters['entity_id'];
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND al.created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND al.created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $where .= " ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $sql = "
            SELECT al.*, a.login as actor_login, a.role as actor_role
            FROM audit_log al
            LEFT JOIN accounts a ON al.actor_id = a.id
            $where
        ";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get audit log count
     */
    public function getLogCount(array $filters = []): int
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['actor_id'])) {
            $where .= " AND actor_id = :actor_id";
            $params['actor_id'] = $filters['actor_id'];
        }

        if (!empty($filters['action'])) {
            $where .= " AND action = :action";
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['entity_type'])) {
            $where .= " AND entity = :entity_type";
            $params['entity_type'] = $filters['entity_type'];
        }

        if (!empty($filters['entity_id'])) {
            $where .= " AND entity_id = :entity_id";
            $params['entity_id'] = $filters['entity_id'];
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql = "SELECT COUNT(*) as count FROM audit_log $where";
        $row = $this->db->fetchOne($sql, $params);
        return $row ? (int)$row['count'] : 0;
    }
}