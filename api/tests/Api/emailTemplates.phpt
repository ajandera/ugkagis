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
class EmailTemplatesPresenterTest extends TestCase
{
    /** @var Container  */
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

    public function testGet()
    {
        $client = new Client;
        $response = $client->get($this->server->getUrl() . 'v2/email-templates', [
            'cookies' => $this->cookieJar
        ]);

        Assert::same(200, $response->getStatusCode());
        $json = $response->getBody()->getContents();

        try {
            $res = Json::decode($json);
            Assert::type('array', $res->templates);
        } catch (\Nette\Utils\JsonException $e) {
            $res = null;
        }
    }
}

$testCase = new EmailTemplatesPresenterTest($container);

try {
    $testCase->run();
} catch (TestCaseException $e) {
    echo $e->getMessage();
}
