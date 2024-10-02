<?php
// Aktiviere Fehlerberichterstattung für Entwicklungszwecke
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datei zum Speichern der Einträge
$datei = 'eintraege.txt';

// Funktion zum Hinzufügen eines Eintrags
function eintragHinzufuegen($name, $nachricht, $stimmung) {
    global $datei;
    $eintrag = date('Y-m-d H:i:s') . " - $name ($stimmung): $nachricht\n";
    file_put_contents($datei, $eintrag, FILE_APPEND);
}

// Funktion zum Lesen aller Einträge
function eintraegeAnzeigen() {
    global $datei;
    if (file_exists($datei)) {
        $eintraege = file_get_contents($datei);
        $eintraege = array_reverse(explode("\n", trim($eintraege)));
        $html = '<ul class="eintraege">';
        foreach ($eintraege as $eintrag) {
            if (!empty($eintrag)) {
                $teile = explode(" - ", $eintrag, 2);
                $datum = $teile[0];
                $inhalt = $teile[1];
                preg_match('/\((.*?)\)/', $inhalt, $stimmung);
                $stimmung = $stimmung[1] ?? 'neutral';
                $inhalt = preg_replace('/\(.*?\):/', '', $inhalt);
                $html .= "<li class='eintrag stimmung-$stimmung'>";
                $html .= "<span class='datum'>$datum</span>";
                $html .= "<span class='inhalt'>$inhalt</span>";
                $html .= "</li>";
            }
        }
        $html .= '</ul>';
        return $html;
    }
    return "<p>Noch keine Einträge vorhanden. Sei der Erste!</p>";
}

// Funktion für zufällige Willkommensnachricht
function zufallsWillkommenNachricht() {
    $nachrichten = [
        "Schön, dass du da bist!",
        "Willkommen im coolsten Gästebuch der Welt!",
        "Dein Eintrag macht unser Gästebuch noch besser!",
        "Lass uns wissen, was du denkst!",
        "Deine Meinung zählt!"
    ];
    return $nachrichten[array_rand($nachrichten)];
}

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $nachricht = htmlspecialchars($_POST['nachricht'] ?? '');
    $stimmung = htmlspecialchars($_POST['stimmung'] ?? 'neutral');
    
    if (!empty($name) && !empty($nachricht)) {
        eintragHinzufuegen($name, $nachricht, $stimmung);
        $erfolgsmeldung = "Dein Eintrag wurde erfolgreich hinzugefügt!";
    } else {
        $fehlermeldung = "Bitte fülle alle Felder aus!";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supercooles Gästebuch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>🎉 Willkommen im Supercoolen Gästebuch! 🎉</h1>
    </header>

    <main>
        <section class="formular-container">
            <h2><?php echo zufallsWillkommenNachricht(); ?></h2>
            <?php
            if (isset($erfolgsmeldung)) echo "<p class='erfolg'>$erfolgsmeldung</p>";
            if (isset($fehlermeldung)) echo "<p class='fehler'>$fehlermeldung</p>";
            ?>
            <form method="post" class="gaestebuch-formular">
                <label for="name">Dein Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="nachricht">Deine Nachricht:</label>
                <textarea id="nachricht" name="nachricht" required></textarea>

                <label for="stimmung">Deine Stimmung:</label>
                <select id="stimmung" name="stimmung">
                    <option value="fröhlich">😄 Fröhlich</option>
                    <option value="neutral" selected>😐 Neutral</option>
                    <option value="nachdenklich">🤔 Nachdenklich</option>
                    <option value="aufgeregt">🎉 Aufgeregt</option>
                </select>

                <button type="submit">Eintrag hinzufügen</button>
            </form>
        </section>

        <section class="eintraege-container">
            <h2>Gästebucheinträge</h2>
            <?php echo eintraegeAnzeigen(); ?>
        </section>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> Mein Supercooles Gästebuch</p>
    </footer>
</body>
</html>