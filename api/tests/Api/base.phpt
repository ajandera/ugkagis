<?php

namespace Test;

use App\Model\Users\Entity\Users;
use GuzzleHttp\Client;
use Nette\DI\Container;
use Tester\Assert;
use Kdyby\TesterExtras\HttpServer;
use Nette\Utils\Json;
use Tester\TestCase;
use Tester\TestCaseException;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class BasePresenterTest extends TestCase
{
    /** @var Container */
    private $container;

    /** @var HttpServer */
    public $server;

    /** @var \GuzzleHttp\Cookie\CookieJar() */
    public $cookieJar;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        $this->server = new HttpServer;
        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();
        $this->server->start(__DIR__ . '/../index.php');
        $client = new Client;
        $response = $client->post($this->server->getUrl() . 'v2/security/login', [
            'headers' => ['Content-type' => 'application/json'],
            'body' => json_encode([
                'username' => 'administrator@administrator.com',
                'password' => 'Ajandera123',
                'role' => Users::ADMIN
            ]),
            'cookies' => $this->cookieJar
        ]);
        Assert::same(201, $response->getStatusCode());
    }

    public function testGetSettings()
    {
        $client = new Client;
        $response = $client->get($this->server->getUrl() . 'v2/base/settings', [
            'cookies' => $this->cookieJar
        ]);
        Assert::same(200, $response->getStatusCode());
        $json = $response->getBody()->getContents();
        try {
            $res = Json::decode($json);
            Assert::type('object', $res);
        } catch (\Nette\Utils\JsonException $e) {
            $res = null;
        }

    }

    public function testPostEmail()
    {
        $client = new Client;
        $response = $client->post($this->server->getUrl() . 'v2/base/email', [
            'headers' => [
                'Content/type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'email' => 'test@gmail.com',
                'subject' => 'Subject',
                'body' => 'Text',
                'bcc' => [],
                'priority' => 1
            ],
            'cookies' => $this->cookieJar
        ]);
        Assert::same(201, $response->getStatusCode());
        $json = $response->getBody()->getContents();

        try {
            $res = Json::decode($json);
            Assert::type('bool', $res->success);
            Assert::same(true, $res->success);
        } catch (\Nette\Utils\JsonException $e) {
            $res = null;
        }

    }
}

$testCase = new BasePresenterTest($container);

try {
    $testCase->run();
} catch (TestCaseException $e) {
    echo $e->getMessage();
}
