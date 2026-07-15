<?php

namespace App\Core;

final class Request
{
    private array $query;      
    private array $body;       
    private array $files;      
    private string $method;
    
    private array $routeParams = [];

    public function __construct()
    {
        $this->query  = $_GET;
        $this->files  = $_FILES;
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->body   = $this->parseBody();
    }

    private function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode((string)$raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        
        
        return $_POST;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isGet(): bool    { return $this->method === 'GET'; }
    public function isPost(): bool   { return $this->method === 'POST'; }
    public function isPut(): bool    { return $this->method === 'PUT'; }
    public function isDelete(): bool { return $this->method === 'DELETE'; }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }
}
