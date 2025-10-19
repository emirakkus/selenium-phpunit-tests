<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class CheckoutTest extends TestCase
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

    public function testCheckout()
    {
        $this->driver->get('https://www.saucedemo.com/');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.inventory_item:first-child .btn_inventory')));
        
        // Sepete ürün ekle
        $this->driver->findElement(WebDriverBy::cssSelector('.inventory_item:first-child .btn_inventory'))->click();

        // Sepete git ve checkout başlat
        $this->driver->findElement(WebDriverBy::className('shopping_cart_link'))->click();
        $wait->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('checkout')));
        $this->driver->findElement(WebDriverBy::id('checkout'))->click();

        // Formu doldur
        $firstName = $wait->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('first-name')));
        $firstName->sendKeys('Emir');

        $this->driver->findElement(WebDriverBy::id('last-name'))->sendKeys('Akkus');
        $this->driver->findElement(WebDriverBy::id('postal-code'))->sendKeys('34000');
        $this->driver->findElement(WebDriverBy::id('continue'))->click();

        // Finish butonunu kontrol et
        $finishButton = $wait->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('finish')));
        $this->assertTrue($finishButton->isDisplayed(), "Finish butonu görünmeli");
        sleep(10);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
