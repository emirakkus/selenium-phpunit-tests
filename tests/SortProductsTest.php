<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SortProductsTest extends TestCase
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

    public function testSortProductsByPrice()
    {
        $this->driver->get('https://www.saucedemo.com/');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        // Dropdown'dan "Price (low to high)" seç
        $dropdown = $this->driver->findElement(WebDriverBy::className('product_sort_container'));
        $dropdown->sendKeys('Price (low to high)');

        // Fiyatları al
        $prices = $this->driver->findElements(WebDriverBy::className('inventory_item_price'));
        $values = array_map(fn($el) => (float) str_replace('$', '', $el->getText()), $prices);

        // Fiyatların artan sırada olduğunu doğrula
        $sorted = $values;
        sort($sorted);
        $this->assertEquals($sorted, $values, 'Ürünler fiyata göre artan sırada olmalı');
        Sleep(10);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
