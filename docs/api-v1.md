# API meteodb v1

## Installation

Après la mise à jour du code, ouvrez une fois /install sur l'instance meteodb. Le chemin d'installation existant crée la table api_tokens si elle n'existe pas, sans toucher aux relevés existants.

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
  "pressure_trend": {
    "period_hours": 3,
    "change_hpa": 1.3,
    "classification": "steady",
    "direction": "steady",
    "condition": "cloud",
    "label": "Temps stable estimé",
    "reference_at": "2026-11-14T09:30:00Z"
  },
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

`pressure_trend` compare la pression courante à la moyenne des relevés disponibles dans une fenêtre de dix minutes centrée sur trois heures avant le relevé courant. Le relevé courant doit dater de moins de dix minutes, et au moins un relevé de pression doit exister dans la fenêtre de référence. La variation est arrondie au dixième avant classification : `strong_rise` au-dessus de +3,5 hPa, `moderate_rise` de +1,6 à +3,5, `steady` de −1,5 à +1,5, `moderate_fall` de −3,5 à −1,6 et `strong_fall` sous −3,5. La condition associée est une estimation heuristique, pas une observation du ciel. Si la pression ou l'historique de référence manque ou est trop ancien, `classification`, `direction` et `condition` valent `unavailable`, tandis que `change_hpa` et `reference_at` valent `null`.

Bien que nommée `dateutc`, la colonne `datapoints.dateutc` stocke une date locale sans fuseau, dans le fuseau configuré par PHP pour l'instance. Les calculs interprètent cette valeur dans ce fuseau; `updated_at` et `reference_at` restent des horodatages UTC ISO 8601 dans la réponse API.

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
