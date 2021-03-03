<?php

namespace App;

use Carbon\Carbon;

class ProjectDailySummary
{
    /** @var string */
    protected $projectUuid;

    /** @var string */
    protected $projectName;

    /**
     * Time in seconds.
     *
     * @var int
     */
    protected $time;

    /** @var \Carbon\Carbon */
    protected $date;

    /** @var string */
    protected $notes;

    public function __construct($projectUuid, $projectName, $time, $date, $notes)
    {
        $this->projectUuid = $projectUuid;
        $this->projectName = $projectName;
        $this->time = $time;
        $this->date = Carbon::parse($date);
        $this->notes = $notes;
    }

    public function getProjectUuid(): string
    {
        return $this->projectUuid;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getTimeInDecimalHours(): float
    {
        return round($this->time / 60 / 60, 2);
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getNotesFormated(): string
    {
        return str_replace(', ', "\n\n", $this->notes);
    }

    public function deleteSessionsFromProject(): void
    {
        app(MinuteurClient::class)
            ->deleteSessionsFromProject($this->getProjectUuid());
    }

    public function getStartTime(): Carbon
    {
        return $this->getDate()->startOfDay()->addHours(9);
    }

    public function getEndTime(): Carbon
    {
        return $this->getStartTime()->addSeconds($this->time);
    }
}
