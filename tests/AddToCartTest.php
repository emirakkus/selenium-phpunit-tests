<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class AddToCartTest extends TestCase
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

    public function testAddToCart()
    {
        $this->driver->get('https://www.saucedemo.com/');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.inventory_item:first-child .btn_inventory')));
        
        // Sepete ürün ekle
        $this->driver->findElement(WebDriverBy::cssSelector('.inventory_item:first-child .btn_inventory'))->click();

        // Sepete git
        $this->driver->findElement(WebDriverBy::className('shopping_cart_link'))->click();

        $wait->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('cart_item')));
        $cartItems = $this->driver->findElements(WebDriverBy::className('cart_item'));

        $this->assertCount(1, $cartItems, "Sepette 1 ürün olmalı");
        sleep(10);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
