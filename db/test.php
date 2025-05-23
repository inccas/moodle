<?php

// Database Configuration - Assuming $CFG is defined in Moodle context
global $CFG;

// Ensure $CFG is defined
if (!isset($CFG)) {
    die("Error: Moodle's \$CFG object is not defined.  This script must be run within a Moodle environment.");
}

// Database Configuration - Assuming $CFG is defined in Moodle context
$config = [
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname
];

// Unified Error Handling
set_exception_handler(function ($e) {
    error_log("Uncaught exception: " . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Internal server error']));
});

set_error_handler(function ($code, $message, $file, $line) {
    throw new ErrorException($message, $code, $code, $file, $line);
});


// Establish Database Connection
try {
    $conn = new mysqli(
        $config['host'],
        $config['user'],
        $config['password'],
        $config['dbname']
    );
    $conn->set_charset("utf8mb4");
    $conn->options(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
} catch (mysqli_sql_exception $e) {
    error_log("Connection failed: " . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

// Tables to Process
$tables = [
    'mdl_qtype_stack_options',
    'mdl_qtype_stack_inputs',
    'mdl_qtype_stack_qtest_inputs',
    'mdl_qtype_stack_prts',
    'mdl_qtype_stack_prt_nodes'
];

// Helper Functions
function h($string) {
    return htmlentities($string ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// AJAX Handler for Fetching Question Details
if (isset($_GET['fetch_question_details']) && !empty($_GET['id'])) {
    header('Content-Type: application/json');

    try {
        if (!ctype_digit($_GET['id'])) {
            throw new InvalidArgumentException("Invalid question ID");
        }

        $questionId = (int)$_GET['id'];
        // Fetch the original question details
        $stmt = $conn->prepare("SELECT * FROM mdl_questions WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Fetch the latest question with the same name (if exists)
            $name = $row['name'];
            $stmt = $conn->prepare("SELECT * FROM mdl_questions WHERE name = ? AND id != ? ORDER BY id DESC LIMIT 1");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param("si", $name, $questionId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row2 = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'questionDetails_alt' => $row,
                    'questionDetails_neu' => $row2
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => "No newer record with the name '{$name}' found."
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => "No record with ID {$questionId} found."
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
}

// Form Processing for Cloning Data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    try {
        // Input Validation
        if (empty($_POST['questionid_alt']) || empty($_POST['questionid_neu'])) {
            throw new InvalidArgumentException("Both source and target question IDs are required.");
        }

        if ($_POST['questionid_alt'] === $_POST['questionid_neu']) {
            throw new InvalidArgumentException("Source and target question IDs cannot be the same.");
        }

        // Initialize Variables
        $questionid_alt = (int)$_POST['questionid_alt'];
        $questionid_neu = (int)$_POST['questionid_neu'];
        $tableData = [];
        foreach ($tables as $table) {
            // Check if data already exists for the target question ID
            $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM {$table} WHERE questionid = ?");
            if (!$checkStmt) throw new Exception("Failed to prepare statement: " . $conn->error);

            $checkStmt->bind_param("i", $questionid_alt);
            $checkStmt->execute();
            if ($checkStmt->get_result()->fetch_assoc()['count'] > 0) continue;

            // Fetch data from the source question ID
            $fetchStmt = $conn->prepare("SELECT * FROM {$table} WHERE questionid = ?");
            if (!$fetchStmt) throw new Exception("Failed to prepare statement: " . $conn->error);

            $fetchStmt->bind_param("i", $questionid_neu);
            $fetchStmt->execute();

            while ($row = $fetchStmt->get_result()->fetch_assoc()) {

                $row['questionid'] = $questionid_alt;
                if (isset($row['id'])) {
                    $row['id'] = NULL;
                }
                $columns = implode(", ", array_keys($row));
                $placeholders = implode(", ", array_fill(0, count($row), "?"));
                $insertSql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
                $insertStmt = $conn->prepare($insertSql);

                // Bind parameters dynamically
                $types = "";
                $params = [];
                foreach ($row as $value) {
                    if ($value === NULL) {
                        $types .= "s";
                    } elseif (is_int($value)) {
                        $types .= "i";
                    } elseif (is_float($value)) {
                        $types .= "d";
                    } else {
                        $types .= "s";
                    }
                    $params[] = $value;
                }
                $bindParams = array();
                $bindParams[] = &$types;
                foreach ($params as $key => $value) {
                    $bindParams[] = &$params[$key];
                }
                call_user_func_array(array($insertStmt, 'bind_param'), $bindParams);
                $success = $insertStmt->execute();
                $insertId = $success ? $conn->insert_id : "N/A";
                $error = $success ? "" : $conn->error;

                $results[$table] = [
                    'status' => $success,
                    'message' => $success ? "Successfully cloned. New ID: {$insertId}" : "Error: {$error}"
                ];

            }
        }

        $successCount = 0;
        $errorCount = 0;
        foreach ($results as $result) {
            if ($result['status']) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        $message = "Executed: {$successCount} tables successfully cloned, {$errorCount} errors.";
    }  catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}

// htmlspecialchars-Alternative - Fix für den Deprecated-Fehler

// Only HTML output if not an AJAX request
if (!isset($_GET['fetch_question_details'])): ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Fragen Reparatur Werkzeug</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            input[type=number], input[type=submit] {
                padding: 10px;
                margin: 5px 0;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            input[type=submit] {
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }
            input[type=submit]:hover {
                background-color: #45a049;
            }
            .error {
                color: red;
            }
            .success {
                color: green;
            }
        </style>
        <script>
            function fetchQuestionDetails(questionId) {
                var xhr = new XMLHttpRequest();
                var baseUrl = "<?php echo $CFG->wwwroot; ?>"; // PHP provides Moodle's base URL
                var url = baseUrl + '/db/repairquestions.php?fetch_question_details=1&id=' + questionId;

                xhr.open('GET', url, true);
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                displayQuestionDetails(response.questionDetails_alt, response.questionDetails_neu);
                            } else {
                                alert('Fehler bei der Anfrage: ' + response.message);
                            }
                        } catch (e) {
                            console.error('Fehler beim Parsen der JSON-Antwort:', e);
                            alert('Fehler bei der Anfrage: Unerwartete Antwort vom Server.');
                        }
                    } else if (xhr.status === 404) {
                        alert('Fehler bei der Anfrage: Ressource nicht gefunden (404). Überprüfen Sie die URL.');
                    }
                    else {
                        console.error('Unerwarteter HTTP-Status:', xhr.status, xhr.statusText);
                        alert('Fehler bei der Anfrage: Unbekannter Fehler (' + xhr.status + ' ' + xhr.statusText + ')');
                    }
                };
                xhr.onerror = function() {
                    console.error('Netzwerkfehler beim Senden der Anfrage.');
                    alert('Fehler bei der Anfrage: Netzwerkfehler. Überprüfen Sie Ihre Verbindung.');
                };
                xhr.send();
            }

            function displayQuestionDetails(details_alt, details_neu) {
                document.getElementById('questionDetails_alt').innerHTML = `
                <h2>Original Frage (ID: ${details_alt.id})</h2>
                <pre>${JSON.stringify(details_alt, null, 2)}</pre>
            `;
                document.getElementById('questionDetails_neu').innerHTML = `
                <h2>Neueste Frage (ID: ${details_neu.id})</h2>
                <pre>${JSON.stringify(details_neu, null, 2)}</pre>
            `;
            }

            function clearQuestionDetails() {
                document.getElementById('questionDetails_alt').innerHTML = '';
                document.getElementById('questionDetails_neu').innerHTML = '';
            }

            document.addEventListener('DOMContentLoaded', function() {
                const altIdInput = document.getElementById('questionid_alt');
                altIdInput.addEventListener('blur', function() {
                    if (this.value) {
                        fetchQuestionDetails(this.value);
                    } else {
                        clearQuestionDetails();
                    }
                });
            });
        </script>
    </head>
    <body>

    <h2>Fragen Reparatur Werkzeug</h2>

    <form method="post">
        Quell-Frage ID:
        <input type="number" name="questionid_alt" id="questionid_alt" value="<?= h($questionid_alt) ?>">
        Ziel-Frage ID:
        <input type="number" name="questionid_neu" value="<?= h($questionid_neu) ?>">
        <input type="submit" name="submit" value="Klonen">
    </form>

    <div id="questionDetails_alt"></div>
    <div id="questionDetails_neu"></div>

    <?php if (!empty($message)): ?>
        <p><strong><?= h($message) ?></strong></p>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <h2>Ergebnisse</h2>
        <ul>
            <?php foreach ($results as $table => $result): ?>
                <li>
                    Tabelle <strong><?= h($table) ?></strong>: <?= h($result['message']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    </body>
    </html>

<?php endif; ?>
