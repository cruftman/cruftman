<?php declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Location;
use App\Factory\LocationFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;


class LocationApiTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @return array<string,mixed>
     */
    public static function getLocationsIriTemplate(): array
    {
        return [
            "@type" => "IriTemplate",
            "mapping" => [
                [
                    "@type" => "IriTemplateMapping",
                    "property" => "ref",
                    "required" => false,
                    "variable" => "ref"
                ],
                [
                    "@type" => "IriTemplateMapping",
                    "property" => "children",
                    "required" => false,
                    "variable" => "exists[children]"
                ],
                [
                    "@type" => "IriTemplateMapping",
                    "property" => "parent",
                    "required" => false,
                    "variable" => "exists[parent]"
                ],
                [
                    "@type" => "IriTemplateMapping",
                    "property" => "stocktakeItemLocations",
                    "required" => false,
                    "variable" => "exists[stocktakeItemLocations]"
                ]
            ],
            "template" => "/api/locations{?ref,exists[children],exists[parent],exists[stocktakeItemLocations]}",
            "variableRepresentation" => "BasicRepresentation"
        ];
    }

    /**
     * @return \Generator<array{
     *      sequence: list<array{ref: string, comment: ?string, parent?: string}>,
     *      request: array{0: string, 1: string, 2?: array<string,mixed>},
     *      expect: array<mixed>,
     *  }>
     */
    public static function provGetLocations(): \Generator
    {
        $acme100rooms = [
            ...array_map(
                fn (int $n): array => [
                    'ref' => "ACME/{$n}",
                    'comment' => "ACME Room {$n}",
                ],
                range(101,200)
            ),
        ];

        $expect100rooms = [
            ...array_map(
                fn(int $id): array => [
                    "@id" => "/api/locations/{$id}",
                    "@type" => "Location",
                    "children" => [],
                    "id" => $id,
                    "stocktakeItemLocations" => [],
                    ...$acme100rooms[$id-1],
                ],
                range(1, 100)
            ),
        ];

        $expect = [
            '@context' => '/api/contexts/Location',
            '@id' => '/api/locations',
            '@type' => 'Collection',
        ];

        $iriTemplate = self::getLocationsIriTemplate();

        yield '#01: empty' => [
            'sequence' => [],
            'request' => ['GET', '/api/locations'],
            'expect' => array_merge($expect, [
                'totalItems' => 0,
                'member' => [],
                'search' => $iriTemplate,
            ]),
        ];

        yield '#02: single entity with null comment' => [
            'sequence' => [
                ['ref' => 'ACME/236', 'comment' => null],
            ],
            'request' => ['GET', '/api/locations'],
            'expect' => array_merge($expect, [
                'totalItems' => 1,
                'member' => [
                    [
                        '@id' => '/api/locations/1',
                        '@type' => 'Location',
                        'id' => 1,
                        'ref' => 'ACME/236',
                        'children' => [],
                        'stocktakeItemLocations' => [],
                    ]
                ],
                'search' => $iriTemplate,
            ]),
        ];

        yield '#03: few entities with parent/child relations' => [
            'sequence' => [
                ['ref' => 'ACME', 'comment' => 'ACME Building'],
                ['ref' => 'ACME/123', 'comment' => 'Room 123 in the ACME Building', 'parent' => 'ACME'],
                ['ref' => 'ACME/124', 'comment' => 'Room 124 in the ACME Building', 'parent' => 'ACME'],
                ['ref' => 'PENT', 'comment' => 'Pentagon house'],
                ['ref' => 'PENT/321', 'comment' => 'Room 321 in the Pentagon House', 'parent' => 'PENT'],
                ['ref' => 'FOO', 'comment' => 'Room FOO somewhere else'],
            ],
            'request' => ['GET', '/api/locations'],
            'expect' => array_merge($expect, [
                'totalItems' => 6,
                'member' => [
                    [
                        '@id' => '/api/locations/1',
                        '@type' => 'Location',
                        'id' => 1,
                        'ref' => 'ACME',
                        'comment' => 'ACME Building',
                        'children' => [
                            '/api/locations/2',
                            '/api/locations/3',
                        ],
                        'stocktakeItemLocations' => [],
                    ],
                    [
                        '@id' => '/api/locations/2',
                        '@type' => 'Location',
                        'id' => 2,
                        'ref' => 'ACME/123',
                        'comment' => 'Room 123 in the ACME Building',
                        'parent' => '/api/locations/1',
                        'children' => [],
                        'stocktakeItemLocations' => [],
                    ],
                    [
                        '@id' => '/api/locations/3',
                        '@type' => 'Location',
                        'id' => 3,
                        'ref' => 'ACME/124',
                        'comment' => 'Room 124 in the ACME Building',
                        'parent' => '/api/locations/1',
                        'children' => [],
                        'stocktakeItemLocations' => [],
                    ],
                    [
                        '@id' => '/api/locations/4',
                        '@type' => 'Location',
                        'id' => 4,
                        'ref' => 'PENT',
                        'comment' => 'Pentagon house',
                        'children' => [
                            '/api/locations/5',
                        ],
                        'stocktakeItemLocations' => [],
                    ],
                    [
                        '@id' => '/api/locations/5',
                        '@type' => 'Location',
                        'id' => 5,
                        'ref' => 'PENT/321',
                        'comment' => 'Room 321 in the Pentagon House',
                        'parent' => '/api/locations/4',
                        'children' => [],
                        'stocktakeItemLocations' => [],
                    ],
                    [
                        '@id' => '/api/locations/6',
                        '@type' => 'Location',
                        'id' => 6,
                        'ref' => 'FOO',
                        'comment' => 'Room FOO somewhere else',
                        'children' => [],
                        'stocktakeItemLocations' => [],
                    ],
                ],
                'search' => $iriTemplate,
            ]),
        ];

        yield '#04: get page=1 of 100 rooms' => [
            'sequence' => $acme100rooms,
            'request' => ['GET', '/api/locations'],
            'expect' => array_merge($expect, [
                'totalItems' => count($acme100rooms),
                'view' => [
                    '@id' => '/api/locations?page=1',
                    '@type' => 'PartialCollectionView',
                    'first' => '/api/locations?page=1',
                    'last' => '/api/locations?page=4',
                    'next' => '/api/locations?page=2',
                ],
                'member' => array_slice($expect100rooms, 0, 30),
                'search' => $iriTemplate,
            ]),
        ];

        yield '#05: get page=2 of 100 rooms' => [
            'sequence' => $acme100rooms,
            'request' => ['GET', '/api/locations?page=2'],
            'expect' => array_merge($expect, [
                'totalItems' => count($acme100rooms),
                'view' => [
                    '@id' => '/api/locations?page=2',
                    '@type' => 'PartialCollectionView',
                    'first' => '/api/locations?page=1',
                    'last' => '/api/locations?page=4',
                    'next' => '/api/locations?page=3',
                    'previous' => '/api/locations?page=1',
                ],
                'search' => $iriTemplate,
                'member' => array_slice($expect100rooms, 30, 30),
            ]),
        ];
    }

    /**
     * @param list<array{id:int, ref: string, comment: ?string, parent?: string}> $sequence
     * @param array{0: string, 1: string, 2?: array<string, mixed>} $request
     * @param array<mixed> $expect
     */
    #[DataProvider('provGetLocations')]
    public function testGetLocations(array $sequence, array $request, array $expect): void
    {
        /** @var \Zenstruck\Foundry\Persistence\Proxy<Location>[] */
        $objects = LocationFactory::createSequence(array_map(
            fn (array $entry): array => array_diff_key($entry, [ 'parent' => true ]), $sequence
        ));

        foreach ($objects as $object) {
            $object->_save();
        }

        for ($i = 0; $i < count($sequence); ++$i) {
            $item = $sequence[$i];
            if (null !== ($parentRef = $item['parent'] ?? null)) {
                $parents = array_values(array_filter($objects, fn ($l): bool => ($l->getRef() === $parentRef)));
                if (1 === count($parents)) {
                    $objects[$i]->_set('parent', $parents[0]->_real())->_save();
                }
            }
        }

        if (count($request) >= 3) {
            static::createClient()->request($request[0], $request[1], $request[2]);
        } else {
            static::createClient()->request($request[0], $request[1]);
        }

        $this->assertResponseIsSuccessful();

        $this->assertJsonEquals($expect);
    }

    public function testPostLocation(): void
    {
        static::createClient()->request('POST', '/api/locations', [
            'json' => [
                'ref' => 'ACME/321',
                'comment' => 'Room 321 in ACME building',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonEquals([
            "@context" => "/api/contexts/Location",
            "@id" => "/api/locations/1",
            "@type" => "Location",
            "children" => [],
            "comment" => "Room 321 in ACME building",
            "id" => 1,
            "ref" => "ACME/321",
            "stocktakeItemLocations" => []
        ]);
    }
}
