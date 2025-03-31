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

$message = '';
$tableData = [];
$existingData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verarbeitung des Formulars bei Submit
    if (isset($_POST['submit'])) {
        $questionid_alt = $_POST['questionid_alt'];
        $questionid_neu = $_POST['questionid_neu'];
        // PHP HEADER SECTION
        // Dear AI, here comes the new code for all tables from $tables

// Clear previous results
        $tableData = [];
        $results = [];

// Check if both IDs are provided and not the same
        if (empty($questionid_alt) || empty($questionid_neu)) {
            $message = "Fehler: Bitte geben Sie beide Fragen-IDs ein.";
        } elseif ($questionid_alt == $questionid_neu) {
            $message = "Fehler: Die Quell-ID und Ziel-ID dürfen nicht identisch sein.";
        } else {
            // Process each table
            foreach ($tables as $table) {
                // First check if an entry with questionid_alt already exists
                $checkSql = "SELECT COUNT(*) as count FROM {$table} WHERE questionid = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("i", $questionid_alt);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['count'] > 0) {
                    // Entry already exists, add to results
                    $results[$table] = [
                        'status' => false,
                        'message' => "Ein Eintrag mit questionid={$questionid_alt} existiert bereits in dieser Tabelle."
                    ];
                    continue;
                }

                // Get the data from the source question
                $sql = "SELECT * FROM {$table} WHERE questionid = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $questionid_neu);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    // No data found
                    $results[$table] = [
                        'status' => false,
                        'message' => "Keine Daten mit questionid={$questionid_neu} gefunden."
                    ];
                    continue;
                }

                // Get all rows (should be only one per table as per requirements)
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                    // Save the original data for display
                    $tableData[$table]['source'][] = $row;
                }

                // Process each row
                foreach ($rows as $row) {
                    // Modify the row: change questionid to the target value
                    $row['questionid'] = $questionid_alt;

                    // Set the ID to NULL to allow auto-increment
                    if (isset($row['id'])) {
                        $row['id'] = NULL;
                    }

                    // Build the INSERT query dynamically
                    $columns = implode(", ", array_keys($row));
                    $placeholders = implode(", ", array_fill(0, count($row), "?"));

                    $insertSql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
                    $insertStmt = $conn->prepare($insertSql);

                    // Bind all parameters dynamically
                    $types = "";
                    $params = [];

                    foreach ($row as $value) {
                        if ($value === NULL) {
                            $types .= "s"; // Handle NULL values
                        } elseif (is_int($value)) {
                            $types .= "i";
                        } elseif (is_float($value)) {
                            $types .= "d";
                        } else {
                            $types .= "s";
                        }
                        $params[] = $value;
                    }

                    // Create the array of references that bind_param needs
                    $bindParams = array();
                    $bindParams[] = &$types;

                    foreach ($params as $key => $value) {
                        $bindParams[] = &$params[$key];
                    }

                    // Apply the parameters to the statement
                    call_user_func_array(array($insertStmt, 'bind_param'), $bindParams);

                    // Execute and store result
                    $success = $insertStmt->execute();
                    $insertId = $success ? $conn->insert_id : "N/A";
                    $error = $success ? "" : $conn->error;

                    if ($success) {
                        // Save the cloned data for display
                        // Make sure to fetch the actual inserted row with new ID for display
                        if ($insertId !== "N/A") {
                            $row['id'] = $insertId; // Update the ID for display
                        }
                        $tableData[$table]['cloned'][] = $row;
                    }

                    $results[$table] = [
                        'status' => $success,
                        'message' => $success ? "Erfolgreich geklont. Neue ID: {$insertId}" : "Fehler: {$error}",
                        'original_count' => count($rows),
                        'inserted_id' => $insertId
                    ];
                }
            }

            // Create summary message
            $successCount = 0;
            $errorCount = 0;

            foreach ($results as $result) {
                if ($result['status']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }

            $message = "Ausgeführt: {$successCount} Tabellen erfolgreich geklont, {$errorCount} Fehler.";

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

        .new-icon:before,
        .new-icon:after {
            content: '';
            position: absolute;
            background-color: white;
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
                <br>
                <div class="form-input">
                    <label for="questionid_alt">Geben Sie hier die neue ID ein, welche zur neuen question passt:</label>
                    <input type="number" id="questionid_neu" name="questionid_neu" value="<?php echo h($questionid_neu); ?>" required>
                </div>
            </div>

            <br>
            <button type="submit" name="submit" class="button-clone">Klonen durchführen</button>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Fehler') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($tableData)): ?>
        <div class="card">
            <!-- Dear AI, Build the main output in this card -->
            <h2 class="section-title">Ergebnisse</h2>

            <p>Quell-Frage ID: <strong><?php echo h($questionid_neu); ?></strong> → Ziel-Frage ID: <strong><?php echo h($questionid_alt); ?></strong></p>

            <?php foreach ($tables as $table): ?>
                <div class="table-info">
                    <div class="table-name"><?php echo h($table); ?></div>
                    <div class="table-count">
                        <?php
                        if (isset($results[$table])) {
                            echo $results[$table]['status']
                                ? "✅ " . $results[$table]['message']
                                : "❌ " . $results[$table]['message'];
                        } else {
                            echo "Keine Verarbeitung";
                        }
                        ?>
                    </div>
                </div>

                <?php if (isset($tableData[$table]) && !empty($tableData[$table]['source'])): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                            <tr>
                                <?php foreach ($tableData[$table]['source'][0] as $key => $value): ?>
                                    <th><?php echo h($key); ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($tableData[$table]['source'] as $idx => $row): ?>
                                <tr>
                                    <?php foreach ($row as $key => $value): ?>
                                        <td>
                                            <?php
                                            if ($key === 'questionid' && $value == $questionid_neu) {
                                                echo "<strong style='color:var(--primary-color);'>" . h($value) . "</strong>";
                                            } else {
                                                echo h($value);
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (isset($tableData[$table]['cloned'])): ?>
                                <?php foreach ($tableData[$table]['cloned'] as $row): ?>
                                    <tr style="background-color: var(--existing-bg);">
                                        <?php foreach ($row as $key => $value): ?>
                                            <td>
                                                <?php
                                                if ($key === 'questionid' && $value == $questionid_alt) {
                                                    echo "<strong style='color:var(--success-color);'>" . h($value) . "</strong>";
                                                } elseif ($key === 'id') {
                                                    echo "<strong style='color:var(--success-color);'>" . h($value) . "</strong> <small>(Neue ID)</small>";
                                                } else {
                                                    echo h($value);
                                                }
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-message">Keine Daten gefunden</div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>