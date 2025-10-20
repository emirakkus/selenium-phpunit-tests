<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

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

    public function testAddToCartAndCheckout()
    {
        // 1️⃣ Siteye git
        $this->driver->get('https://www.saucedemo.com/');

        // 2️⃣ Login işlemi
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys('standard_user');
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('secret_sauce');
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();

        // 3️⃣ İlk ürünü sepete ekle
        $this->driver->findElement(WebDriverBy::cssSelector('.inventory_item button'))->click();

        // 4️⃣ Sepete git
        $this->driver->findElement(WebDriverBy::className('shopping_cart_link'))->click();

        // 5️⃣ Sepette ürün olduğunu doğrula
        $items = $this->driver->findElements(WebDriverBy::className('cart_item'));
        $this->assertCount(1, $items, 'Sepette 1 ürün olmalı');

        // 6️⃣ Checkout'a geç
        $this->driver->findElement(WebDriverBy::id('checkout'))->click();

        // 7️⃣ Bilgileri doldur
        $this->driver->findElement(WebDriverBy::id('first-name'))->sendKeys('Emir');
        $this->driver->findElement(WebDriverBy::id('last-name'))->sendKeys('Akkus');
        $this->driver->findElement(WebDriverBy::id('postal-code'))->sendKeys('34000');
        $this->driver->findElement(WebDriverBy::id('continue'))->click();

        // 8️⃣ Finish butonuna bas ve satın almayı tamamla
        $this->driver->findElement(WebDriverBy::id('finish'))->click();

        // 9️⃣ Sipariş onay mesajını doğrula
        $confirmation = $this->driver->findElement(WebDriverBy::className('complete-header'))->getText();
        $this->assertStringContainsString('Thank you for your order!', $confirmation, 'Sipariş başarıyla tamamlanmalı');

        // Görsel olarak sonuçları görmek için kısa bekleme
        sleep(5);
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}
