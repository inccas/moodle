<?php
require_once(__DIR__ . '/../config.php');
// Konfiguration aus Moodle-Config �bernehmen
$config = [
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname
];

// Verbindung zur Datenbank herstellen
$conn = new mysqli($config['host'], $config['user'], $config['password'], $config['dbname']);

// UTF-8 Zeichensatz f�r Verbindung setzen
$conn->set_charset("utf8mb4");

// �berpr�fen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindungsfehler: " . $conn->connect_error);
}

// Tabellen, die bearbeitet werden sollen
$tables = [
    'mdl_qtype_stack_options',
    'mdl_qtype_stack_inputs',
    'mdl_qtype_stack_qtest_inputs',
    'mdl_qtype_stack_prts',
    'mdl_qtype_stack_prt_nodes'
];

// Verarbeitung des Formulars
$questionid_alt = '';
$questionid_neu = '';
$dryRun = true;
$message = '';
$tableData = [];
$existingData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verarbeitung des Formulars bei Submit
    if (isset($_POST['submit'])) {
        $questionid_alt = $_POST['questionid_alt'];
        $dryRun = isset($_POST['dryrun']) && $_POST['dryrun'] == 'true';

        // �berpr�fen, ob questionid_alt g�ltig ist
        $query = "SELECT * FROM mdl_question WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $questionid_alt);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $questionData = $result->fetch_assoc();
            $questionName = $questionData['name'];

            // Suche nach allen Eintr�gen mit dem gleichen Namen und parent = 0
            $query = "SELECT * FROM mdl_question WHERE name = ? AND parent = 0 ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $questionName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $newestQuestion = $result->fetch_assoc();
                $questionid_neu = $newestQuestion['id'];

                // Daten f�r jede Tabelle abrufen und pr�fen, ob bereits Eintr�ge existieren
                foreach ($tables as $table) {
                    // Daten aus der Quelltabelle abrufen (questionid_neu)
                    $query = "SELECT * FROM $table WHERE questionid = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $questionid_neu);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $tableData[$table] = [];
                    while ($row = $result->fetch_assoc()) {
                        $tableData[$table][] = $row;
                    }

                    // Pr�fen, ob bereits Daten in der Zieltabelle existieren (questionid_alt)
                    $existingData[$table] = [];
                    $query = "SELECT * FROM $table WHERE questionid = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $questionid_alt);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        // Als Index verwenden wir eine kombinierte ID aus Tabellenname und ID
                        $existingData[$table][$row['id']] = $row;
                    }
                }

                // Klonen der Daten, wenn der Clone-Button gedr�ckt wurde
                if (isset($_POST['clone']) && !$dryRun) {
                    foreach ($tables as $table) {
                        foreach ($tableData[$table] as $row) {
                            // Speichern der aktuellen ID
                            $oldId = $row['id'];

                            // Neue Kopie des Datensatzes erstellen
                            $newRow = $row;

                            // Entfernen der ID f�r das INSERT
                            unset($newRow['id']);

                            // �ndern der questionid auf questionid_alt
                            $newRow['questionid'] = $questionid_alt;

                            // Pr�fen, ob ein entsprechender Datensatz bereits existiert (basierend auf ID)
                            $existingId = null;
                            foreach ($existingData[$table] as $id => $existingRow) {
                                // Hier pr�fen wir, ob ein Datensatz mit gleicher ID existiert
                                // Du k�nntest auch andere Felder f�r den Vergleich verwenden
                                if ($existingRow['id'] == $oldId) {
                                    $existingId = $id;
                                    break;
                                }
                            }

                            if ($existingId !== null) {
                                // UPDATE f�r bestehenden Datensatz
                                $setClause = [];
                                $bindParams = [''];
                                $bindValues = [];

                                foreach ($newRow as $column => $value) {
                                    $setClause[] = "$column = ?";
                                    $bindParams[0] .= 's';
                                    $bindValues[] = $value;
                                }

                                // ID f�r die WHERE-Bedingung hinzuf�gen
                                $bindParams[0] .= 'i';
                                $bindValues[] = $existingId;

                                $query = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE id = ?";
                                $stmt = $conn->prepare($query);

                                $stmt->bind_param(...array_merge($bindParams, $bindValues));

                                if ($stmt->execute()) {
                                    $message .= "Datensatz aus $table mit ID $existingId erfolgreich aktualisiert.<br>";
                                } else {
                                    $message .= "Fehler beim Aktualisieren des Datensatzes aus $table mit ID $existingId: " . $stmt->error . "<br>";
                                }
                            } else {
                                // INSERT f�r neuen Datensatz
                                $columns = implode(', ', array_keys($newRow));
                                $placeholders = implode(', ', array_fill(0, count($newRow), '?'));
                                $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

                                $stmt = $conn->prepare($query);
                                $types = str_repeat('s', count($newRow));
                                $stmt->bind_param($types, ...array_values($newRow));

                                if ($stmt->execute()) {
                                    $message .= "Datensatz aus $table mit ID $oldId erfolgreich geklont.<br>";
                                } else {
                                    $message .= "Fehler beim Klonen des Datensatzes aus $table mit ID $oldId: " . $stmt->error . "<br>";
                                }
                            }
                        }
                    }
                    $message .= "Alle Datens�tze wurden erfolgreich geklont/aktualisiert.";
                } elseif (isset($_POST['clone']) && $dryRun) {
                    $message = "Dry Run: Keine �nderungen wurden in der Datenbank vorgenommen.";
                }
            } else {
                $message = "Fehler: Es wurden keine Eintr�ge mit dem Namen '$questionName' und parent = 0 gefunden.";
            }
        } else {
            $message = "Fehler: Keine Frage mit der ID $questionid_alt gefunden.";
        }
    }
}

// htmlspecialchars-Alternative - Fix f�r den Deprecated-Fehler
function h($string) {
    // Sicherstellen, dass $string nicht null ist
    if ($string === null) {
        return '';
    }
    return htmlentities($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moodle Fragen-Kloner</title>
    <style>
        :root {
            --primary-color: #1e88e5;
            --secondary-color: #e3f2fd;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --error-color: #f44336;
            --existing-bg: #fff9c4;
            --text-color: #333;
            --light-text: #757575;
            --border-color: #e0e0e0;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f5f5;
            padding-top: 80px;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background-color: white;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }

        .header .logo {
            height: 60px;
        }

        .header .title {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .form-row .form-input {
            flex-grow: 1;
            margin-right: 20px;
        }

        .form-row .form-toggle {
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            max-width: 400px;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="number"]:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1565c0;
        }

        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
            font-weight: 500;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid var(--success-color);
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid var(--error-color);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: var(--secondary-color);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        /* Hervorhebung f�r bereits existierende Datens�tze */
        tr.existing {
            background-color: var(--existing-bg);
        }

        tr.existing:hover {
            background-color: #ffe082;
        }

        .existing-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: var(--warning-color);
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }

        .new-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: var(--success-color);
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
            position: relative;
        }

        .new-icon:before,
        .new-icon:after {
            content: '';
            position: absolute;
            background-color: white;
        }

        .new-icon:before {
            width: 2px;
            height: 10px;
            top: 3px;
            left: 7px;
        }

        .new-icon:after {
            width: 10px;
            height: 2px;
            top: 7px;
            left: 3px;
        }

        .legend {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 14px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            vertical-align: middle;
            margin-left: 10px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        #dryrun-status {
            margin-left: 10px;
            font-weight: 500;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-clone {
            background-color: var(--success-color);
        }

        .button-clone:hover {
            background-color: #388e3c;
        }

        .table-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .table-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .table-count {
            color: var(--light-text);
            font-size: 14px;
        }

        .empty-message {
            padding: 20px;
            text-align: center;
            color: var(--light-text);
        }
    </style>
</head>
<body>
<header class="header">
    <img src="https://moodle-daad-de.daad.com/pluginfile.php/1/theme_boost_union/logocompact/300x300/1742385000/DAAD_Logo_Kreis_Basic_RGB-kl2.jpg" alt="DAAD Logo" class="logo">
    <h1 class="title">Moodle Fragen-Kloner</h1>
    <img src="https://inccas.de/wp-content/uploads/2023/10/logo.png" alt="INCCAS Logo" class="logo">
</header>

<div class="container">
    <div class="card">
        <h2 class="section-title">Defekte embedded Questions und SLACK Fragen reparieren</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-input">
                    <label for="questionid_alt">Geben Sie hier die ID aus der Fehlermeldung von moodle ein, zu der in der Tabelle qtype_stack_options WHERE questionid = ? die Fehlermeldung kam:</label>
                    <input type="number" id="questionid_alt" name="questionid_alt" value="<?php echo h($questionid_alt); ?>" required>
                </div>
                <div class="form-toggle">
                    <label for="dryrun">Dry Run Modus:</label>
                    <label class="switch">
                        <input type="checkbox" id="dryrun" name="dryrun" value="true" <?php echo $dryRun ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                    <span id="dryrun-status"><?php echo $dryRun ? 'Aktiv (keine &Auml;nderungen werden gespeichert)' : 'Inaktiv (&Auml;nderungen werden gespeichert)'; ?></span>
                </div>
            </div>

            <button type="submit" name="submit">Suchen</button>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Fehler') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($tableData)): ?>
        <div class="card">
            <h2 class="section-title">Gefundene Datens&auml;tze f&uuml;r Frage-ID <?php echo h($questionid_neu); ?></h2>

            <div class="legend">
                <div class="legend-item">
                    <span class="existing-icon"></span> Datensatz existiert bereits und wird bei Klonen aktualisiert
                </div>
                <div class="legend-item">
                    <span class="new-icon"></span> Neuer Datensatz wird erstellt
                </div>
            </div>

            <?php foreach ($tables as $table): ?>
                <div class="table-info">
                    <span class="table-name"><?php echo h($table); ?></span>
                    <?php if (!empty($tableData[$table])): ?>
                        <span class="table-count">
                                <?php echo count($tableData[$table]); ?> Datens&auml;tze gefunden
                                <?php
                                $existingCount = 0;
                                foreach ($tableData[$table] as $row) {
                                    foreach ($existingData[$table] as $existingRow) {
                                        if ($existingRow['id'] == $row['id']) {
                                            $existingCount++;
                                            break;
                                        }
                                    }
                                }
                                if ($existingCount > 0) {
                                    echo " ($existingCount bereits vorhanden)";
                                }
                                ?>
                            </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($tableData[$table])): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                            <tr>
                                <th>Status</th>
                                <?php foreach (array_keys($tableData[$table][0]) as $column): ?>
                                    <th><?php echo h($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($tableData[$table] as $row):
                                $exists = false;
                                foreach ($existingData[$table] as $existingRow) {
                                    if ($existingRow['id'] == $row['id']) {
                                        $exists = true;
                                        break;
                                    }
                                }
                                ?>
                                <tr class="<?php echo $exists ? 'existing' : ''; ?>">
                                    <td>
                                        <?php if ($exists): ?>
                                            <span class="existing-icon" title="Bereits vorhanden"></span>
                                        <?php else: ?>
                                            <span class="new-icon" title="Neu erstellen"></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php foreach ($row as $value): ?>
                                        <td><?php echo h($value); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-message">Keine Daten f&uuml;r <?php echo h($table); ?> gefunden.</div>
                <?php endif; ?>
            <?php endforeach; ?>

            <form method="post">
                <input type="hidden" name="questionid_alt" value="<?php echo h($questionid_alt); ?>">
                <input type="hidden" name="dryrun" value="<?php echo $dryRun ? 'true' : 'false'; ?>">
                <input type="hidden" name="submit" value="1">
                <button type="submit" name="clone" class="button-clone" id="clone-button">
                    <?php echo $dryRun ? 'Datens&auml;tze simulieren (Dry Run)' : 'Datens&auml;tze klonen/aktualisieren'; ?>
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('dryrun').addEventListener('change', function() {
        var status = document.getElementById('dryrun-status');
        var cloneButton = document.getElementById('clone-button');

        if (this.checked) {
            status.innerHTML = 'Aktiv (keine &Auml;nderungen werden gespeichert)';
            if (cloneButton) {
                cloneButton.innerHTML = 'Datens&auml;tze simulieren (Dry Run)';
            }
        } else {
            status.innerHTML = 'Inaktiv (&Auml;nderungen werden gespeichert)';
            if (cloneButton) {
                cloneButton.innerHTML = 'Datens&auml;tze klonen/aktualisieren';
            }
        }
    });
</script>
</body>
</html>