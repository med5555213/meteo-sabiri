<?php
$apiKey = 'b5700854d33d8db467d91cf194b109e7';

function fetchWeatherData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) return false;
    return json_decode($response, true);
}

$error = null;
$currentWeather = null;
$forecast = null;
$city = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['city'])) {
    $city = trim(filter_var($_GET['city'], FILTER_SANITIZE_STRING));
    if ($city === '') {
        $error = 'Veuillez entrer une ville valide.';
    } else {
        $urlCurrent = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid={$apiKey}&units=metric&lang=fr";
        $urlForecast = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($city) . "&appid={$apiKey}&units=metric&lang=fr";

        $currentWeather = fetchWeatherData($urlCurrent);
        $forecast = fetchWeatherData($urlForecast);

        if (!$currentWeather || !$forecast || ($currentWeather['cod'] != 200)) {
            $error = "Ville non trouvée ou erreur lors de la récupération des données météo.";
            $currentWeather = null;
            $forecast = null;
        }
    }
}

function formatDateFR($datetime) {
    $timestamp = strtotime($datetime);
    $jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    $mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $jourSemaine = $jours[date('w', $timestamp)];
    $jourMois = date('d', $timestamp);
    $moisAnnee = $mois[date('n', $timestamp) - 1];
    return ucfirst("$jourSemaine $jourMois $moisAnnee");
}

function formatHourFR($datetime) {
    $timestamp = strtotime($datetime);
    return date('H:i', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>🌤️ Météo Express</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #87CEFA, #f9f871);
        color: #333;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dark-mode {
        background-color: #121212;
        color: #eee;
    }

    header {
        text-align: center;
        padding: 30px 10px 10px;
    }

    h1 {
        font-size: 2.5rem;
        margin: 0;
    }

    p {
        font-size: 1.1rem;
        margin: 0;
    }

    #mode-toggle {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #fff;
        color: #333;
        border: none;
        font-size: 20px;
        padding: 10px 14px;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        cursor: pointer;
        z-index: 1000;
    }

    .dark-mode #mode-toggle {
        background: #444;
        color: #fff;
    }

    .search-container {
        max-width: 600px;
        margin: 20px auto;
        background: rgba(255,255,255,0.9);
        border-radius: 30px;
        padding: 20px;
        display: flex;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }

    .dark-mode .search-container {
        background-color: #1e1e1e;
    }

    input[type="text"] {
        flex: 1;
        padding: 15px;
        font-size: 16px;
        border: none;
        outline: none;
        border-radius: 30px 0 0 30px;
    }

    button[type="submit"] {
        padding: 15px 20px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 0 30px 30px 0;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .results-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .current-weather {
        display: flex;
        gap: 20px;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
    }

    .current-weather img {
        width: 80px;
        height: 80px;
    }

    .day-forecast {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .dark-mode .day-forecast {
        background-color: #222;
    }

    .day-forecast h3 {
        margin-top: 0;
        text-transform: capitalize;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }

    thead th {
        background-color: #2980b9;
        color: white;
        padding: 10px;
    }

    .dark-mode thead th {
        background-color: #444;
    }

    tbody td {
        padding: 10px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .dark-mode tbody td {
        background-color: #333;
    }
    .search-box {
        display: flex;
        justify-content: center;
        margin: 20px auto;
    }

    .search-box form {
        display: flex;
        background-color: #f0f8ff;
        padding: 10px;
        border-radius: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .search-box input[type="text"] {
        padding: 10px 15px;
        border: none;
        border-radius: 25px 0 0 25px;
        font-size: 16px;
        width: 260px;
        outline: none;
    }

    .search-box button {
        padding: 10px 20px;
        border: none;
        background-color: #3498db;
        color: white;
        font-size: 16px;
        cursor: pointer;
        border-radius: 0 25px 25px 0;
        transition: background-color 0.3s ease;
    }

    .search-box button:hover {
        background-color: #2980b9;
    }
</style>
</head>
<body>
<button id="mode-toggle" aria-label="Toggle dark mode"><i class="fas fa-moon"></i></button>
<header>
    <h1>🌤️ Météo Express</h1>
    <p>Consultez la météo actuelle et les prévisions à 5 jours</p>
</header>
<div class="search-box">
    <form method="GET" action="">
        <input type="text" name="city" placeholder="Entrez une ville" value="<?= htmlspecialchars($city) ?>" required />
        <button type="submit">🔍 Chercher</button>
    </form>
</div>

<?php if ($error): ?>
    <p style="text-align:center;color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($currentWeather && $forecast): ?>
<main class="results-container" role="main">
    <div class="current-weather" aria-label="Météo actuelle">
        <img src="https://openweathermap.org/img/wn/<?= htmlspecialchars($currentWeather['weather'][0]['icon']) ?>@2x.png" alt="<?= htmlspecialchars($currentWeather['weather'][0]['description']) ?>">
        <div>
            <strong style="font-size:2rem;"><?= round($currentWeather['main']['temp']) ?>°C</strong><br>
            <?= htmlspecialchars(ucfirst($currentWeather['weather'][0]['description'])) ?><br>
            💧 <?= htmlspecialchars($currentWeather['main']['humidity']) ?>% - 🌬️ <?= round($currentWeather['wind']['speed'] * 3.6) ?> km/h
        </div>
    </div>

    <?php
        $forecastsByDay = [];
        foreach ($forecast['list'] as $entry) {
            $day = date('Y-m-d', strtotime($entry['dt_txt']));
            $forecastsByDay[$day][] = $entry;
        }
    ?>
    <?php foreach ($forecastsByDay as $day => $entries): ?>
        <section class="day-forecast" aria-label="Prévisions pour le <?= formatDateFR($day) ?>">
            <h3><?= formatDateFR($day) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Température</th>
                        <th>Vent</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?= formatHourFR($entry['dt_txt']) ?></td>
                        <td><?= round($entry['main']['temp']) ?>°C</td>
                        <td><?= round($entry['wind']['speed'] * 3.6) ?> km/h</td>
                        <td>
                            <img src="https://openweathermap.org/img/wn/<?= htmlspecialchars($entry['weather'][0]['icon']) ?>.png" alt="<?= htmlspecialchars($entry['weather'][0]['description']) ?>" />
                            <?= htmlspecialchars(ucfirst($entry['weather'][0]['description'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endforeach; ?>
</main>
<?php endif; ?>

<script>
  const toggle = document.getElementById('mode-toggle');
  const body = document.body;
  const icon = toggle.querySelector('i');

  // Load saved mode
  if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark-mode');
    icon.classList.replace('fa-moon', 'fa-sun');
  }

  toggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    if (isDark) {
      icon.classList.replace('fa-moon', 'fa-sun');
      localStorage.setItem('darkMode', 'enabled');
    } else {
      icon.classList.replace('fa-sun', 'fa-moon');
      localStorage.setItem('darkMode', 'disabled');
    }
  });
</script>
</body>
</html>
