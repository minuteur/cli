<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use App\ProjectDailySummary;

class Clockify
{
    /**
     * @var \GuzzleHttp\Client
     */
	protected $client;

    /**
     * @var array
     */
	protected $projects = [];

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $workspaceId;

    /**
     * @var array
     */
	public $errors = [];

    public function __construct($apiKey, $workspaceId)
    {
        $this->client = new Client([
            'headers' => [
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ],
        ]);

        $this->apiKey = $apiKey;
        $this->workspaceId = $workspaceId;
    }

    /**
     * Fetch all the freshbook projects.
     */
    public function fetchProjects(): void
    {
        $response = $this->client->get(sprintf('https://api.clockify.me/api/v1/workspaces/%s/projects', $this->workspaceId));
        $projects = json_decode($response->getBody()->getContents(), true);

        foreach ($projects as $project) {
            $this->projects[$project['id']] = $project['name'];
        }
    }

    /**
     * Post hours to freshbooks.
     */
    public function postHours(array $summary): void
    {
        foreach ($summary as $item) {
            $this->postTimeEntry($item);
        }
    }

    public function postTimeEntry(ProjectDailySummary $summary)
    {
        try {
            $payload = [
                'billable' => true,
                'description' => $summary->getNotesFormated(),
                'projectId' => $this->getProjectId($summary->getProjectName()),
                'task_id' => null,
                'start' => $summary->getStartTime()->toISOString(),
                'end' => $summary->getEndTime()->toISOString(),
            ];

            $response = $this->client->post(sprintf('https://api.clockify.me/api/v1/workspaces/%s/time-entries', $this->workspaceId), [
                'json' => $payload,
            ]);

            $summary->deleteSessionsFromProject();
        } catch (Exception $exception) {
            $this->errors[] = sprintf(
                'Error when posting hours for project %s (Check if project names are matching). Total Hours: %s. Error message: %s.',
                $summary->getProjectName(),
                $summary->getTimeInDecimalHours(),
                $exception->getMessage()
            );
        }
    }

    protected function getProjectId(string $projectName): string
    {
        $response = $this->client->get(
            sprintf(
                'https://api.clockify.me/api/v1/workspaces/%s/projects?name=%s',
                $this->workspaceId,
                $projectName
            )
        );
        $projects = json_decode($response->getBody()->getContents(), true);

        if (count($projects) === 0) {
            throw new Exception('Project not found');
        }

        return (string) $projects[0]['id'];
    }
}
