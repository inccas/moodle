<?php
require_once(__DIR__ . '/../config.php');
// Konfiguration aus Moodle-Config übernehmen
$config = [
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname
];

require_once($CFG->libdir.'/clilib.php');

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

// Variable für die Anzeige der Frage-Details
$questionDetails_alt = null;
$questionDetails_neu = null;

// AJAX Anfrage für die Frage-Details verarbeiten
if (isset($_GET['fetch_question_details']) && !empty($_GET['id'])) {
    $question_id = $_GET['id'];

    $result1 = $DB->get_record('question', array('id' => $question_id), '*', MUST_EXIST);

    # $db_connect = mysqli_connect($config['host'], $config['user'], $config['password'], $config['dbname']);

    // Datensatz_1 abrufen
    # $query1 = "SELECT * FROM mdl_questions WHERE id = ".$question_id . ";";
    # $result1 = mysqli_query($db_connect, $query1);
    #echo("<br><br><br>");
    #var_dump($result1);
    $datensatz_1 = $result1;

    # var_dump($result1);
    # echo("<hr>");
    if ($datensatz_1) {
        // Datensatz_2 abrufen (neueste mit gleichem Namen)
        # $query2 = "SELECT * FROM mdl_questions WHERE name = '".$datensatz_1['name']."' ORDER BY id DESC LIMIT 1 ;";

        $results2 = $DB->get_records('question', array('name' => $datensatz_1->name), 'id DESC', '*', -1, 1);
        $datensatz_2 = current($results2);

        # var_dump($datensatz_2);
        if ($datensatz_2) {
            // Geben Sie beide Datensätze als JSON zurück
            echo json_encode([
                'success' => true,
                'questionDetails_alt' => $datensatz_1,
                'questionDetails_neu' => $datensatz_2
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Keinen neueren Datensatz mit dem Namen '{$name}' gefunden."
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Kein Datensatz mit der ID {$questionId} gefunden."
        ]);
        exit;
    }
}

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

// htmlspecialchars-Alternative - Fix für den Deprecated-Fehler
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
                    <div class="form-input-group">
                        <input type="number" id="questionid_alt" name="questionid_alt" value="<?php echo h($questionid_alt); ?>" required>
                        <button type="button" id="searchButton" class="button-search">
                            <span id="searchButtonText">Suchen</span>
                            <span id="loadingIndicator" class="loading hidden"></span>
                        </button>
                    </div>
                </div>
                <br>
                <div class="form-input">
                    <label for="questionid_neu">Geben Sie hier die neue ID ein, welche zur neuen question passt:</label>
                    <div class="form-input-group">
                        <input type="number" id="questionid_neu" name="questionid_neu" value="<?php echo h($questionid_neu); ?>" required>
                        <button type="submit" id="cloneButton" name="submit" class="button-clone hidden">Klonen durchführen</button>
                    </div>
                </div>
            </div>
          </form>
    </div>


    <?php if (!empty($message)): ?>
        <div id="messageBlock" class="message <?php echo strpos($message, 'Fehler') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Container für Datensatz-Anzeige -->
    <div id="questionDetails" class="card hidden">
        <div id="questionDetails_alt" class="question-details">
            <h3>Details zur Frage (Datensatz 1 - Problematische Frage)</h3>
            <div id="questionDetails_alt_content"></div>
        </div>

        <div id="questionDetails_neu" class="question-details">
            <h3>Details zur neuesten Frage mit gleichem Namen (Datensatz 2 - Funktionierende Frage)</h3>
            <div id="questionDetails_neu_content"></div>
        </div>
    </div>

    <?php if (!empty($tableData)): ?>
        <div class="card" id="resultDetails">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchButton = document.getElementById('searchButton');
        const questionid_alt = document.getElementById('questionid_alt');
        const questionid_neu = document.getElementById('questionid_neu');
        const questionDetails = document.getElementById('questionDetails');
        const questionDetails_alt_content = document.getElementById('questionDetails_alt_content');
        const questionDetails_neu_content = document.getElementById('questionDetails_neu_content');
        const searchButtonText = document.getElementById('searchButtonText');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const messageDiv = document.getElementById('messageBlock');
        const resultDiv = document.getElementById('resultDetails');
        const cloneButton = document.getElementById('cloneButton');

        searchButton.addEventListener('click', function() {
            const id = questionid_alt.value;

            if (!id) {
                alert('Bitte geben Sie eine Frage-ID ein.');
                return;
            }

            // if exists element
            if(messageDiv) {
                messageDiv.classList.add('hidden');
            }
            // if exists element
            if(resultDiv) {
                resultDiv.classList.add('hidden');
            }

            // Zeige Lade-Animation
            searchButtonText.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');

            // Hole die Frage-Details per AJAX
            fetch(`?fetch_question_details=1&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    // Zeige Lade-Animation
                    searchButtonText.classList.add('hidden');
                    loadingIndicator.classList.remove('hidden');
                    if (data.success) {

                        // Fülle die neue ID in das Eingabefeld ein
                        questionid_neu.value = data.questionDetails_neu.id;

                        if (questionid_neu.value == questionid_alt.value) {
                            // Zeige Fehlermeldung
                            alert("Die alte QuestionID muss anders sein, als die neue QuestionID, da man sonst ja den gleichen Datensatz klont und das keine Probleme löst. \n\n Du musst erst die neuen Fragen dieser Lektion von HAGEN per XML hier importieren, damit es weiter gehen kann.");
                        } else {
                            // Zeige die Datensätze an
                            questionDetails.classList.remove('hidden');

                            // Erstelle Tabellen für die Datensätze
                            questionDetails_alt_content.innerHTML = createTable(data.questionDetails_alt);
                            questionDetails_neu_content.innerHTML = createTable(data.questionDetails_neu);

                            // Show the submit button
                            cloneButton.classList.remove('hidden');
                        }

                        // Verstecke Lade-Animation
                        searchButtonText.classList.remove('hidden');
                        loadingIndicator.classList.add('hidden');

                    } else {
                        // Zeige Fehlermeldung
                        alert(data.message);

                        // Verstecke Lade-Animation
                        searchButtonText.classList.remove('hidden');
                        loadingIndicator.classList.add('hidden');

                    }
                })
                .catch(error => {
                    // Verstecke Lade-Animation
                    searchButtonText.classList.remove('hidden');
                    loadingIndicator.classList.add('hidden');

                    console.error('Error:', error);
                    alert('Fehler bei der Anfrage. Bitte versuchen Sie es erneut.');
                });
        });

        // Funktion zum Erstellen einer Tabelle für die Datensätze
        function createTable(data) {
            let html = '<table><thead><tr><th>Feld</th><th>Wert</th></tr></thead><tbody>';

            for (const [key, value] of Object.entries(data)) {
                let displayValue = value;

                // Markiere wichtige Felder
                if (key === 'id') {
                    displayValue = `<strong style="color:var(--primary-color);">${value}</strong>`;
                } else if (key === 'name') {
                    displayValue = `<strong>${value}</strong>`;
                }

                html += `<tr>
                <td>${key}</td>
                <td>${displayValue}</td>
            </tr>`;
            }

            html += '</tbody></table>';
            return html;
        }
    });
</script>

</body>
</html>