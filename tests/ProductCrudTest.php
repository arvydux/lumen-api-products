<?php

namespace Tests;

use App\Models\Product;
use PHPUnit\Framework\Attributes\TestWith;

class ProductCrudTest extends TestCase
{
    public function test_product_added_successfully()
    {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Drink',
                'description'   => 'Fresh water',
                'sku'           => 'qwerty123456',
                'cost_price'    => '12.88',
                'selling_price' => '25',
                'vat'           => '1',
                'vat_rate'      => '10',
            ]
        )->seeJson([
            'message' => 'Product has been added',
            'status'  => 'success',
        ])->seeStatusCode(201);
    }

    public function test_list_all_products()
    {
        $this->json(
            'GET',
            '/api/products',
            []
        )->seeJson([
                'message' => 'All products listing',
                'status'  => 'success',
            ]
        )->seeStatusCode(200);
    }

    public function test_single_product_shows_successfully()
    {
        $productData =
            [
                'name'          => 'Big Mac',
                'description'   => 'McDonald food',
                'sku'           => 'vvdinin6454',
                'cost_price'    => '59.88',
                'selling_price' => '9.99',
                'vat'           => '1',
                'vat_rate'      => '20',
            ];
        $product = Product::create(
            $productData
        );

        $this->json(
            'GET',
            "api/product/$product->id"
        )->seeJson([
                'message' => 'Single product',
                'status'  => 'success',
            ]
        )->seeJsonStructure([
                'data' => [
                    'name',
                    'description',
                    'sku',
                    'cost_price',
                    'selling_price',
                    'vat',
                    'vat_rate',
                ]
            ]
        )->seeStatusCode(200);
    }

    public function test_product_updated_successfully()
    {
        $productData =
            [
                'name'          => 'Intel conputer',
                'description'   => 'PC',
                'sku'           => 'vy3v5y346vyf',
                'cost_price'    => '1021.88',
                'selling_price' => '3245.99',
                'vat'           => '1',
                'vat_rate'      => '21',
            ];

        $productNewData =
            [
                'name'          => 'Google Pixel',
                'description'   => 'Intel computer',
                'sku'           => 'vy3v5y346vyf',
                'cost_price'    => '564.88',
                'selling_price' => '245.99',
                'vat'           => '1',
                'vat_rate'      => '21',
            ];

        $product = Product::create(
            $productData
        );

        $this->json(
            'PUT',
            "api/product/" . $product->id, $productNewData
        )->seeJson([
                'message' => 'Product has been updated',
                'status'  => 'success',
            ]
        )->seeJsonStructure([
                'data' => [
                    'name',
                    'description',
                    'sku',
                    'cost_price',
                    'selling_price',
                    'vat',
                    'vat_rate',
                ]
            ]
        )->seeStatusCode(200);
    }

    public function test_product_deleted_successfully()
    {
        $productData =
            [
                'name'          => 'BMW',
                'description'   => 'Fast car',
                'sku'           => 'asd123456ū90',
                'cost_price'    => '30',
                'selling_price' => '79.99',
                'vat'           => '1',
                'vat_rate'      => '20',
            ];
        $product = Product::create(
            $productData
        );

        $this->json(
            'DELETE',
            "api/product/$product->id"
        )->seeStatusCode(204);
    }

    public function test_validation_required_fields_for_product()
    {
        $this->json(
            'POST',
            '/api/product',
            []
        )->seeJson([
            'message' => 'The given data was invalid.',
            'status'  => 'error',
            'data'    => null,
            'errors'  => [
                'name'          => ['The name field is required.'],
                'description'   => ['The description field is required.'],
                'sku'           => ['The sku field is required.'],
                'cost_price'    => ['The cost price field is required.'],
                'selling_price' => ['The selling price field is required.'],
            ]
        ])->seeStatusCode(422);
    }

    public function test_validation_required_vat_rate_field_when_vat_field_is_true()
    {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Book',
                'description'   => 'Harry Potter',
                'sku'           => 'fer35fgege',
                'cost_price'    => '567.89',
                'selling_price' => '4647.97',
                'vat'           => '1',
            ]
        )->seeJson([
            'message' => 'The given data was invalid.',
            'status'  => 'error',
            'data'    => null,
            'errors'  => [
                'vat_rate' => ['The vat rate field is required when vat is present.'],
            ]
        ])->seeStatusCode(422);
    }

    public function test_validation_description_field_is_limited_to_max_140_char()
    {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Book',
                'description'   => str_repeat('A', 145),
                'sku'           => 'fer35fgege',
                'cost_price'    => '567.89',
                'selling_price' => '4647.97',

            ]
        )->seeJson([
            'message' => 'The given data was invalid.',
            'status'  => 'error',
            'data'    => null,
            'errors'  => [
                'description' => ['The description must not be greater than 140 characters.'],
            ]
        ])->seeStatusCode(422);
    }

    public function test_validation_sku_field_is_limited_to_max_13_digit_alphanumeric_only()
    {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Book',
                'description'   => 'Harry Potter',
                'sku'           => 'fer3gygt7gugtbt466-=+_',
                'cost_price'    => '567.89',
                'selling_price' => '4647.97',

            ]
        )->seeJson([
            'message' => 'The given data was invalid.',
            'status'  => 'error',
            'data'    => null,
            'errors'  => [
                'sku' => [
                    "The sku must only contain letters and numbers.",
                    "The sku must not be greater than 13 characters."
                ],
            ]
        ])->seeStatusCode(422);
    }

    #[TestWith(['10'])]
    #[TestWith(['20'])]
    #[TestWith(['21'])]
    public function test_validation_vat_rate_field_accepts_valid_values_only($vatRate
    ) {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Book',
                'description'   => 'Harry Potter',
                'sku'           => 'fer3gygt7gu',
                'cost_price'    => '567.89',
                'selling_price' => '4647.97',
                'vat'           => '1',
                'vat_rate'      => $vatRate,
            ]
        )->seeJson([
            'message' => 'Product has been added',
            'status'  => 'success',
        ])->seeStatusCode(201);
    }

    #[TestWith(['9'])]
    #[TestWith(['11'])]
    #[TestWith(['22'])]
    public function test_validation_vat_rate_field_do_not_accepts_invalid_values($vatRate
    ) {
        $this->json(
            'POST',
            '/api/product',
            [
                'name'          => 'Book',
                'description'   => 'Harry Potter',
                'sku'           => 'fer3gygt7gu',
                'cost_price'    => '567.89',
                'selling_price' => '4647.97',
                'vat'           => '1',
                'vat_rate'      => $vatRate,
            ]
        )->seeJson([
            'message' => 'The given data was invalid.',
            'status'  => 'error',
            'data'    => null,
            'errors'  => [
                'vat_rate' => ["The selected vat rate is invalid.",],
            ]
        ])->seeStatusCode(422);
    }
}
