<?php
declare(strict_types=1);

namespace Raricart\Api;

require_once __DIR__ . '/R2Client.php';

/**
 * Service for managing private, authenticated-only storage in Cloudflare R2.
 * Used for lead backups, sensitive data, and system snapshots.
 * Zero public internet access.
 */
class PrivateStorageService
{
    private ?R2Client $client = null;
    private string $bucketName;

    public function __construct(?string $bucketName = null)
    {
        $secretsFile = dirname(__DIR__) . '/config/secrets.php';
        if (file_exists($secretsFile)) {
            require_once $secretsFile;
        }

        $accountId = defined('R2_ACCOUNT_ID') ? (string)R2_ACCOUNT_ID : '';
        $accessKey = defined('R2_ACCESS_KEY_ID') ? (string)R2_ACCESS_KEY_ID : '';
        $secretKey = defined('R2_SECRET_ACCESS_KEY') ? (string)R2_SECRET_ACCESS_KEY : '';
        $this->bucketName = $bucketName ?? (defined('R2_PRIVATE_BUCKET_NAME') ? (string)R2_PRIVATE_BUCKET_NAME : 'raricart-private');

        if ($accountId !== '' && $accessKey !== '' && $secretKey !== '' && $this->bucketName !== '') {
            $this->client = new R2Client($accountId, $accessKey, $secretKey, $this->bucketName, 'auto');
        }
    }

    public function isAvailable(): bool
    {
        return $this->client !== null;
    }

    /**
     * Backs up the leads CSV file to the private R2 bucket.
     * Stores both a "latest" snapshot and an optional dated archive.
     */
    public function backupLeads(string $localCsvPath): bool
    {
        if (!$this->isAvailable() || !file_exists($localCsvPath)) {
            return false;
        }

        $fp = @fopen($localCsvPath, 'rb');
        if (!$fp) {
            return false;
        }

        $content = '';
        if (flock($fp, LOCK_SH)) {
            $content = (string)stream_get_contents($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        if ($content === '') {
            return false;
        }

        // 1. Zapis kopii bieżącej (Master copy)
        $uploadedMaster = $this->client->putObject('leads/leady.csv', $content, 'text/csv; charset=UTF-8');

        // 2. Zapis kopii dziennej (Versioning archive)
        $dateKey = 'leads/archive/leady_' . date('Y-m-d') . '.csv';
        $this->client->putObject($dateKey, $content, 'text/csv; charset=UTF-8');

        return $uploadedMaster;
    }

    /**
     * Retrieves the latest master copy of leads CSV from the private R2 bucket.
     */
    public function fetchLatestLeads(): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }

        return $this->client->getObject('leads/leady.csv');
    }

    /**
     * Restores or initializes local CSV from private R2 if local file does not exist or is empty.
     */
    public function restoreLeadsIfMissing(string $localCsvPath): bool
    {
        if (file_exists($localCsvPath) && filesize($localCsvPath) > 10) {
            return true; // Already exists with content
        }

        $remoteContent = $this->fetchLatestLeads();
        if ($remoteContent === null || trim($remoteContent) === '') {
            return false;
        }

        $dir = dirname($localCsvPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $tmpPath = $localCsvPath . '.tmp.' . bin2hex(random_bytes(6));
        $fp = @fopen($tmpPath, 'wb');
        if (!$fp) {
            return false;
        }

        fwrite($fp, $remoteContent);
        fclose($fp);

        return rename($tmpPath, $localCsvPath);
    }

    /**
     * Generic put object to private storage.
     */
    public function putPrivate(string $key, string $content, string $contentType = 'application/octet-stream'): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        return $this->client->putObject($key, $content, $contentType);
    }

    /**
     * Generic get object from private storage.
     */
    public function getPrivate(string $key): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }
        return $this->client->getObject($key);
    }
}
