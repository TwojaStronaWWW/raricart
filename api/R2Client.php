<?php
declare(strict_types=1);

namespace Raricart\Api;

/**
 * Cloudflare R2 Client via AWS S3 SigV4 (Native PHP, zero external dependencies).
 */
class R2Client
{
    private string $accountId;
    private string $accessKeyId;
    private string $secretAccessKey;
    private string $bucketName;
    private string $region;
    private ?string $lastError = null;
    private ?int $lastStatusCode = null;

    public function __construct(
        string $accountId,
        string $accessKeyId,
        string $secretAccessKey,
        string $bucketName,
        string $region = 'auto'
    ) {
        $this->accountId = trim($accountId);
        $this->accessKeyId = trim($accessKeyId);
        $this->secretAccessKey = trim($secretAccessKey);
        $this->bucketName = trim($bucketName);
        $this->region = trim($region) !== '' ? trim($region) : 'auto';
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getLastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }

    /**
     * Uploads an object to Cloudflare R2.
     *
     * @param string $key Object key/path in bucket
     * @param string $content Raw binary or text payload
     * @param string $contentType MIME type (default: application/octet-stream)
     * @return bool True if upload succeeded (HTTP 200..299)
     */
    public function putObject(string $key, string $content, string $contentType = 'application/octet-stream'): bool
    {
        $this->lastError = null;
        $this->lastStatusCode = null;

        $key = ltrim($key, '/');
        $host = "{$this->accountId}.r2.cloudflarestorage.com";
        $uri = '/' . rawurlencode($this->bucketName) . '/' . $this->encodeKey($key);

        $response = $this->sendRequest('PUT', $host, $uri, $content, $contentType);

        return $response !== false;
    }

    /**
     * Retrieves an object from Cloudflare R2.
     *
     * @param string $key Object key/path
     * @return string|null Object body or null on failure
     */
    public function getObject(string $key): ?string
    {
        $this->lastError = null;
        $this->lastStatusCode = null;

        $key = ltrim($key, '/');
        $host = "{$this->accountId}.r2.cloudflarestorage.com";
        $uri = '/' . rawurlencode($this->bucketName) . '/' . $this->encodeKey($key);

        $response = $this->sendRequest('GET', $host, $uri, '', 'application/octet-stream');

        return $response !== false ? $response : null;
    }

    /**
     * Deletes an object from Cloudflare R2.
     *
     * @param string $key Object key/path
     * @return bool True if deleted or nonexistent
     */
    public function deleteObject(string $key): bool
    {
        $this->lastError = null;
        $this->lastStatusCode = null;

        $key = ltrim($key, '/');
        $host = "{$this->accountId}.r2.cloudflarestorage.com";
        $uri = '/' . rawurlencode($this->bucketName) . '/' . $this->encodeKey($key);

        $response = $this->sendRequest('DELETE', $host, $uri, '', 'application/octet-stream');

        return $response !== false;
    }

    /**
     * Checks if an object exists in Cloudflare R2 (HEAD request).
     *
     * @param string $key Object key/path
     * @return bool
     */
    public function hasObject(string $key): bool
    {
        $this->lastError = null;
        $this->lastStatusCode = null;

        $key = ltrim($key, '/');
        $host = "{$this->accountId}.r2.cloudflarestorage.com";
        $uri = '/' . rawurlencode($this->bucketName) . '/' . $this->encodeKey($key);

        $response = $this->sendRequest('HEAD', $host, $uri, '', 'application/octet-stream');

        return $response !== false;
    }

    /**
     * Encodes key segments individually while preserving directory slashes.
     */
    private function encodeKey(string $key): string
    {
        $segments = explode('/', $key);
        return implode('/', array_map('rawurlencode', $segments));
    }

    /**
     * Signs and dispatches an AWS SigV4 request via cURL.
     */
    private function sendRequest(
        string $method,
        string $host,
        string $uri,
        string $payload,
        string $contentType,
        string $queryString = ''
    ): string|false {
        $now = time();
        $amzDate = gmdate('Ymd\THis\Z', $now);
        $dateStamp = gmdate('Ymd', $now);
        $payloadHash = hash('sha256', $payload);

        $headersToSign = [
            'content-type' => $contentType,
            'host' => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date' => $amzDate,
        ];
        ksort($headersToSign);

        $canonicalHeaders = '';
        $signedHeaderNames = [];
        foreach ($headersToSign as $name => $value) {
            $canonicalHeaders .= strtolower($name) . ':' . trim((string)$value) . "\n";
            $signedHeaderNames[] = strtolower($name);
        }
        $signedHeaders = implode(';', $signedHeaderNames);

        $canonicalRequest = implode("\n", [
            $method,
            $uri,
            $queryString,
            $canonicalHeaders,
            $signedHeaders,
            $payloadHash,
        ]);

        $credentialScope = "{$dateStamp}/{$this->region}/s3/aws4_request";
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        // Key derivation
        $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $this->secretAccessKey, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);
        $authHeader = "AWS4-HMAC-SHA256 Credential={$this->accessKeyId}/{$credentialScope}, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $requestHeaders = [
            "Host: {$host}",
            "Content-Type: {$contentType}",
            "x-amz-date: {$amzDate}",
            "x-amz-content-sha256: {$payloadHash}",
            "Authorization: {$authHeader}",
        ];

        $url = "https://{$host}{$uri}" . ($queryString !== '' ? "?{$queryString}" : '');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if ($method === 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        } elseif ($method === 'PUT' || $method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->lastStatusCode = $statusCode;

        if ($errno !== 0) {
            $this->lastError = "cURL error ({$errno}): {$error}";
            return false;
        }

        if ($statusCode >= 200 && $statusCode < 300) {
            return is_string($response) ? $response : '';
        }

        $this->lastError = "HTTP {$statusCode}: " . (is_string($response) ? trim($response) : 'No response body');
        return false;
    }
}
