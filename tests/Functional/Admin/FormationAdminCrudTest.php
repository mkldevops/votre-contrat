<?php

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\FormationCrudController;
use App\Repository\FormationRepository;
use App\Tests\Trait\GetUserTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class FormationAdminCrudTest extends WebTestCase
{
    use GetUserTrait;

    private AdminUrlGenerator $urlGenerator;

    private static KernelBrowser $client;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::$client = static::createClient();
        self::$client->loginUser(self::getUser('admin@gmail.com'));

        $this->urlGenerator = self::getContainer()->get(AdminUrlGenerator::class)->setAll([
            EA::CRUD_ACTION => 'index',
            EA::CRUD_CONTROLLER_FQCN => FormationCrudController::class,
        ]);
    }

    #[Test]
    public function index(): void
    {
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', 'Formation');
    }

    #[Test]
    public function view(): void
    {
        $entity = self::getContainer()->get(FormationRepository::class)->findOneBy([]);
        self::assertNotNull($entity);
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->setAction('detail')->setEntityId($entity->getId())->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', $entity->__toString());
    }

    #[Test]
    public function edit(): void
    {
        $entity = self::getContainer()->get(FormationRepository::class)->findOneBy([]);
        self::assertNotNull($entity);
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->setAction('edit')->setEntityId($entity->getId())->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', 'Modifier Formation');
    }
}
