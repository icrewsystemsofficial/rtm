<?php

namespace Icrewsystems\Rtm;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;

class RtmTestBrowser extends Browser
{
    /**
     * Take a screenshot with a dynamic folder structure.
     * This method allows capturing screenshots with a clear context description for each test step.
     * The screenshots are saved in a dynamically created folder structure based on the provided `$folder`.
     * It ensures that all screenshots are organized for easy tracking and reporting.
     *
     *
     *
     * @param string $context A descriptive name for the screenshot (e.g., test case ID or action description).
     * @param string $folder The folder structure where the screenshot will be saved,
     *                         formatted as "RTM_XX-ModuleName" (e.g., "RTM_02-AuthenticationModule").
     *
     * @return $this
     */
    public function snap(string $context, string $folder): self
    {
        $timestamp = now()->format('YmdHis'); // Generate a timestamp for uniqueness
        $screenshotName = Str::slug($context);


        //TODO - generate screenshots only when --snap flag is passed while running art test/dusk
        //to remove unnecessary git changes
        $relativePath = str_replace('-', '/', $folder);
        $directoryPath = base_path("tests/Browser/{$relativePath}");


        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0777, true); // Recursive directory creation
        }

        $relativeScreenshotPath = "{$relativePath}/{$screenshotName}";

        $this->screenshot($relativeScreenshotPath);

        return $this;
    }
}
