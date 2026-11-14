# API meteodb v1

## Installation

Après la mise à jour du code, ouvrez une fois /install sur l'instance meteodb. Le chemin d'installation existant crée la table api_tokens s'il elle n'existe pas, sans toucher aux relevés existants.

## Jetons de compte

Connectez-vous et ouvrez « Jetons API » dans le menu. Créez un jeton par appareil et copiez le secret lorsqu'il s'affiche. La base ne conserve que son empreinte SHA-256. Les jetons peuvent être révoqués depuis cette page.

Le jeton reste un secret sur l'appareil. Configurez-le uniquement via HTTPS, ne le publiez pas et révoquez-le si l'appareil est perdu.

## Relevé courant

GET /api/v1/stations/{station_id}/current
Authorization: Bearer mdb_<secret>
Accept: application/json

La réponse réussie contient le relevé courant dans un seul document JSON :

{
  "station": {"id": "station-id", "name": "Station"},
  "updated_at": "2026-11-14T12:30:00Z",
  "measurements": {
    "temperature_c": 12.3,
    "indoor_temperature_c": 20.1,
    "humidity_pct": 80,
    "indoor_humidity_pct": 45,
    "pressure_hpa": 1012.4,
    "dew_point_c": 8.9,
    "daily_rain_mm": 0,
    "rain_rate_mm_h": 0,
    "solar_radiation_w_m2": 42,
    "uv_index": 0,
    "wind_speed_kmh": 4.8,
    "wind_gust_kmh": 8,
    "wind_direction_degrees": 180,
    "wind_direction_cardinal": "S"
  }
}

Mesures absentes sont null. Station inconnue ou station sans relevé: HTTP 404. Jeton absent, malformé, invalide ou révoqué: HTTP 401. Toutes les réponses sont en JSON.

Le préfixe /api/v1 réserve un espace stable pour les extensions. Une future route d'historique pourra accepter un indicateur et une période et renvoyer une série horodatée sans changer ce contrat.

## Vérifications manuelles

Remplacez l'hôte, l'identifiant de station et le secret :

    curl -i -H 'Accept: application/json' -H 'Authorization: Bearer mdb_<secret>' \
      'https://meteo.example/api/v1/stations/<station-id>/current'
    curl -i 'https://meteo.example/api/v1/stations/<station-id>/current'
    curl -i -H 'Authorization: Bearer mdb_invalid' \
      'https://meteo.example/api/v1/stations/<station-id>/current'
    curl -i -H 'Authorization: Bearer mdb_<revoked-secret>' \
      'https://meteo.example/api/v1/stations/<station-id>/current'
    curl -i -H 'Authorization: Bearer mdb_<secret>' \
      'https://meteo.example/api/v1/stations/no-such-station/current'
