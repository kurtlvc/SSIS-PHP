<?php

class AnnouncementService
{
    private $db;
    private $emailService;
    private $notificationService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->emailService = new EmailService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Get all announcements
     */
    public function getAll(bool $onlyActive = true): array
    {
        $where = $onlyActive ? 'WHERE is_active = 1' : 'WHERE 1=1';
        $sql = "SELECT ann_id, ann_title, ann_body, p_id, ann_status, ann_index, is_active
                FROM announcements
                {$where}
                ORDER BY ann_index ASC, ann_id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get announcement by ID
     */
    public function getById(int $annId): ?array
    {
        $sql = "SELECT ann_id, ann_title, ann_body, p_id, ann_status, ann_index, is_active
                FROM announcements
                WHERE ann_id = :id";
        return $this->db->fetchOne($sql, ['id' => $annId]);
    }

    /**
     * Create announcement
     */
    public function create(array $data): int
    {
        $insertData = [
            'ann_title' => $data['ann_title'] ?? null,
            'ann_body' => $data['ann_body'] ?? '',
            'p_id' => $data['p_id'] ?? 0,
            'ann_status' => (int) ($data['ann_status'] ?? 1),
            'ann_index' => (int) ($data['ann_index'] ?? 0),
            'is_active' => (int) ($data['is_active'] ?? 1)
        ];

        return $this->db->insert('announcements', $insertData);
    }

    /**
     * Update announcement
     */
    public function update(int $annId, array $data): int
    {
        $updateData = [];
        $allowed = ['ann_title', 'ann_body', 'ann_status', 'ann_index', 'is_active'];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return 0;
        }

        return $this->db->update('announcements', $updateData, 'ann_id = :id', ['id' => $annId]);
    }

    /**
     * Delete announcement
     */
    public function delete(int $annId): int
    {
        return $this->db->delete('announcements', 'ann_id = :id', ['id' => $annId]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $annId): int
    {
        $current = $this->getById($annId);
        if (!$current) return 0;

        $newStatus = $current['is_active'] ? 0 : 1;
        return $this->update($annId, ['is_active' => $newStatus]);
    }

    /**
     * Get active announcements for display
     */
    public function getActiveForDisplay(): array
    {
        $sql = "SELECT ann_id, ann_title, ann_body, ann_index
                FROM announcements
                WHERE is_active = 1 AND ann_status = 1
                ORDER BY ann_index ASC, ann_id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Create announcement and notify users
     */
    public function createAndNotify(array $data): int
    {
        $annId = $this->create($data);
        
        // Notify all active users
        $this->notificationService->systemBroadcast(
            $data['ann_title'],
            $data['ann_body'],
            'announcement'
        );

        return $annId;
    }
}