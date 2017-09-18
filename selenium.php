<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SeleniumTests extends TestCase
{
    protected $webDriver;

    protected $url = "http://sharelogin.com";

    /**
     * A basic test example.
     *
     * @return void
     */

    public function setUp()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create("http://localhost:4444/wd/hub", $capabilities);
    }

    public function testTitle()
    {
        $this->webDriver->get($this->url);
        $this->assertContains("Sharelogin", $this->webDriver->getTitle());
    }

    public function testLoginLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('login'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/login");
    }

    public function testRegisterLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('register'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/register");
    }

    public function testSupportLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('support'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/support");
    }

    public function testLogoLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('logo'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/");
    }

    public function testHomeLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('home'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/");
    }

    public function testPrivacyLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('pp'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/privacy");
    }

    public function testTosLink(){
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::id('tos'))->click();
        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/tos");
    }

    public function testRegister()
    {
        $this->webDriver->get($this->url."/register");

        $this->webDriver->findElement(WebDriverBy::name('name'))->sendKeys('phpunit2');
        $this->webDriver->findElement(WebDriverBy::name('email'))->sendKeys('phpunitbitree2@gmail.com');
        $this->webDriver->findElement(WebDriverBy::name('password'))->sendKeys('123456');
        $this->webDriver->findElement(WebDriverBy::name('password_confirmation'))->sendKeys('123456');
        $this->webDriver->findElement(WebDriverBy::name('terms'))->click();
        $this->webDriver->findElement(WebDriverBy::id('registerSubmit'))->click();

        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/notactivated");
    }

    public function testLogin()
    {
        $this->webDriver->get($this->url."/login");

        $this->webDriver->findElement(WebDriverBy::name('email'))->sendKeys('phpunitbitree2@gmail.com');
        $this->webDriver->findElement(WebDriverBy::name('password'))->sendKeys('123456');
        $this->webDriver->findElement(WebDriverBy::id('submitLogin'))->click();

        $this->assertTrue($this->webDriver->getCurrentUrl() == $this->url."/notactivated");
    }

    public function tearDown()
    {
        //Close the browser
        $this->webDriver->quit();
    }

}
