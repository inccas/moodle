<?php

# define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

header('Content-Type: application/json');
# $_POST['question_id'] = 47160;

// Konfiguration aus Moodle-Config Ã¼bernehmen
$config = [
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname
];



if (isset($_POST['question_id'])) {
    $question_id = intval($_POST['question_id']);

    $result1 = $DB->get_record('question', array('id' => $question_id), '*', MUST_EXIST);

    # $db_connect = mysqli_connect($config['host'], $config['user'], $config['password'], $config['dbname']);

    // Datensatz_1 abrufen
    # $query1 = "SELECT * FROM mdl_questions WHERE id = ".$question_id . ";";
    # $result1 = mysqli_query($db_connect, $query1);
    #echo("<br><br><br>");
    #var_dump($result1);
    $datensatz_1 = $result1;

    if ($datensatz_1) {
        // Datensatz_2 abrufen (neueste mit gleichem Namen)
        # $query2 = "SELECT * FROM mdl_questions WHERE name = '".$datensatz_1['name']."' ORDER BY id DESC LIMIT 1 ;";

        $result2 = $DB->get_records('question', array('name' => $datensatz_1->name), 'id DESC', '*', 1);

        $datensatz_2 = $result2;
        #echo("<br><br><br>");
        #var_dump($result2);
        echo json_encode([
            'datensatz_1' => $datensatz_1,
            'datensatz_2' => $datensatz_2
        ]);
    } else {
        echo json_encode(['error' => 'Kein Datensatz gefunden']);
    }
}
?>
