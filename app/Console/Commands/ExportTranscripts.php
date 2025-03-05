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
    protected $signature = 'transcript:export {date? : La date des transcripts à exporter (format YYYY-MM-DD)} {--min-turns=0 : Le nombre minimum de tours pour inclure une partie}';


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
        $date = $this->argument('date');
        $minTurns = $this->option('min-turns');

        // Récupérer tous les UUID uniques
        $uuids = Transcript::distinct()
            ->when($date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->pluck('game_uuid');

        // Vérifier s'il y a des données à exporter
        if ($uuids->isEmpty()) {
            $this->info('Aucun transcript trouvé.');
            return;
        }

        // Dossier où enregistrer les fichiers (storage/app/transcripts/)
        $directory = 'transcripts';
        Storage::makeDirectory($directory);

        foreach ($uuids as $uuid) {
            // Vérifier le nombre de tours pour cet UUID
            $turnCount = Transcript::where('game_uuid', $uuid)->count();
            if ($turnCount < $minTurns) {
                $this->info("UUID $uuid ignoré : $turnCount tours (minimum : $minTurns)");
                continue;
            }

            // Récupérer les lignes pour cet UUID, triées par "turn"
            $transcripts = Transcript::where('game_uuid', $uuid)
                ->orderBy('turn', 'asc')
                ->pluck('text');

            // Récupérer le timestamp du premier enregistrement
            $firstTranscript = Transcript::where('game_uuid', $uuid)
                ->orderBy('turn', 'asc')
                ->first();
            $timestamp = $firstTranscript->created_at->format('Ymd_His');

            // Concaténer les textes en une seule chaîne
            $content = $transcripts->filter()->implode("\n\n");

            // Définir le chemin du fichier
            $filePath = "$directory/{$timestamp}_$uuid.txt";

            // Écrire le fichier
            Storage::put($filePath, $content);

            $this->info("Fichier exporté : storage/app/private/$filePath");
        }

        $this->info('Export terminé !');
    }
}
