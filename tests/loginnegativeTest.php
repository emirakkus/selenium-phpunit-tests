<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class LoginNegativeTest extends TestCase
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

    public function testInvalidLoginShowsErrorMessage()
    {
        // 1️⃣ Sayfaya git
        $this->driver->get('https://www.saucedemo.com/');

        // 2️⃣ Yanlış kullanıcı adı ve şifre gir
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('wrong_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('wrong_password');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        // 3️⃣ Hata mesajını bekle
        $wait = new WebDriverWait($this->driver, 5);
        $wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-test="error"]'))
        );

        // 4️⃣ Hata mesajını oku
        $errorMessage = $this->driver
            ->findElement(WebDriverBy::cssSelector('[data-test="error"]'))
            ->getText();

        // 5️⃣ Doğrulama yap
        $this->assertStringContainsString(
            'Epic sadface: Username and password do not match any user in this service',
            $errorMessage,
            'Beklenen hata mesajı görünmedi.'
        );

        sleep(5);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
