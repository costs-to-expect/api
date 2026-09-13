<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    public function testOptionsRequestForCreatePassword(): void
    {
        $response = $this->fetchOptionsForCreatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/create-password.json');
    }

    public function testOptionsRequestForMigrateBudgetProRequestDelete(): void
    {
        $response = $this->fetchOptionsForMigrateBudgetProRequestDelete();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/migrate-budget-pro-request-delete.json');
    }

    public function testOptionsRequestForRegister(): void
    {
        $response = $this->fetchOptionsForRegister();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/register.json');
    }

    public function testOptionsRequestForUpdatePassword(): void
    {
        $response = $this->fetchOptionsForUpdatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-password.json');
    }

    public function testOptionsRequestForUpdateProfile(): void
    {
        $response = $this->fetchOptionsForUpdateProfile();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-profile.json');
    }

    public function testOptionsRequestForCheck(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForCheck();
        $response->assertStatus(200);
    }

    public function testOptionsRequestForCreateNewPassword(): void
    {
        $response = $this->fetchOptionsForCreateNewPassword();
        $response->assertStatus(200);
    }

    public function testOptionsRequestForForgotPassword(): void
    {
        $response = $this->fetchOptionsForForgotPassword();
        $response->assertStatus(200);
    }

    public function testUserSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToAuthUser();

        $response->assertStatus(200);
        $this->assertArrayHasKey('id', $response->json());
        $this->assertArrayHasKey('tokens', $response->json());
    }

    public function testUserFailsUnauthenticated(): void
    {
        $response = $this->getToAuthUser();
        $response->assertStatus(403);
    }

    public function testOptionsRequestForAuthUser(): void
    {
        $response = $this->fetchOptionsForAuthUser();
        $response->assertStatus(200);
    }

    public function testPermittedResourceTypesSuccess(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToPermittedResourceTypeList();

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function testOptionsRequestForPermittedResourceTypes(): void
    {
        $response = $this->fetchOptionsForPermittedResourceTypeCollection();
        $response->assertStatus(200);
    }

    public function testPermittedResourceTypeShowSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToPermittedResourceTypeShow(['permitted_resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
    }

    public function testPermittedResourceTypeShowFailsNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type = \App\Models\ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type === null) {
            $this->fail('Unable to fetch a resource type for testing in');
        }

        $other_resource_type_id = (new \App\HttpRequest\Hash())->encode('resource-type', $resource_type->id);

        $response = $this->getToPermittedResourceTypeShow(['permitted_resource_type_id' => $other_resource_type_id]);

        $response->assertStatus(404);
    }

    public function testOptionsRequestForPermittedResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForPermittedResourceType(['permitted_resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }

    public function testPermittedResourceTypeResourcesListSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToPermittedResourceTypeResourcesList(['permitted_resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function testOptionsRequestForPermittedResourceTypeResources(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForPermittedResourceTypeResourcesCollection(['permitted_resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }

    public function testPermittedResourceTypeResourceShowSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToPermittedResourceTypeResourceShow([
            'permitted_resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
    }

    public function testOptionsRequestForPermittedResourceTypeResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForPermittedResourceTypeResource([
            'permitted_resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);
        $response->assertStatus(200);
    }

    public function testOptionsRequestForAuthRequestDelete(): void
    {
        $response = $this->fetchOptionsForAuthRequestDelete();
        $response->assertStatus(200);
    }

    public function testOptionsRequestForAuthRequestResourceDelete(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForAuthRequestResourceDelete([
            'permitted_resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);
        $response->assertStatus(200);
    }

    public function testOptionsRequestForAuthRequestResourceTypeDelete(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForAuthRequestResourceTypeDelete(['permitted_resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }

    public function testTokensListSuccess(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $user->createToken('a-device');

        $response = $this->getToTokenList();

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function testOptionsRequestForTokens(): void
    {
        $response = $this->fetchOptionsForTokenCollection();
        $response->assertStatus(200);
    }

    public function testTokenShowSuccess(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $token = $user->createToken('a-device');

        $response = $this->getToTokenShow(['token_id' => $token->accessToken->id]);

        $response->assertStatus(200);
        $this->assertEquals('a-device', $response->json('name'));
    }

    public function testTokenShowFailsNotFound(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToTokenShow(['token_id' => 999999]);

        $response->assertStatus(404);
    }

    public function testOptionsRequestForToken(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $token = $user->createToken('a-device');

        $response = $this->fetchOptionsForToken(['token_id' => $token->accessToken->id]);
        $response->assertStatus(200);
    }
}
