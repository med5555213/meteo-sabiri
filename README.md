# 🌤️ Météo Express

Une application web simple en PHP pour afficher la météo actuelle et les prévisions à 5 jours d'une ville donnée, en utilisant l'API OpenWeatherMap.

---

## Description

Météo Express permet à l'utilisateur de saisir le nom d'une ville et d'obtenir :

- La météo actuelle (température, humidité, vent, description).
- Les prévisions météo détaillées par tranches horaires sur 5 jours.
- Un affichage clair avec mode clair/sombre.

L'interface est responsive et accessible, avec un design moderne utilisant HTML, CSS et FontAwesome.

---

## Fonctionnalités

- Recherche météo par ville.
- Affichage des conditions actuelles (température, humidité, vent, icône météo).
- Prévisions météo détaillées par jour et heure, sous forme de tableaux.
- Mode clair/sombre avec sauvegarde de la préférence utilisateur.
- Gestion des erreurs (ville non trouvée, champs vides).
- Affichage en français (langue et format des dates).

---

## Prérequis

- Serveur PHP 7.0 ou supérieur (Apache, Nginx, etc.)
- Extension cURL activée dans PHP
- Clé API OpenWeatherMap (inscription gratuite sur https://openweathermap.org/api)

---

## Installation

1. Clonez ou téléchargez ce dépôt dans votre dossier serveur web.

2. Ouvrez le fichier PHP principal (`index.php` ou autre).

3. Remplacez la variable `$apiKey` par votre propre clé API OpenWeatherMap :

```php
$apiKey = 'votre_cle_api_ici';
