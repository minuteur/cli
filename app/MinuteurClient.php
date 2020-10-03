<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MinuteurClient
{
    public function fetchProjects(array $filters): array
    {
        $response = Http::get("{$this->getBaseUrl()}/projects", $filters);
        $projects = $response->json();

        return $projects;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function startTimer(string $projectUuid): void
    {
        $response = Http::post(sprintf('%s/projects/%s/sessions', $this->getBaseUrl(), $projectUuid));
        $response->throw();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function stopTimer(string $projectUuid): void
    {
        $response = Http::delete(sprintf('%s/projects/%s/session/running', $this->getBaseUrl(), $projectUuid));
        $response->throw();
    }

    protected function getBaseUrl(): string
    {
        return 'http://localhost:22507/api';
    }
}
