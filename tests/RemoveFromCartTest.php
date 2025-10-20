<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class RemoveFromCartTest extends TestCase
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

    public function testRemoveFromCart()
    {
        $this->driver->get('https://www.saucedemo.com/');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        // Ürünü sepete ekle
        $this->driver->findElement(WebDriverBy::cssSelector('.inventory_item button'))->click();

        // Sepete git
        $this->driver->findElement(WebDriverBy::className('shopping_cart_link'))->click();

        // Ürünü kaldır
        $this->driver->findElement(WebDriverBy::cssSelector('.cart_button'))->click();

        // Sepetin boş olduğunu kontrol et
        $items = $this->driver->findElements(WebDriverBy::className('cart_item'));
        $this->assertCount(0, $items, 'Sepet boş olmalı');
        sleep(10);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
