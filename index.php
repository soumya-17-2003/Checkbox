<?php
// index.php

// Subjects and times array
$subjects = [
    ['name' => 'Web3', 'time' => '10-11AM'],
    ['name' => 'Full Stack', 'time' => '11-12PM'],
    ['name' => 'Java', 'time' => '12-1PM'],
    ['name' => 'C++', 'time' => '1-2PM'],
    ['name' => 'DSA/DAA', 'time' => '6-7PM'],
    ['name' => 'Aptitude', 'time' => '7-8PM'],
    ['name' => 'Comp. Arch', 'time' => '8-9PM'],
    ['name' => 'Networking', 'time' => '9-10PM'],
    ['name' => 'Soft. Eng', 'time' => '10-11PM'],
    ['name' => 'OS', 'time' => '11PM-12AM'],
];

// Handle AJAX toggle request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle'])) {
    session_start();
    $index = intval($_POST['index']);
    if (!isset($_SESSION['done'])) $_SESSION['done'] = array_fill(0, count($subjects), false);
    $_SESSION['done'][$index] = !$_SESSION['done'][$index];
    echo json_encode(['done' => $_SESSION['done'][$index]]);
    exit;
}

// Handle AJAX notes save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    session_start();
    $index = intval($_POST['index']);
    $note = trim($_POST['note']);
    if (!isset($_SESSION['notes'])) $_SESSION['notes'] = array_fill(0, count($subjects), '');
    $_SESSION['notes'][$index] = $note;
    echo json_encode(['note' => $_SESSION['notes'][$index]]);
    exit;
}

// On page load, get done status and notes from session
session_start();
if (!isset($_SESSION['done'])) {
    $_SESSION['done'] = array_fill(0, count($subjects), false);
}
if (!isset($_SESSION['notes'])) {
    $_SESSION['notes'] = array_fill(0, count($subjects), '');
}
$done = $_SESSION['done'];
$notes = $_SESSION['notes'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Progress Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background: #f3f4f6; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0002; padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .datetime { font-size: 1.1em; color: #4f46e5; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 10px 8px; text-align: left; }
        th { background: #6366f1; color: #fff; }
        tr:nth-child(even) { background: #f1f5f9; }
        .toggle-btn {
            padding: 6px 18px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .not-done { background: #e5e7eb; color: #374151; }
        .done { background: #4f46e5; color: #fff; }
        .note-section { display: flex; align-items: center; gap: 6px; }
        .note-input { display: none; }
        .note-checkbox:checked ~ .note-input { display: inline-block; }
        .note-text { min-width: 120px; padding: 4px 8px; border-radius: 4px; border: 1px solid #d1d5db; }
        .add-note-btn { padding: 4px 10px; border: none; border-radius: 4px; background: #6366f1; color: #fff; cursor: pointer; }
        .note-display { margin-left: 8px; color: #2563eb; font-size: 0.98em; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Subject Progress Tracker</h2>
        <div class="datetime" id="datetime"></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Time</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($subjects as $i => $subject): ?>
            <tr>
                <td><?= htmlspecialchars($subject['name']) ?></td>
                <td><?= htmlspecialchars($subject['time']) ?></td>
                <td>
                    <button class="toggle-btn <?= $done[$i] ? 'done' : 'not-done' ?>"
                            data-index="<?= $i ?>">
                        <?= $done[$i] ? 'Done' : 'Not Done' ?>
                    </button>
                </td>
                <td>
                    <div class="note-section">
                        <input type="checkbox" class="note-checkbox" id="note-check-<?= $i ?>" style="margin-right:2px;">
                        <label for="note-check-<?= $i ?>" title="Add/View Note"></label>
                        <span class="note-input">
                            <input type="text" class="note-text" value="<?= htmlspecialchars($notes[$i]) ?>" data-index="<?= $i ?>">
                            <button class="add-note-btn" data-index="<?= $i ?>">Add</button>
                        </span>
                        <span class="note-display" id="note-display-<?= $i ?>"><?= htmlspecialchars($notes[$i]) ?></span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
// Real-time date and time
function updateDateTime() {
    const dt = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = dt.toLocaleDateString(undefined, options);
    const timeStr = dt.toLocaleTimeString();
    document.getElementById('datetime').textContent = dateStr + ' ' + timeStr;
}
setInterval(updateDateTime, 1000);
updateDateTime();

// Toggle button AJAX
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const index = this.getAttribute('data-index');
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'toggle=1&index=' + encodeURIComponent(index)
        })
        .then(res => res.json())
        .then(data => {
            if (data.done) {
                this.classList.remove('not-done');
                this.classList.add('done');
                this.textContent = 'Done';
            } else {
                this.classList.remove('done');
                this.classList.add('not-done');
                this.textContent = 'Not Done';
            }
        });
    });
});

// Notes section logic
document.querySelectorAll('.note-checkbox').forEach((checkbox, i) => {
    const noteInput = checkbox.parentElement.querySelector('.note-input');
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            noteInput.style.display = 'inline-block';
        } else {
            noteInput.style.display = 'none';
        }
    });
    // Hide input by default
    noteInput.style.display = 'none';
});

document.querySelectorAll('.add-note-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const index = this.getAttribute('data-index');
        const input = this.parentElement.querySelector('.note-text');
        const note = input.value;
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'save_note=1&index=' + encodeURIComponent(index) + '&note=' + encodeURIComponent(note)
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('note-display-' + index).textContent = data.note;
        });
    });
});
</script>
</body>
</html>
