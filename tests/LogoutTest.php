<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class LogoutTest extends TestCase
{
    protected $driver;

    protected function setUp(): void
    {
        $options = new ChromeOptions();
        $options->addArguments(['--incognito', '--disable-popup-blocking']);
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
    }

    public function testLogout()
    {
        $this->driver->get('https://www.saucedemo.com/');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        // Menüden logout'a tıkla
        $this->driver->findElement(WebDriverBy::id('react-burger-menu-btn'))->click();
        sleep(1);
        $this->driver->findElement(WebDriverBy::id('logout_sidebar_link'))->click();

        // Login sayfasına dönüldüğünü kontrol et
        $this->assertStringContainsString(
            'https://www.saucedemo.com/',
            $this->driver->getCurrentURL(),
            'Kullanıcı login sayfasına yönlendirilmeli'
        );
        sleep(10);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
