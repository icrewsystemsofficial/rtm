<?php

namespace Icrewsystems\Rtm\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateDuskTestCase extends  Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rtm:generate-dusk-test-case';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generate dusk test case with a custom testcase stub as per icrewsystems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //TODO,add default values for all the prompts

        $baseDirectory = 'tests/Browser/Tests';

        // Prompt for RTM folder name
        $rtmName = $this->ask('Enter the RTM name (e.g., RTM_01)');
        $rtmPath = $baseDirectory . '/' . $rtmName;

        // Prompt for Module name
        $moduleName = $this->ask('Enter the module name');
        $modulePath = $rtmPath . '/' . $moduleName;

        // Ensure the directories exist
        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0777, true);
        }

        // Additional inputs
        $authorName =  exec('git config --get user.name', $output, $resultCode);

        $milestoneName = $this->ask('Enter milestone name');
        $milestoneId = $this->ask('Enter milestone ID');
        $taskId = $this->ask('Enter task ID');
        $testCaseDescription = $this->ask('Enter a brief description of the test case');
//        $testMethodName = $this->ask('Enter the test method name (e.g., can_enable_dark_mode)');

        // Create the test file name
        $testFileName = ucfirst($moduleName) . 'DuskTest.php';
        $testFilePath = $modulePath . '/' . $testFileName;

        // Check if the test file already exists
        if (File::exists($testFilePath)) {
            $this->warn("Test file already exists: $testFilePath");
            return;
        }

        // Generate the test content from the stub
        $stubPath = base_path('stubs/dusk_test_case.stub'); // Define the stub path
        if (!File::exists($stubPath)) {
            $this->error("Stub file not found at: $stubPath");
            return;
        }

        $stubContent = File::get($stubPath);

        $testContent = str_replace(
            [
                '{{AUTHOR_NAME}}',
                '{{CURRENT_DATE}}',
                '{{MILESTONE_NAME}}',
                '{{MILESTONE_ID}}',
                '{{TASK_ID}}',
                '{{TEST_CASE_DESCRIPTION}}',
                '{{MODULE_NAME}}',
                '{{GROUP_NAME}}',
                '{{RTM_FOLDER}}',
            ],
            [
                $authorName,
                now()->format('Y-m-d'),
                $milestoneName,
                $milestoneId,
                $taskId,
                $testCaseDescription,
                $moduleName,
                Str::snake($moduleName),
                $rtmName,

            ],
            $stubContent
        );

        // Write the generated content to the file
        File::put($testFilePath, $testContent);

        $this->info("Test case generated successfully at: $testFilePath");
    }
}
