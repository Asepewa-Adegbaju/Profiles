<?php

declare(strict_types=1);

namespace App\Core;

class AuditLog
{
    public static function log(
        string $action,
        string $tableName,
        ?int   $recordId,
        string $description
    ): void {
        try {
            Database::getInstance()->query(
                'INSERT INTO audit_log
                    (user_id, action, table_name, record_id, description, ip_address, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())',
                [
                    Session::get('user_id'),
                    $action,
                    $tableName,
                    $recordId,
                    $description,
                    $_SERVER['REMOTE_ADDR'] ?? 'onbekend',
                ]
            );
        } catch (\Throwable $e) {
            error_log('[AuditLog] Fout: ' . $e->getMessage());
        }
    }
}
