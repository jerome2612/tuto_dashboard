<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard Financier">
    <meta name="author" content="Votre Nom">
    <title>Dashboard Financier</title>

    <link rel="stylesheet" href="styles.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Rapport Financier</h1>
        <!-- Menu de navigation pour sélectionner le magasin -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Sélectionner le magasin</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="?magasin=ventes_magasin_paris">Paris</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?magasin=ventes_magasin_lille">Lille</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?magasin=ventes_magasin_marseille">Marseille</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="row mt-4">
            <!-- Revenu Journalier -->
            <div class="col-md-6">
                <h3>Revenu Journalier</h3>
                <canvas id="revenuJournalierChart"></canvas>
            </div>
            <!-- Revenu par Catégorie -->
            <div class="col-md-6">
                <h3>Revenu par Catégorie</h3>
                <canvas id="revenuCategorieChart"></canvas>
            </div>
        </div>
    </div>

    <?php
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pane-admin";

    // Créer la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Détecter le magasin sélectionné dans l'URL
    $magasin = isset($_GET['magasin']) ? $_GET['magasin'] : 'ventes_magasin_paris';

    // Requête pour le revenu journalier
    $revenuJournalierQuery = "SELECT date_vente, SUM(prix * quantite) AS revenu_journalier FROM $magasin GROUP BY date_vente";
    $revenuJournalierResult = $conn->query($revenuJournalierQuery);

    $dates = [];
    $revenus = [];

    if ($revenuJournalierResult->num_rows > 0) {
        while($row = $revenuJournalierResult->fetch_assoc()) {
            $dates[] = $row['date_vente'];
            $revenus[] = $row['revenu_journalier'];
        }
    }

    // Requête pour le revenu par catégorie
    $revenuCategorieQuery = "SELECT categorie, SUM(prix * quantite) AS revenu_par_categorie FROM $magasin GROUP BY categorie";
    $revenuCategorieResult = $conn->query($revenuCategorieQuery);

    $categories = [];
    $revenusCategorie = [];

    if ($revenuCategorieResult->num_rows > 0) {
        while($row = $revenuCategorieResult->fetch_assoc()) {
            $categories[] = $row['categorie'];
            $revenusCategorie[] = $row['revenu_par_categorie'];
        }
    }

    $conn->close();
    ?>

    <!-- Script pour afficher les graphiques -->
    <script>
    // Graphique du revenu journalier
    var revenuJournalierCtx = document.getElementById('revenuJournalierChart').getContext('2d');
    var revenuJournalierChart = new Chart(revenuJournalierCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Revenu Journalier',
                data: <?php echo json_encode($revenus); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique du revenu par catégorie
    var revenuCategorieCtx = document.getElementById('revenuCategorieChart').getContext('2d');
    var revenuCategorieChart = new Chart(revenuCategorieCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                label: 'Revenu par Catégorie',
                data: <?php echo json_encode($revenusCategorie); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>