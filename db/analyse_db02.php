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
$inputString = 'https://vorsprung.fernuni-hagen.de/static';
$replaceString = '/theme/boost_union_vorsprung/pix/static';
$targetCourseId = 4089;
$message = null;

// Falls Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchString = $_POST['searchString'] ?? $searchString;
    $inputString = $_POST['searchString'] ?? $inputString;
    $targetCourseId = $_POST['kursid'] ?? $targetCourseId;
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
            <button type="submit">Suche starten</button>
        </form>
    </div>
    <br>
    <div class="card">
    <?php

    // Verbindung zur Datenbank herstellen
    $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['dbname']);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("<div class='error'>Verbindungsfehler: " . $conn->connect_error . "</div>");
    }

    // 🔍 Schritt 1: Alle relevanten TEXT-Spalten finden
    $sql = "
    SELECT TABLE_NAME, COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '{$config['dbname']}'
      AND DATA_TYPE IN ('varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext')
      AND (CHARACTER_MAXIMUM_LENGTH IS NULL OR CHARACTER_MAXIMUM_LENGTH >= 10)
";
    $result = $conn->query($sql);

    $matches = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $table = $row['TABLE_NAME'];
            $column = $row['COLUMN_NAME'];

            // Prüfen, ob der Suchstring in der Spalte vorkommt
            $checkSql = "SELECT COUNT(*) AS cnt FROM `$table` WHERE `$column` LIKE '%" . $conn->real_escape_string($searchString) . "%' LIMIT 1";
            $checkResult = @$conn->query($checkSql);
            if ($checkResult) {
                $count = $checkResult->fetch_assoc()['cnt'];
                if ($count > 0) {
                    $matches[] = ['table' => $table, 'column' => $column];
                }
            }
        }
    }

    echo "<h2>Teil 1: Gefundene Felder mit Treffern:</h2>";
    echo "<p>Folgend eine Liste mit allen Datenbanktabellen und den Feldern wo der Suchstring auftaucht. Das brauchen wir für Schritt zwei :</p>";
    $message .= json_encode($matches, JSON_PRETTY_PRINT);
    echo ('<pre class="message">' .  h($message) .'</pre>');
    $message = null;

    echo "\n </div><br><div class='card'>";

    // 🛠 SQL-UPDATEs generieren
    echo "<h2> Teil 2: UPDATE-Befehle zur Ersetzung von '$inputString' durch '$replaceString':</h2>";
    echo "<p>Folgend eine Liste mit allen SQL Befehlen die man so mit copy&paste direkt in den phpmyAdmin Editor kopieren und danach ausführen könnte :</p>";

    foreach ($matches as $match) {
        $table = $match['table'];
        $column = $match['column'];

        // Prüfen, ob die Tabelle ein 'course'-Feld hat
        $courseFieldCheck = "
        SELECT COUNT(*) AS has_course
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = '{$conn->real_escape_string($table)}'
          AND COLUMN_NAME = 'course'
          AND TABLE_SCHEMA = '{$config['dbname']}'
    ";
        $courseResult = $conn->query($courseFieldCheck);
        $hasCourseField = ($courseResult && $courseResult->fetch_assoc()['has_course']) ? true : false;

        // SQL-Statement zusammenbauen
        $updateSql = "UPDATE `$table` SET `$column` = REPLACE(`$column`, '$inputString', '$replaceString')";
        if ($hasCourseField) {
            $updateSql .= " WHERE course = $targetCourseId AND `$column` LIKE '%$inputString%';";
        } else {
            $updateSql .= " WHERE `$column` LIKE '%$inputString%';  -- Achtung: Kein course-Feld!";
        }

        $message .= $updateSql . "\n";
    }

    // Verbindung schließen
    $conn->close();

    ?>

    <?php if (!empty($message)): ?>
        <pre class="message"><?= h($message) ?></pre>
    <?php endif; ?>
    </div>
</div>
</body>
</html>
