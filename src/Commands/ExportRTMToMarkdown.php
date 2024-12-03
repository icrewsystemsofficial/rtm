<?php

namespace Icrewsystems\Rtm\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportRTMToMarkdown  extends Command
{
    protected $signature = 'rtm:export-rtm-to-markdown';
    protected $description = 'Generate a Markdown file from test case context and screenshots';

    public function handle()
    {
        $rtm_folder = 'tests/Browser/Tests/';
        $outputFile = 'tests/Browser/RTM_EXTRAS/rtm_test_cases.md';

        $testFiles = File::allFiles($rtm_folder);
        $markdownContent = "# Test Case Documentation\n\n";

        foreach ($testFiles as $file) {
            $filePath = $file->getRealPath();
            $fileContent = File::get($filePath);

            $moduleName = $this->getModuleName($fileContent);
            $moduleFolder = $this->getModuleFolder($fileContent);
            $testCases = $this->extractTestCases($fileContent, $moduleFolder);

            if ($moduleName) {
                $markdownContent .= "## Module: {$moduleName}\n\n";
            }

            foreach ($testCases as $testCase) {
                $steps = $this->formatStepsForMarkdown($testCase['steps']);
                $markdownContent .= "### Test Case: {$testCase['test_case_id']} - {$testCase['test_case_description']}\n\n";
                $markdownContent .= "#### Steps:\n\n";
                $markdownContent .= $steps . "\n\n";
            }
        }

        File::put($outputFile, $markdownContent);
        $this->info("Markdown file generated at: $outputFile");
    }

    private function getModuleName(string $fileContent): ?string
    {
        preg_match('/describe\(\'(.*?)\'/', $fileContent, $matches);
        return $matches[1] ?? null;
    }

    private function getModuleFolder(string $fileContent): ?string
    {
        preg_match('/\$this->folder = \'(.*?)\'/', $fileContent, $matches);
        return $matches[1] ?? null;
    }

    private function extractTestCases(string $fileContent, ?string $moduleFolder): array
    {
        $testCases = [];
        preg_match_all('/(?:test|it)\(\'(.*?)\',.*?function.*?{(.*?)}/s', $fileContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $testCaseId = $this->extractTestCaseId($match[1]);
            $testCaseDescription = $this->extractTestCaseDescription($match[1]);
            $testCaseContent = $match[2];

            $steps = $this->extractSteps($testCaseContent, $moduleFolder, $testCaseId);

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

    private function extractSteps(string $testCaseContent, ?string $moduleFolder, string $testCaseId): array
    {
        $steps = [];
        preg_match_all('/->snap\(\'(.*?)\'/', $testCaseContent, $matches);

        $actual_screenshot_path = explode('-', $moduleFolder, 2);

        foreach ($matches[1] as $index => $snapName) {
            $formattedSnapName = strtolower(str_replace('_', '-', $snapName));
            $steps[] = "Step " . ($index + 1) . ": ![{$snapName}](screenshots/{$actual_screenshot_path[0]}/$actual_screenshot_path[1]/{$formattedSnapName}.png)";
        }

        return $steps;
    }

    private function formatStepsForMarkdown(array $steps): string
    {
        return implode("\n", $steps);
    }

}
