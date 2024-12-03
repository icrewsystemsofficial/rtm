<?php

namespace Icrewsystems\Rtm\Commands;



use IcrewSystems\Rtm\Services\DevOps\RTMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateGifForTestCases  extends Command
{
    // TODO ADD A FLAG AS --all to generate gifs for all the rtm's
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rtm:generate-gif-for-test-cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseDirectory = 'tests/Browser/screenshots';

        $rtmDirectories = File::directories($baseDirectory);

        $rtmNames = array_map('basename', $rtmDirectories);

        // Step 2: Ask the user to select an RTM folder
        $this->info('This command will help you generate GIFs from your test screenshots. Before proceeding, ensure you’ve covered both the happy and unhappy path test cases as per TID.');

        $this->warn('Important: Before proceeding, please ensure the following:');
        $this->line('- Imagick PHP extension is installed and enabled on your system. check Readme for more information.');
        $this->line('- A valid font path is configured in the "rtm" config file for annotating images.');
        $this->line('- Ensure a valid font path is configured in the "rtm-testing" configuration file and updated in the Services/GifCreator class according to your operating system for annotating GIFs.');
        $this->line('- Your screenshots are organized under the appropriate RTM and module folders.');

        if (!$this->confirm('Do you want to proceed with generating the GIFs?', true)) {
            $this->info('GIF generation process aborted. Please review the above requirements and try again.');
            return;
        }

        $os_choices = [
            'mac',
            'windows',
            'linux'
        ];

        $user_os = $this->choice('Your system os (MAC/Windows/Linux) ?', $os_choices);

        $selectedRTM = $this->choice('Select an RTM folder', $rtmNames);

        // Step 3: Get the selected RTM folder's path
        $selectedRTMPath = $baseDirectory . '/' . $selectedRTM;

        // Step 4: List Modules inside the selected RTM folder
        $modules = File::directories($selectedRTMPath);

        $moduleNames = array_map('basename', $modules); // Get the names of module folders

        // Step 5: Ask the user to select a module
        $selectedModule = $this->choice('Select a module:', $moduleNames);

        $this->info('Please hold on while the GIF is being generated...');

        // Step 6: Get images from the selected module
        $selectedModulePath = $selectedRTMPath . '/' . $selectedModule;

        $images = [];
        $files = File::files($selectedModulePath);

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif'])) {
                $images[] = $file->getPathname();
            }
        }


        $gif_creator = new RTMService();
        $gif_creator->createGif($images, 'tests/Browser/screenshots/' . $selectedRTM . DIRECTORY_SEPARATOR . $selectedModule . '.gif', $user_os);

        $this->info('Gif created successfully!, Check tests/Browser/screenshots/' . $selectedRTM . DIRECTORY_SEPARATOR . $selectedModule . '.gif');

    }
}
