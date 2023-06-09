<?php

namespace App\Responses;

class Response
{
    protected int $status = 200;
    protected array $headers = [
        'Content-Type' => 'text/html'
    ];
    protected mixed $body = null;

    public function setStatusCode($status): void
    {
        $this->status = $status;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function setHeader($header, $value): void
    {
        $this->headers[$header] = $value;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setBody(mixed $body): void
    {
        $this->body = $body;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function redirectBack(): void
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            echo "<script>window.history.back();</script>";
        }
        exit;
    }

    public function redirect($path): void
    {
        header("Location: $path");
        exit;
    }
}