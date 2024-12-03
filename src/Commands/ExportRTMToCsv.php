<?php

namespace Icrewsystems\Rtm\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportRTMToCsv extends Command
{
    protected $signature = 'rtm:export-rtm-to-csv';
    protected $description = 'Export test cases with steps into a CSV file';

    public function handle()
    {
        $rtm_folder = 'tests/Browser/Tests/';
        $outputFile = 'tests/Browser/RTM_EXTRAS/rtm_test_cases.csv';

        $testFiles = File::allFiles($rtm_folder);
        $parsedData = [];

        foreach ($testFiles as $file) {
            $filePath = $file->getRealPath();
            $fileContent = File::get($filePath);

            $moduleName = $this->getModuleName($fileContent);
            $testCases = $this->extractTestCases($fileContent);

            foreach ($testCases as $testCase) {
                $steps = $this->formatSteps($testCase['steps']);
                $parsedData[] = [
                    'module_name' => $moduleName,
                    'test_case' => "{$testCase['test_case_id']} {$testCase['test_case_description']}",
                    'steps' => $steps,
                ];
            }
        }

        $this->generateCsv($parsedData, $outputFile);
        $this->info("Test cases exported to: $outputFile");
    }

    private function getModuleName(string $fileContent): ?string
    {
        preg_match('/describe\(\'(.*?)\'/', $fileContent, $matches);
        return $matches[1] ?? null;
    }

    private function extractTestCases(string $fileContent): array
    {
        $testCases = [];
        preg_match_all('/(?:test|it)\(\'(.*?)\',.*?function.*?{(.*?)}/s', $fileContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $testCaseId = $this->extractTestCaseId($match[1]);
            $testCaseDescription = $this->extractTestCaseDescription($match[1]);
            $testCaseContent = $match[2];

            $steps = $this->extractSteps($testCaseContent);

            $testCases[] = [
                'test_case_id' => $testCaseId,
                'test_case_description' => $testCaseDescription,
                'steps' => $steps,
            ];
        }

        return $testCases;
    }

    private function extractTestCaseId(string $testString): string
    {
        $parts = explode(':', $testString, 2);
        return trim($parts[0]);
    }

    private function extractTestCaseDescription(string $testString): string
    {
        $parts = explode(':', $testString, 2);
        return isset($parts[1]) ? trim($parts[1]) : '';
    }

    private function extractSteps(string $testCaseContent): array
    {
        $steps = [];
        preg_match_all('/->snap\(\'(.*?)\'/', $testCaseContent, $matches);

        foreach ($matches[1] as $index => $snapName) {
            $steps[] = "Step " . ($index + 1) . ": {$snapName}";
        }

        return $steps;
    }

    private function formatSteps(array $steps): string
    {
        return implode(' ', $steps);
    }

    private function generateCsv(array $data, string $filePath)
    {
        $csvContent = "module_name,test_case,steps\n";

        foreach ($data as $row) {
            $csvContent .= "\"{$row['module_name']}\",\"{$row['test_case']}\",\"{$row['steps']}\"\n";
        }

        File::put($filePath, $csvContent);
    }
}
