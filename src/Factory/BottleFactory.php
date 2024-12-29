<?php

namespace App\Factory;

use App\Entity\Bottle;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Bottle>
 */
final class BottleFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Bottle::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->words(3, true),
            'type' => self::faker()->randomElement(['Wine', 'Beer', 'Spirit', 'Soft']),
            'quantity' => self::faker()->numberBetween(0, 100),
            'buyingPrice' => (string)self::faker()->randomFloat(2, 1, 99.99),
            'sellingPrice' => (string)self::faker()->randomFloat(2, 2, 199.99),
            'minQuantity' => self::faker()->numberBetween(5, 20),
            'volume' => self::faker()->optional(0.9)->numberBetween(25, 100),
            'alcoholDegree' => self::faker()->boolean(80) ?
                (string)self::faker()->randomFloat(1, 0, 99.9) :
                null,
            'reference' => self::faker()->optional(0.8)->regexify('[A-Z]{2}[0-9]{6}'),
            'supplier' => self::faker()->optional(0.7)->company(),
            'category' => CategoryFactory::new(),
            'organization' => OrganizationFactory::new()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Bottle $bottle): void {})
            ;
    }
}
