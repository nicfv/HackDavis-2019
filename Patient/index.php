<?php include $_SERVER['DOCUMENT_ROOT'] . '/Scripts/sqlfunc.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Seize</title>
        <meta charset="UTF-8">
        <meta name="description" content="TODO">
        <meta name="keywords" content="TODO">
        <meta name="author" content="TODO">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/Styles/styles.css">
        <link rel="shortcut icon" type="image/png" href="/Styles/favico.png"/>
    </head>
    <body>
        <ul id="nav">
            <li class="navitem">
                <a href="/">Home</a>
            </li>
            <li class="navitem">
                <a href="/About">About</a>
            </li>
        </ul>
        <?php
            if(isset($_GET['id']) && getcol('patients', $_GET['id'], 'id')) {
                if(isset($_POST['hide'])) {
                    if($_POST['hide'] == 'hide') {
                        if(query('UPDATE `'.$dbname.'`.`patients` SET hide=1 WHERE id='.(int)($_GET['id']))) {
                            echo '<p>Patient removed from map!</p>';
                        } else {
                            echo '<p>An error occurred removing patient.</p>';
                        }
                    }
                    else if($_POST['hide'] == 'unhide') {
                        if(query('UPDATE `'.$dbname.'`.`patients` SET hide=0 WHERE id='.(int)($_GET['id']))) {
                            echo '<p>Patient added to map!</p>';
                        } else {
                            echo '<p>An error occurred adding patient.</p>';
                        }
                    }
                }
                echo '<h1>'.getcol('patients', $_GET['id'], 'name').'</h1>';
                echo '<img src="'.getcol('patients', $_GET['id'], 'link').'" alt="Heart Rate Graph" width="100%" height="auto">';
                echo '<form action="/Patient/?id='.$_GET['id'].'" method="post">';
                echo '<input type="radio" name="hide" value="hide" checked />Remove from map';
                echo '<input type="radio" name="hide" value="unhide" />Show on map (Undo)';
                echo '<input type="Submit" />';
                echo '</form>';
            } else {
                echo '<h1>Error</h1>';
                echo '<p>No id was inputted or the patient was not found. Use the navigation bar to go back.</p>';
            }
        ?>
    </body>
</html>