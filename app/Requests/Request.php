<?php

namespace App\Requests;

class Request
{
    protected ?array $query;
    protected ?array $post;
    protected ?array $files;
    protected ?array $server;
    protected ?array $headers;
    protected ?array $sessions;
    protected array $body;
    public array $params = [];
    public function __construct() {
        $this->query = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->sessions = $_SESSION;
        $this->headers = getallheaders();
        $this->parseBody();
    }

    public function query(): ?array
    {
        return $this->query();
    }

    public function post(): ?array
    {
        return $this->post;
    }

    public function files(): ?array
    {
        return $this->files;
    }

    public function server(): ?array
    {
        return $this->server;
    }

    public function headers(): ?array
    {
        return $this->headers;
    }

    public function sessions(): ?array
    {
        return $this->sessions;
    }

    public function body(): array
    {
        $this->parseBody();
        return $this->body;
    }

    public function parseBody(): void
    {
        $this->body = (array) json_decode(file_get_contents('php://input'));
    }
}