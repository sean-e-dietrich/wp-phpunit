<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\DriverException;

class SomeClassTest extends TestCase
{
    use GoutteTrait;

    public $testPostId;

    protected $driver;

    /**
     * @return \Behat\Mink\Driver\DriverInterface
     * @throws \Behat\Mink\Exception\DriverException
     *   Thrown when invalid arguments are passed.
     */
    protected function getDriverInstance()
    {
        if (!isset($this->driver) && ($driverArgs = '["chrome", { "chromeOptions" : { "w3c": false } }, "http://browser:4444/wd/hub"]')) {
            $driverArgs = json_decode($driverArgs, true);
            $this->driver = new Selenium2Driver(...$driverArgs);
        }
        return $this->driver;
    }

    /**
     * Captures and saves a screenshot.
     *
     * The filename generated screenshot will contain a unique ID, the URL where
     * the screenshot was taken and the given base filename.
     *
     * @param string $base_filename (optional)
     *   The base filename to use, defaults to 'screenshot'.
     */
    protected function captureScreenshot($base_filename = 'screenshot')
    {
        $filename = __DIR__ . '/screenshots/test_' . $base_filename . '.png';
        $screenshot = $this->getDriverInstance()->getScreenshot();
        file_put_contents($filename, $screenshot);
    }

    protected function setUp(): void
    {
//        parent::setUp();
//        $this->setHost('browser');
//        $this->setPort(4444);
//        $this->setBrowserUrl('http://wptests.docksal');
//        $this->setBrowser('firefox');

        parent::setUp();
        $this->setupMinkSession();

        $this->testPostId = wp_insert_post([
            'post_title' => 'Sample Post 22',
            'post_content' => 'This is just some sample post content.',
            'post_status' => 'publish'
        ]);
    }

    protected function tearDown(): void
    {
//        $this->stop();
        parent::tearDown();
        $this->tearDownMinkSession();
        wp_delete_post($this->testPostId, true);
    }

    public function testPostCreated()
    {
//        wp_set_post_tags($this->testPostId, 'angry', true);
        $this->assertNotNull($this->testPostId, 'Test Post Created');

        $link = get_permalink($this->testPostId);
        $this->visit($link);
        $this->captureScreenshot(md5(time()));

        $item = $this->getDriverInstance()->getText('//h1');
//        fwrite(STDERR, var_export($item, true));
        $this->assertEquals('Sample Post 22', $item);
//        file_put_contents(__DIR__ . '/screenshots/test.png', $this->currentScreenshot());
    }

}
