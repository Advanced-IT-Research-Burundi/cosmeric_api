<?php

namespace App\Jobs;

use App\Services\ImportationService;
use App\Models\CotisationMensuelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;
    public $userId;

    /**
     * Maximum seconds a job can run
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $date, ?int $userId = null)
    {
        $this->date = $date;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportationService $importationService)
    {
        try {
            $staging = CotisationMensuelle::where('date_cotisation', $this->date)->get()->toArray();

            if (empty($staging)) {
                Log::warning("ProcessImportJob: aucun enregistrement de staging pour la date {$this->date}");
                return;
            }

            $importationService->processImport($staging, $this->date);

            Log::info("Importation traitée pour la date {$this->date}");
        } catch (\Exception $e) {
            Log::error("Erreur lors du job d'importation pour la date {$this->date}: " . $e->getMessage());
            // Optionally: notify admins, update staging rows with error, etc.
        }
    }
}
