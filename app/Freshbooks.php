<?php

namespace App;

use Exception;
use App\ProjectDailySummary;
use Freshbooks\FreshBooksApi;

class Freshbooks
{
    /**
     * @var \Freshbooks\FreshBooksApi
     */
	protected $freshbooks;

    /**
     * @var array
     */
	protected $projects = [];

    /**
     * @var array
     */
	protected $staff;

    /**
     * @var array
     */
	public $errors = [];

    public function __construct($subdomain, $apiToken)
    {
        $this->freshbooks = new FreshBooksApi($subdomain, $apiToken);

        $this->fetchProjects();
        $this->fetchTasks();
        $this->fetchStaff();
    }

    /**
     * Fetch all the freshbook projects.
     */
    public function fetchProjects(): void
    {
        $this->freshbooks->setMethod('project.list');
        $this->freshbooks->post(['per_page' => 100]);
        $this->freshbooks->request();

        if (! $this->freshbooks->success()) {
            throw new Exception(
                sprintf('Error fetching freshbooks projects. Error: %s', $this->freshbooks->getError())
            );
        }

        $projects = $this->freshbooks->getResponse()['projects']['project'];

        foreach ($projects as $project) {
            $this->projects[$project['project_id']] = $project['name'];
        }
    }

    /**
     * Fetch the freshbook tasks.
     */
    public function fetchTasks(): void
    {
        $this->freshbooks->setMethod('task.list');
        $this->freshbooks->post(['per_page' => 100]);
        $this->freshbooks->request();
        $tasks = $this->freshbooks->getResponse()['tasks']['task'];

        foreach ($tasks as $task) {
            $this->tasks[$task['task_id']] = $task['name'];
        }
    }

    /**
     * Fetch the staff informations.
     */
    public function fetchStaff(): void
    {
        $this->freshbooks->setMethod('staff.list');
        $this->freshbooks->post(['per_page' => 100]);
        $this->freshbooks->request();
        $staff = $this->freshbooks->getResponse()['staff_members'];
        $this->staff = $staff['member']['staff_id'] ?? 1;
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
        $payload = [
            'time_entry' => [
                'project_id' => $this->getProjectId($summary->getProjectName()),
                'task_id' => $this->getGeneralTaskId(),
                'staff_id' => $this->getStaffId(),
                'hours' => $summary->getTimeInDecimalHours(),
                'notes' => $summary->getNotesFormated(),
                'date' => $summary->getDate()->format('Y-m-d'),
            ],
        ];

        $this->freshbooks->setMethod('time_entry.create');
        $this->freshbooks->post($payload);
        $this->freshbooks->request();

        if ($this->freshbooks->getResponse()['@attributes']['status'] === 'ok') {
            $summary->deleteSessionsFromProject();
        } else {
            $this->errors[] = sprintf(
                'Error when posting hours for project %s (Check if project names are matching). Total Hours: %s. Error message: %s.',
                $summary->getProjectName(),
                $summary->getTimeInDecimalHours(),
                $this->freshbooks->getResponse()['error'] ?? ''
            );
        }
    }

    protected function getStaffId()
    {
        return $this->staff;
    }

    protected function getGeneralTaskId()
    {
        return array_search('General', $this->tasks);
    }

    protected function getProjectId($project)
    {
        return array_search($project, $this->projects);
    }
}
