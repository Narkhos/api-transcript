# Api Transcript

Cette api permet de sauvegarder les transcripts des joueurs.

## Installation de l'api

L'api utilise laravel 12.


- L'api requier php 8.
- On aura besoin de composer
- Pour utiliser sqlite, il faudra installer sqlite3 sur le système hôte.

```
sudo apt-get install sqlite3
```

- Récupérer le projet depuis github
- Installation des dépendances

```
composer install
```

- Créer le fichier .env à partir du .env.example
- Lancer les migrations pour créer les tables en base de données :
```
php artisan migrate
```

Pour information, la base de donnée sqlite est un simple fichier qui se trouve dans le projet :
```
./database/database.sqlite
```

## Démarrer l'api

```
php artisan serve
```

On peut éventuellement préciser des paramètres comme le port ou autre, cf. l'aide de la commande :

```
php artisan serve --help
```

## Headers communs à toutes les routes

```
accept: application/json
```

## Routes disponibles

### Lister les parties [GET]

Cette route permet de lister les parties enregistrées. Chaque session est référencée par un uuid.

```
/api/games
```

#### Format de retour :
```
[
	{
		"game_uuid": "3332c9db-93de-44b5-b0d7-78089a10ee78",
		"latest_created_at": "2025-03-02 18:17:52"
	},
	{
		"game_uuid": "93a9722b-a03b-4f6d-80e6-cec9f77ba662",
		"latest_created_at": "2025-03-02 18:10:11"
	},
	{
		"game_uuid": "a9ece1b7-fe98-43fe-814d-628d685a470f",
		"latest_created_at": "2025-03-02 17:34:22"
	}
]
```

### Lister les lignes de transcript [GET]

Liste toutes les lignes de transcript, triées par uuid et par turn.

```
/api/transcripts
```

#### Header optionnel pour filtrer sur une partie en particulier

```
game_uuid: [uuid de la partie] 
```

### Ajouter une ligne à un transcript [POST]

Permet d'enregistrer une action dans le transcript.

```
/api/transcripts
```

Les paramètres sont envoyés dans le body au format JSON :

```json
{
	"turn": 0,
	"game_uuid": "a9ece1b7-fe98-43fe-814d-628d685a470f",
	"text": "Contenu du transcript"
}
```

## Commande d'export

Depuis le serveur, on peut exporter l'ensemble des transcripts dans des fichiers texte pour consultation.

```
php artisan transcript:export
```

les fichiers ont des noms de la forme [uuid.txt] et sont créés dans le dossier [./storage/app/private/transcripts].

