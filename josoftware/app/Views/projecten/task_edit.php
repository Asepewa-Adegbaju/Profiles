<?php use App\Core\CSRF; ?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Taak bewerken</h2>
    <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" class="btn btn-secondary btn-sm">← Terug naar project</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title text-muted text-sm">Project: <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/taak/<?= (int)$task['id'] ?>/edit">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="title">Titel <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-input"
                    maxlength="300"
                    required
                    autofocus
                    value="<?= htmlspecialchars($_POST['title'] ?? $task['title'], ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <?php
                        $selectedStatus = $_POST['status'] ?? $task['status'];
                        $statusOptions  = [
                            'te-doen' => 'Te doen',
                            'bezig'   => 'Bezig',
                            'review'  => 'Review',
                            'klaar'   => 'Klaar',
                        ];
                        foreach ($statusOptions as $val => $lbl):
                        ?>
                            <option value="<?= $val ?>" <?= $selectedStatus === $val ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="priority">Prioriteit</label>
                    <select id="priority" name="priority" class="form-select">
                        <?php
                        $selectedPriority = $_POST['priority'] ?? $task['priority'];
                        $priorityOptions  = [
                            'laag'    => 'Laag',
                            'normaal' => 'Normaal',
                            'hoog'    => 'Hoog',
                            'urgent'  => 'Urgent',
                        ];
                        foreach ($priorityOptions as $val => $lbl):
                        ?>
                            <option value="<?= $val ?>" <?= $selectedPriority === $val ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="assigned_to">Toegewezen aan</label>
                    <select id="assigned_to" name="assigned_to" class="form-select">
                        <option value="">— Niemand —</option>
                        <?php
                        $selectedUser = $_POST['assigned_to'] ?? $task['assigned_to'];
                        foreach ($users as $user):
                        ?>
                            <option value="<?= (int)$user['id'] ?>"
                                <?= ($selectedUser == $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="due_date">Deadline</label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['due_date'] ?? $task['due_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Omschrijving</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-textarea"
                    rows="4"
                ><?= htmlspecialchars($_POST['description'] ?? $task['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
