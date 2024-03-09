<?php

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\CompanyCrudController;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Tests\Trait\GetUserTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CompanyAdminCrudTest extends WebTestCase
{
    use GetUserTrait;

    private AdminUrlGenerator $urlGenerator;

    private static KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::$client = static::createClient();
        self::$client->loginUser(self::getUser('admin@gmail.com'));

        $this->urlGenerator = self::getContainer()->get(AdminUrlGenerator::class)->setAll([
            EA::CRUD_ACTION => 'index',
            EA::CRUD_CONTROLLER_FQCN => CompanyCrudController::class,
            EA::ENTITY_FQCN => Company::class,
            EA::ENTITY_ID => null,
        ]);
    }

    #[Test]
    public function index(): void
    {
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', 'Société');
    }

    #[Test]
    public function view(): void
    {
        $entity = self::getContainer()->get(CompanyRepository::class)->findOneBy([]);
        self::assertInstanceOf(Company::class, $entity);
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->setAction('detail')->setEntityId($entity->getId())->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', $entity->__toString());
    }

    #[Test]
    public function edit(): void
    {
        $entity = self::getContainer()->get(CompanyRepository::class)->findOneBy([]);
        self::assertInstanceOf(Company::class, $entity);
        self::$client->request(Request::METHOD_GET, $this->urlGenerator->setAction('edit')->setEntityId($entity->getId())->generateUrl());

        static::assertResponseIsSuccessful();
        static::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        static::assertSelectorTextContains('h1.title', 'Modifier Société');
    }
}
