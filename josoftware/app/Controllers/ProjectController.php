<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\AuditLog;
use App\Models\Project;
use App\Models\Task;

class ProjectController
{
    // ── Projecten ────────────────────────────────────────────────────────────

    public function index(): void
    {
        Auth::require();

        $statusFilter = $_GET['status'] ?? '';
        $allowedStatuses = ['actief', 'on-hold', 'afgerond', 'geannuleerd'];
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = '';
        }

        $projects     = Project::all($statusFilter);
        $statusCounts = Project::countByStatus();

        $title = 'Projecten';
        $view  = 'projecten/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function create(): void
    {
        Auth::require();

        $companies = Project::getCompanies();

        $title = 'Nieuw project';
        $view  = 'projecten/create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            Session::flash('error', 'Projectnaam is verplicht.');
            header('Location: ' . APP_URL . '/projecten/nieuw');
            exit;
        }

        $id = Project::create([
            'company_id'  => ($_POST['company_id'] ?? '') !== '' ? (int) $_POST['company_id'] : null,
            'name'        => $name,
            'description' => trim($_POST['description'] ?? '') ?: null,
            'status'      => $_POST['status'] ?? 'actief',
            'start_date'  => trim($_POST['start_date'] ?? ''),
            'end_date'    => trim($_POST['end_date'] ?? ''),
            'budget'      => trim($_POST['budget'] ?? '') !== '' ? $_POST['budget'] : null,
            'created_by'  => Auth::id(),
        ]);

        AuditLog::log('create', 'projects', $id, 'Project aangemaakt: ' . $name);
        Session::flash('success', 'Project "' . $name . '" is aangemaakt.');
        header('Location: ' . APP_URL . '/projecten');
        exit;
    }

    public function show(string $id): void
    {
        Auth::require();

        $project = Project::find((int) $id);
        if ($project === null) {
            http_response_code(404);
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $tasks      = Task::allByProject((int) $id);
        $taskCounts = Task::countByProjectAndStatus((int) $id);
        $users      = Task::getUsers();

        // Verdeel taken per status
        $tasksByStatus = [
            'te-doen' => [],
            'bezig'   => [],
            'review'  => [],
            'klaar'   => [],
        ];
        foreach ($tasks as $task) {
            $tasksByStatus[$task['status']][] = $task;
        }

        $title = htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8');
        $view  = 'projecten/show';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function edit(string $id): void
    {
        Auth::require();

        $project = Project::find((int) $id);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $companies = Project::getCompanies();

        $title = 'Project bewerken';
        $view  = 'projecten/edit';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function update(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $project = Project::find((int) $id);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            Session::flash('error', 'Projectnaam is verplicht.');
            header('Location: ' . APP_URL . '/projecten/' . $id . '/edit');
            exit;
        }

        Project::update((int) $id, [
            'company_id'  => ($_POST['company_id'] ?? '') !== '' ? (int) $_POST['company_id'] : null,
            'name'        => $name,
            'description' => trim($_POST['description'] ?? '') ?: null,
            'status'      => $_POST['status'] ?? 'actief',
            'start_date'  => trim($_POST['start_date'] ?? ''),
            'end_date'    => trim($_POST['end_date'] ?? ''),
            'budget'      => trim($_POST['budget'] ?? '') !== '' ? $_POST['budget'] : null,
        ]);

        AuditLog::log('update', 'projects', (int) $id, 'Project bijgewerkt: ' . $name);
        Session::flash('success', 'Project "' . $name . '" is bijgewerkt.');
        header('Location: ' . APP_URL . '/projecten/' . $id);
        exit;
    }

    public function delete(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $project = Project::find((int) $id);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        Project::delete((int) $id);
        AuditLog::log('delete', 'projects', (int) $id, 'Project verwijderd: ' . $project['name']);
        Session::flash('success', 'Project "' . $project['name'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/projecten');
        exit;
    }

    // ── Taken ────────────────────────────────────────────────────────────────

    public function storeTask(string $projectId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $project = Project::find((int) $projectId);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            Session::flash('error', 'Taaknaam is verplicht.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        $taskId = Task::create([
            'project_id'  => (int) $projectId,
            'assigned_to' => ($_POST['assigned_to'] ?? '') !== '' ? (int) $_POST['assigned_to'] : null,
            'title'       => $title,
            'description' => trim($_POST['description'] ?? '') ?: null,
            'status'      => 'te-doen',
            'priority'    => $_POST['priority'] ?? 'normaal',
            'due_date'    => trim($_POST['due_date'] ?? ''),
        ]);

        AuditLog::log('create', 'tasks', $taskId, 'Taak aangemaakt: ' . $title . ' (project ' . $projectId . ')');
        Session::flash('success', 'Taak "' . $title . '" is toegevoegd.');
        header('Location: ' . APP_URL . '/projecten/' . $projectId);
        exit;
    }

    public function editTask(string $projectId, string $taskId): void
    {
        Auth::require();

        $project = Project::find((int) $projectId);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $task = Task::find((int) $taskId);
        if ($task === null || (int) $task['project_id'] !== (int) $projectId) {
            Session::flash('error', 'Taak niet gevonden.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        $users = Task::getUsers();

        $title = 'Taak bewerken';
        $view  = 'projecten/task_edit';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function updateTask(string $projectId, string $taskId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $project = Project::find((int) $projectId);
        if ($project === null) {
            Session::flash('error', 'Project niet gevonden.');
            header('Location: ' . APP_URL . '/projecten');
            exit;
        }

        $task = Task::find((int) $taskId);
        if ($task === null || (int) $task['project_id'] !== (int) $projectId) {
            Session::flash('error', 'Taak niet gevonden.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        $taskTitle = trim($_POST['title'] ?? '');
        if ($taskTitle === '') {
            Session::flash('error', 'Taaknaam is verplicht.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId . '/taak/' . $taskId . '/edit');
            exit;
        }

        Task::update((int) $taskId, [
            'assigned_to' => ($_POST['assigned_to'] ?? '') !== '' ? (int) $_POST['assigned_to'] : null,
            'title'       => $taskTitle,
            'description' => trim($_POST['description'] ?? '') ?: null,
            'status'      => $_POST['status'] ?? 'te-doen',
            'priority'    => $_POST['priority'] ?? 'normaal',
            'due_date'    => trim($_POST['due_date'] ?? ''),
        ]);

        AuditLog::log('update', 'tasks', (int) $taskId, 'Taak bijgewerkt: ' . $taskTitle);
        Session::flash('success', 'Taak "' . $taskTitle . '" is bijgewerkt.');
        header('Location: ' . APP_URL . '/projecten/' . $projectId);
        exit;
    }

    public function updateTaskStatus(string $projectId, string $taskId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $task = Task::find((int) $taskId);
        if ($task === null || (int) $task['project_id'] !== (int) $projectId) {
            Session::flash('error', 'Taak niet gevonden.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        $allowedStatuses = ['te-doen', 'bezig', 'review', 'klaar'];
        $status = $_POST['status'] ?? '';
        if (!in_array($status, $allowedStatuses, true)) {
            Session::flash('error', 'Ongeldig status.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        Task::updateStatus((int) $taskId, $status);
        AuditLog::log('update', 'tasks', (int) $taskId, 'Taakstatus bijgewerkt naar: ' . $status);

        header('Location: ' . APP_URL . '/projecten/' . $projectId);
        exit;
    }

    public function deleteTask(string $projectId, string $taskId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $task = Task::find((int) $taskId);
        if ($task === null || (int) $task['project_id'] !== (int) $projectId) {
            Session::flash('error', 'Taak niet gevonden.');
            header('Location: ' . APP_URL . '/projecten/' . $projectId);
            exit;
        }

        Task::delete((int) $taskId);
        AuditLog::log('delete', 'tasks', (int) $taskId, 'Taak verwijderd: ' . $task['title']);
        Session::flash('success', 'Taak "' . $task['title'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/projecten/' . $projectId);
        exit;
    }
}
