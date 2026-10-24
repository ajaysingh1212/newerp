<?php

namespace App\Console\Commands;

use App\Services\MasterReportImporter;
use Illuminate\Console\Command;

class ImportBusinessMasterReport extends Command
{
    protected $signature = 'business:import-master-report {manifest} {workbook} {--user=1}';

    protected $description = 'Import a reconciled master-report manifest with complete source history and automatic party matching';

    public function handle(MasterReportImporter $importer): int
    {
        $manifest = json_decode(file_get_contents($this->argument('manifest')), true, 512, JSON_THROW_ON_ERROR);
        $batch = $importer->import($manifest, $this->argument('workbook'), (int) $this->option('user'));
        $this->info('Import #'.$batch->id.' reconciled.');
        $this->line(json_encode($batch->summary, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
