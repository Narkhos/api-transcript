<?php

namespace App\Console\Commands;

use App\Models\Transcript;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportTranscripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transcript:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporte les transcripts dans des fichiers texte par UUID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer tous les UUID uniques
        $uuids = Transcript::distinct()->pluck('game_uuid');

        // Vérifier s'il y a des données à exporter
        if ($uuids->isEmpty()) {
            $this->info('Aucun transcript trouvé.');
            return;
        }

        // Dossier où enregistrer les fichiers (storage/app/transcripts/)
        $directory = 'transcripts';
        Storage::makeDirectory($directory);

        foreach ($uuids as $uuid) {
            // Récupérer les lignes pour cet UUID, triées par "turn"
            $transcripts = Transcript::where('game_uuid', $uuid)
                ->orderBy('turn', 'asc')
                ->pluck('text');

            // Concaténer les textes en une seule chaîne
            $content = $transcripts->filter()->implode("\n\n");

            // Définir le chemin du fichier
            $filePath = "$directory/$uuid.txt";

            // Écrire le fichier
            Storage::put($filePath, $content);

            $this->info("Fichier exporté : storage/app/private/$filePath");
        }

        $this->info('Export terminé !');
    }
}
