<?php

namespace IcrewSystems\Rtm\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

//TODO add icrew TM in all commands and scripts with proper documentation, such as author , verison , created at
// private zip folder repo and setup steps

class ExportRtmToZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-rtm-to-zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package all RTM files (screenshots, GIFs) into a timestamped ZIP archive and provide a rtm path.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rtm_file_path = 'tests/Browser/screenshots';
        $output_path = 'tests/Browser/RTM_EXPORTS/RTM_exports_' . now()->format('YmdHis') . '.zip';

        // Validate the source directory exists
        if (!File::exists($rtm_file_path)) {
            $this->error("The specified source path does not exist: $rtm_file_path");
            return;
        }

        // Retrieve all files from the directory
        $files = File::allFiles($rtm_file_path);
        $extras = File::allFiles('tests/Browser/RTM_EXTRAS');
        $files = array_merge($files, $extras);

        if (empty($files)) {
            $this->info('No files found in the specified source path.');
            return;
        }

        // Ensure output directory exists
        $outputDir = dirname($output_path);
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0777, true);
        }

        // Create the ZIP archive
        $zip = new ZipArchive();
        if ($zip->open($output_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Failed to create ZIP file at: $output_path");
            return;
        }

        // Add files to the ZIP archive
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname(); // Preserve relative paths within the ZIP
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        // Close the ZIP archive
        if (!$zip->close()) {
            $this->error("Failed to finalize the ZIP file.");
            return;
        }

        $this->info('ZIP file created successfully!');
        $this->info('Download Path: ' . $output_path);
    }
}
