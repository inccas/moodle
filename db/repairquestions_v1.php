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

// UTF-8 Zeichensatz für Verbindung setzen
$conn->set_charset("utf8mb4");

// Überprüfen, ob die Verbindung erfolgreich war
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
    <link rel="stylesheet" type="text/css" href="/db/styles.css" />
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