<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MinuteurClient
{
    public const PORT = 22507;

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fetchProjects(array $filters): array
    {
        $response = Http::get("{$this->getBaseUrl()}/projects", $filters);
        $response->throw();

        return $response->json();
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
    public function stopTimer(string $projectUuid, ?string $sessionName): void
    {
        $response = Http::post(sprintf('%s/projects/%s/session/running/stop', $this->getBaseUrl(), $projectUuid), [
            'name' => $sessionName,
        ]);

        $response->throw();
    }

    /**
     * Get the a list grouped by date for each project .
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @return ProjectDailySummary[]
     */
    public function summaryFromProjects(): array
    {
        $response = Http::get(sprintf('%s/projects/summary/daily', $this->getBaseUrl()));
        $response->throw();

        return array_map(function ($item) {
            return new ProjectDailySummary($item['uuid'], $item['name'], $item['time'], $item['date'], $item['notes']);
        }, $response->json());
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function deleteSessionsFromProject($projectUuid): void
    {
        $response = Http::delete(sprintf('%s/projects/%s/sessions/clear', $this->getBaseUrl(), $projectUuid));
        $response->throw();
    }

    protected function getBaseUrl(): string
    {
        return sprintf('http://localhost:%s/api', self::PORT);
    }
}
