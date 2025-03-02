// Chargement initial des données
let chart;
let dataSets; // Variable globale pour stocker les données

// Fonction pour charger les données depuis le serveur
async function loadData() {
    try {
        const response = await fetch('http://site1.com/PHP/project/FINAL/src/admin/controllers/home.php');
        const data = await response.json();
        
        // Stockage des données dans la variable globale
        dataSets = data.filieres;
        
        initializeCharts(data);
    } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
    }
}

// Fonction modifiée pour filtrer les données
function filterData(type) {
    if (dataSets && dataSets[type]) {
        chart.options.title["text"] = `Candidatures par Filière en ${type.toUpperCase()}`;

        // Vérification que dataSets[type] est un objet avec des indices numériques
        if (typeof dataSets[type] === 'object') {
            // Convertir les valeurs de l'objet en un tableau
            const dataArray = Object.values(dataSets[type]);

            // Formatage des données dynamiques pour 'master'
            chart.options.data[0].dataPoints = dataArray.map(item => ({
                label: item.label,
                y: item.y
            }));

        } else {
            console.error(`Les données pour le type ${type} ne sont pas au format attendu (tableau ou objet).`);
        }

        chart.render();
    } else {
        console.error(`Aucune donnée pour le type : ${type}`);
    }
}

function initializeCharts(data) {
    // Premier graphique (colonnes)
    chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title: {
            text: "Candidatures par Filière en Licence"
        },
        axisY: {
            includeZero: true
        },
        axisX: { 
            labelAngle: -45,
        },
        data: [{
            type: "column",
            indexLabel: "{y}",
            indexLabelFontColor: "#5A5757",
            indexLabelPlacement: "outside",
            dataPoints: data.filieres.licence // Données par défaut
        }]
    });

    // Deuxième graphique (pie)
    const chart1 = new CanvasJS.Chart("chartContainer1", {
        theme: "light2",
        animationEnabled: true,
        exportEnabled: true,
        title: {
            text: "Répartition des Candidatures par Cycle d'Ingénieur"
        },
        data: [{
            type: "pie",
            indexLabel: "{y}%",
            yValueFormatString: "#,##0.00\"%\"",
            indexLabelPlacement: "inside",
            indexLabelFontColor: "#36454F",
            indexLabelFontSize: 18,
            indexLabelFontWeight: "bolder",
            showInLegend: true,
            legendText: "{label}",
            dataPoints: data.ingenieur
        }]
    });

    // Troisième graphique (donut)
    const chart2 = new CanvasJS.Chart("chartContainer2", {
        theme: "light2",
        animationEnabled: true,
        exportEnabled: true,
        title: {
            text: "Répartition des Candidatures: Validées/Refusées"
        },
        data: [{
            type: "doughnut",
            startAngle: 240,
            innerRadius: "50%",
            indexLabel: "{label}: {y}%",
            yValueFormatString: "##0.00\"%\"",
            dataPoints: [
                { y: data.validation.validees, label: "Validées", color: "#4CAF50" },
                { y: data.validation.refusees, label: "Refusées", color: "#F44336" }
            ]
        }]
    });

    // Rendu des graphiques
    chart.render();
    chart1.render();
    chart2.render();
    }

// Chargement initial des données
document.addEventListener('DOMContentLoaded', loadData);