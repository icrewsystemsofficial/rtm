<?php

namespace IcrewSystems\Rtm\Services\DevOps;

use Illuminate\Support\Str;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use Laravel\Dusk\Browser;

class RTMService
{
    protected static $screenshotPath;
    protected static $prefix;
    protected static $group;

    /**
     * setScreenshotPath
     *
     * @param mixed $path
     * @return void
     */
    public static function setScreenshotPath(string $path)
    {
        self::$screenshotPath = $path;
    }

    /**
     * setPrefix
     *
     * @param mixed $path
     * @return void
     */
    public static function setPrefix(string $path)
    {
        self::$prefix = $path;
    }

    /**
     * setGroup
     *
     * @param mixed $group
     * @return void
     */
    public static function setGroup(string $group)
    {
        self::$group = $group;
    }


    /**
     * log_test_for_documentation
     *
     * @param mixed $browser
     * @param mixed $test_case_id
     * @return void
     */
    public static function log_test_for_documentation(Browser $browser, string $test_case_id)
    {
        // Take screenshot
        $screenshotPath = self::$screenshotPath . DIRECTORY_SEPARATOR . self::$prefix . '_test_' . $test_case_id;
        $browser->screenshot($screenshotPath);

        return;
    }


    public function createGif(array $images, $outputPath, $user_os)
    {
        try {

            if ($user_os != null) {
                $gif = new Imagick();

                foreach ($images as $imagePath) {
                    try {
                        $frame = new Imagick($imagePath);

                        $text = Str::upper(str_replace('-', ' ', basename($imagePath)));

                        // Annotate the image with the file name (test step)
                        $this->addAnnotation($frame, $text, $user_os);

                        $frame->setImageDelay(100); // Delay between frames (100 = 1 second)
                        $gif->addImage($frame);
                        $gif->setImageFormat('gif');
                    } catch (\Exception $e) {
                        echo "Error processing image '$imagePath': " . $e->getMessage() . PHP_EOL;
                    }
                }

                $gif->writeImages($outputPath, true); // Save GIF

            }


        } catch (\Exception $e) {
            // Handle errors (e.g., Imagick not installed or invalid image paths)
            echo "Error creating GIF: " . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Add annotation (watermark) to an image.
     *
     * @param Imagick $image
     * @param string $text The text to annotate (e.g., file name or test step description)
     * @return void
     */
    private function addAnnotation(Imagick $image, string $text, string $os_text)
    {
        try {
            $font_path = config('rtm-testing')[$os_text];

            $draw = new ImagickDraw();
            $draw->setFont($font_path);
            $draw->setFontSize(45);
            $draw->setFillColor(new ImagickPixel('red'));
            $draw->setGravity(Imagick::GRAVITY_NORTHWEST);

            $paddingX = 20;
            $paddingY = 20;

            $image->annotateImage($draw, $paddingX, $paddingY, 0, $text);
        } catch (\Exception $e) {
            // Handle errors in annotation (e.g., missing font file or invalid settings)
            echo "Error annotating image: " . $e->getMessage() . PHP_EOL;
        }
    }
}


?>
