<?php
// Moodle-Konfiguration einbinden
require_once(__DIR__ . '/../config.php');

// 🔧 Datenbankkonfiguration aus $CFG übernehmen
$config = [
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname
];

// 🔐 Sicherheitsfunktion für HTML-Ausgabe
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Standardwerte
$searchString = 'https://vorsprung.fernuni-hagen.de/';
$inputString = 'https://vorsprung.fernuni-hagen.de/';
$replaceString = 'https://moodle.daad.de/';
$targetCourseId = 4089;
$message = null;
$executeUpdates = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchString = $_POST['searchString'] ?? $searchString;
    $inputString = $_POST['searchString'] ?? $inputString;
    $targetCourseId = $_POST['kursid'] ?? $targetCourseId;
    $executeUpdates = isset($_POST['executeUpdates']);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Moodle Datenbank Analysetool</title>
    <link rel="stylesheet" href="/db/styles.css" />
</head>
<body>
<header class="header">
    <img src="https://moodle-daad-de.daad.com/pluginfile.php/1/theme_boost_union/logocompact/300x300/1742385000/DAAD_Logo_Kreis_Basic_RGB-kl2.jpg" alt="DAAD Logo" class="logo">
    <h1 class="title">Moodle Datenbank Analysetool</h1>
    <img src="https://inccas.de/wp-content/uploads/2023/10/logo.png" alt="INCCAS Logo" class="logo">
</header>

<div class="container">
    <div class="card">
        <h2>Moodle-Datenbankinhalte direkt finden und austauschen</h2>
        <form method="post">
            <label>Suchstring in DB:</label>
            <input type="text" name="searchString" value="<?= h($searchString) ?>" required>
            <br>
            <label>Kurs-ID:</label>
            <input type="number" name="kursid" value="<?= h($targetCourseId) ?>" required>
            <br><br>
            <button type="submit" name="searchOnly">Suche starten</button>
            <button type="submit" name="executeUpdates">Updates ausführen & speichern</button>
        </form>
    </div>
    <br>
    <div class="card">
        <?php
        $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['dbname']);
        $conn->set_charset("utf8mb4");

        if ($conn->connect_error) {
            die("<div class='error'>Verbindungsfehler: " . $conn->connect_error . "</div>");
        }

        $sql = "
    SELECT TABLE_NAME, COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '{$config['dbname']}'
      AND DATA_TYPE IN ('varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext')
      AND (CHARACTER_MAXIMUM_LENGTH IS NULL OR CHARACTER_MAXIMUM_LENGTH >= 10)
    ";
        $result = $conn->query($sql);

        $matches = [];
        $totalFound = 0;
        $updateSQLs = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $table = $row['TABLE_NAME'];
                $column = $row['COLUMN_NAME'];

                $checkSql = "SELECT COUNT(*) AS cnt FROM `$table` WHERE `$column` LIKE '%" . $conn->real_escape_string($searchString) . "%'";
                $checkResult = @$conn->query($checkSql);
                if ($checkResult) {
                    $count = $checkResult->fetch_assoc()['cnt'];
                    if ($count > 0) {
                        $matches[] = ['table' => $table, 'column' => $column, 'count' => $count];
                        $totalFound += $count;
                    }
                }
            }
        }

        echo "<h2>Teil 1: Gefundene Felder mit Treffern:</h2>";
        echo "<p>Treffer insgesamt: $totalFound</p>";
        echo ('<pre class="message">' .  h(json_encode($matches, JSON_PRETTY_PRINT)) .'</pre>');
        echo "\n </div><br><div class='card'>";

        echo "<h2> Teil 2: UPDATE-Befehle zur Ersetzung von '$inputString' durch '$replaceString':</h2>";
        echo "<p>Folgend die generierten SQL-Befehle:</p>";

        $logFile = __DIR__ . '/updates_' . date('Ymd_His') . '.sql';
        $logHandle = fopen($logFile, 'w');
        $updatesExecuted = 0;

        foreach ($matches as $match) {
            $table = $match['table'];
            $column = $match['column'];
            $count = $match['count'];

            $courseFieldCheck = "
        SELECT COUNT(*) AS has_course
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = '{$conn->real_escape_string($table)}'
          AND COLUMN_NAME = 'course'
          AND TABLE_SCHEMA = '{$config['dbname']}'
        ";
            $courseResult = $conn->query($courseFieldCheck);
            $hasCourseField = ($courseResult && $courseResult->fetch_assoc()['has_course']) ? true : false;

            $updateSql = "UPDATE `$table` SET `$column` = REPLACE(`$column`, '$inputString', '$replaceString')";
            if ($hasCourseField) {
                $updateSql .= " WHERE course = $targetCourseId AND `$column` LIKE '%$inputString%';";
            } else {
                $updateSql .= " WHERE `$column` LIKE '%$inputString%'; -- Achtung: Kein course-Feld!";
            }

            $updateSQLs[] = $updateSql;
            fwrite($logHandle, $updateSql . "\n");

            if ($executeUpdates) {
                if ($conn->query($updateSql)) {
                    $updatesExecuted++;
                }
            }
        }
        fclose($logHandle);

        echo "<pre class='message'>" . h(implode("\n", $updateSQLs)) . "</pre>";
        echo "<p><strong>Anzahl der UPDATE-Befehle: " . count($updateSQLs) . ", ausgeführt: $updatesExecuted</strong></p>";

        $conn->close();
        ?>
    </div>
</div>
</body>
</html>
