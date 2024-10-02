<?php declare(strict_types=1);

namespace App\Tests\api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;


class LocationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
}
