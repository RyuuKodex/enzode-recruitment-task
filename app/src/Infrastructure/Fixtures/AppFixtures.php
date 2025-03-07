<?php

declare(strict_types=1);

namespace App\Infrastructure\Fixtures;

use App\Domain\Entity\Attribute;
use App\Domain\Entity\Category;
use App\Domain\Entity\Currency;
use App\Domain\Entity\Product;
use App\Domain\Entity\ProductAttribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $currencies = [];
        $currencyCodes = ['USD', 'EUR', 'GBP'];
        foreach ($currencyCodes as $code) {
            $currency = new Currency($code);
            $manager->persist($currency);
            $currencies[$code] = $currency;
        }

        $categories = [];
        for ($i = 1; $i <= 5; ++$i) {
            $category = new Category($faker->word);
            $manager->persist($category);
            $categories[] = $category;
        }

        $attributes = [];
        $attributeCodes = ['COL', 'SIZ', 'MAT'];
        foreach ($attributeCodes as $code) {
            $attribute = new Attribute($code);
            $manager->persist($attribute);
            $attributes[$code] = $attribute;
        }

        $products = [];
        for ($i = 1; $i <= 20; ++$i) {
            $currencyCode = $currencyCodes[array_rand($currencyCodes)];
            $product = new Product(
                $faker->word,
                $faker->sentence,
                $faker->randomFloat(2, 10, 1000),
                $currencies[$currencyCode],
                $categories[array_rand($categories)]
            );
            $manager->persist($product);
            $products[] = $product;
        }

        foreach ($products as $product) {
            foreach ($attributes as $attribute) {
                if (rand(0, 1)) {
                    $productAttribute = new ProductAttribute($product, $attribute, $faker->word);
                    $manager->persist($productAttribute);
                }
            }
        }

        $manager->flush();
    }
}
