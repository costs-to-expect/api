<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class SubcategoryManagerTest extends TestCase
{
    /** @test */
    public function create_subcategory_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->postSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_subcategory_forbidden_when_category_id_invalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();

        $response = $this->postSubcategory(
            $resource_type_id,
            'wwwwwwwwww',
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(403);
    }
}
