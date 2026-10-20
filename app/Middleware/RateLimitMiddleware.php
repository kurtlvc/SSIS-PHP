<?php

class RateLimitMiddleware
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
    }

    /**
     * Check rate limit for an action
     * Returns [allowed: bool, remaining: int, reset_time: int]
     */
    public function check(string $identifier, string $action, ?int $maxRequests = null, ?int $windowMinutes = null): array
    {
        $settings = $this->config['rate_limit'] ?? [];
        
        $max = $maxRequests ?? ($settings[$action . '_max'] ?? 60);
        $window = $windowMinutes ?? ($settings[$action . '_window'] ?? 1); // minutes
        
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$window} minutes"));
        $windowStart = date('Y-m-d H:i:s', strtotime("-{$window} minutes"));

        // Clean expired entries
        $this->cleanExpired();

        // Check current count
        $sql = "SELECT count FROM rate_limits 
                WHERE identifier = :identifier AND action = :action 
                AND expires_at > NOW()";
        $row = $this->db->fetchOne($sql, [
            'identifier' => $identifier,
            'action' => $action
        ]);

        $currentCount = $row ? (int)$row['count'] : 0;

        if ($currentCount >= $max) {
            // Get reset time
            $resetSql = "SELECT expires_at FROM rate_limits 
                         WHERE identifier = :identifier AND action = :action 
                         AND expires_at > NOW() LIMIT 1";
            $resetRow = $this->db->fetchOne($resetSql, [
                'identifier' => $identifier,
                'action' => $action
            ]);
            
            $resetTime = $resetRow ? strtotime($resetRow['expires_at']) : time() + ($window * 60);
            
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset_time' => $resetTime,
                'limit' => $max,
                'window' => $window
            ];
        }

        // Increment or insert
        if ($row) {
            $this->db->update('rate_limits', [
                'count' => $currentCount + 1
            ], 'identifier = :identifier AND action = :action AND expires_at > NOW()', [
                'identifier' => $identifier,
                'action' => $action
            ]);
        } else {
            $this->db->insert('rate_limits', [
                'identifier' => $identifier,
                'action' => $action,
                'count' => 1,
                'window_start' => date('Y-m-d H:i:s'),
                'expires_at' => $expiresAt
            ]);
        }

        return [
            'allowed' => true,
            'remaining' => $max - $currentCount - 1,
            'reset_time' => strtotime($expiresAt),
            'limit' => $max,
            'window' => $window
        ];
    }

    /**
     * Middleware for login rate limiting
     */
    public function loginLimit(string $identifier): bool
    {
        $result = $this->check($identifier, 'login', 10, 15); // 10 attempts per 15 min
        
        if (!$result['allowed']) {
            $resetIn = ceil(($result['reset_time'] - time()) / 60);
            App::flash('error', "Too many login attempts. Please try again in {$resetIn} minutes.");
            App::redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Middleware for API rate limiting
     */
    public function apiLimit(string $identifier): bool
    {
        $result = $this->check($identifier, 'api', 60, 1); // 60 requests per minute
        
        if (!$result['allowed']) {
            header('X-RateLimit-Limit: ' . $result['limit']);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . $result['reset_time']);
            http_response_code(429);
            echo json_encode([
                'error' => 'Rate limit exceeded',
                'retry_after' => $result['reset_time'] - time()
            ]);
            exit;
        }
        
        // Add rate limit headers
        header('X-RateLimit-Limit: ' . $result['limit']);
        header('X-RateLimit-Remaining: ' . $result['remaining']);
        header('X-RateLimit-Reset: ' . $result['reset_time']);
        
        return true;
    }

    /**
     * Middleware for password reset rate limiting
     */
    public function passwordResetLimit(string $identifier): bool
    {
        $result = $this->check($identifier, 'password_reset', 3, 60); // 3 per hour
        
        if (!$result['allowed']) {
            $resetIn = ceil(($result['reset_time'] - time()) / 60);
            App::flash('error', "Too many password reset requests. Please try again in {$resetIn} minutes.");
            App::redirect('/forgot-password');
            return false;
        }
        return true;
    }

    /**
     * Get client identifier (IP + User Agent hash for API)
     */
    public function getClientIdentifier(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return hash('sha256', $ip . '|' . $ua);
    }

    /**
     * Get authenticated user identifier
     */
    public function getUserIdentifier(): string
    {
        return 'user_' . ($_SESSION['account_id'] ?? 'anonymous');
    }

    /**
     * Clean expired rate limit entries
     */
    private function cleanExpired(): void
    {
        $this->db->delete('rate_limits', 'expires_at <= NOW()');
    }

    /**
     * Reset rate limit for an identifier/action (admin use)
     */
    public function reset(string $identifier, ?string $action = null): int
    {
        if ($action) {
            return $this->db->delete('rate_limits', 'identifier = :identifier AND action = :action', [
                'identifier' => $identifier,
                'action' => $action
            ]);
        }
        return $this->db->delete('rate_limits', 'identifier = :identifier', [
            'identifier' => $identifier
        ]);
    }

    /**
     * Get rate limit status for display
     */
    public function getStatus(string $identifier, string $action): array
    {
        $sql = "SELECT count, expires_at, window_start FROM rate_limits 
                WHERE identifier = :identifier AND action = :action 
                AND expires_at > NOW()";
        $row = $this->db->fetchOne($sql, [
            'identifier' => $identifier,
            'action' => $action
        ]);

        if (!$row) {
            return [
                'used' => 0,
                'remaining' => $this->config['rate_limit'][$action . '_max'] ?? 60,
                'reset_time' => time() + 60
            ];
        }

        $max = $this->config['rate_limit'][$action . '_max'] ?? 60;
        return [
            'used' => (int)$row['count'],
            'remaining' => max(0, $max - (int)$row['count']),
            'reset_time' => strtotime($row['expires_at']),
            'window_start' => strtotime($row['window_start'])
        ];
    }
}