<?php

namespace App\Http\Controllers\View;

use App\HttpOptionResponse\Auth\PermittedResourceType;
use App\HttpOptionResponse\Auth\PermittedResourceTypeResource;
use App\HttpOptionResponse\Auth\PermittedResourceTypeResources;
use App\HttpOptionResponse\Auth\PermittedResourceTypes;
use App\HttpOptionResponse\Auth\RequestDelete;
use App\HttpOptionResponse\Auth\RequestResourceDelete;
use App\HttpOptionResponse\Auth\RequestResourceTypeDelete;
use App\HttpResponse\Response;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\ResourceType;
use App\HttpOptionResponse\Auth\Check;
use App\HttpOptionResponse\Auth\CreateNewPassword;
use App\HttpOptionResponse\Auth\CreatePassword;
use App\HttpOptionResponse\Auth\Login;
use App\HttpOptionResponse\Auth\Register;
use App\HttpOptionResponse\Auth\UpdatePassword;
use App\HttpOptionResponse\Auth\UpdateProfile;
use App\Transformer\PermittedResourceType as PermittedResourceTypeTransformer;
use App\Transformer\Resource as ResourceTransformer;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends \Illuminate\Routing\Controller
{
    protected \App\HttpRequest\Hash $hash;

    public function __construct()
    {
        $this->hash = new \App\HttpRequest\Hash();
    }

    public function check(): Http\JsonResponse
    {
        return response()->json(['auth' => Auth::guard('api')->check()]);
    }

    public function optionsCheck(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new Check(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsCreatePassword(): Http\JsonResponse
    {
        $response = new CreatePassword([]);

        return $response->create()->response();
    }
    public function optionsCreateNewPassword(): Http\JsonResponse
    {
        $response = new CreateNewPassword([]);

        return $response->create()->response();
    }

    public function optionsForgotPassword(): Http\JsonResponse
    {
        $response = new \App\HttpOptionResponse\Auth\ForgotPassword([]);

        return $response->create()->response();
    }

    public function optionsLogin(): Http\JsonResponse
    {
        $response = new Login([]);

        return $response->create()->response();
    }

    public function permittedResourceType($permitted_resource_type_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);
        $permitted_resource_type = (new ResourceType())->single(
            $permitted_resource_type_id,
            $permitted_resource_types
        );

        if ($permitted_resource_type === null) {
            return Response::notFound(trans('entities.resource-type'));
        }

        return response()->json(
            (new PermittedResourceTypeTransformer($permitted_resource_type))->asArray(),
        );
    }

    public function permittedResourceTypesResource($permitted_resource_type_id, $resource_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);
        if (in_array($permitted_resource_type_id, $permitted_resource_types, true) === false) {
            return Response::notFound(trans('entities.resource-type'));
        }

        $resource = (new Resource())->single(
            $permitted_resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return Response::notFound(trans('entities.resource'));
        }

        return response()->json(
            (new ResourceTransformer($resource))->asArray(),
        );
    }

    public function permittedResourceTypesResources($permitted_resource_type_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);
        if (in_array($permitted_resource_type_id, $permitted_resource_types, true) === false) {
            return Response::notFound(trans('entities.resource-type'));
        }

        $resources = (new Resource())->paginatedCollection(
            $permitted_resource_type_id,
            0,
            100
        );

        $collection = array_map(
            static function ($resource) {
                return (new ResourceTransformer($resource))->asArray();
            },
            $resources
        );

        return response()->json($collection);
    }

    public function permittedResourceTypes(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);
        $resource_types = (new ResourceType())->paginatedCollection(
            $permitted_resource_types,
            0,
            100
        );

        $collection = array_map(
            static function ($resource_type) {
                return (new PermittedResourceTypeTransformer($resource_type))->asArray();
            },
            $resource_types
        );

        return response()->json($collection);
    }

    public function optionsPermittedResourceType(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new PermittedResourceType(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsPermittedResourceTypeResource(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new PermittedResourceTypeResource(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsPermittedResourceTypeResources(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new PermittedResourceTypeResources(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsPermittedResourceTypes(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new PermittedResourceTypes(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsRegister(): Http\JsonResponse
    {
        $response = new Register([]);

        return $response->create()->response();
    }

    public function optionsRequestDelete(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new RequestDelete(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsRequestResourceDelete($permitted_resource_type_id, $resource_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);
        if (in_array($permitted_resource_type_id, $permitted_resource_types, true) === false) {
            return Response::notFound(trans('entities.resource-type'));
        }

        $resource = (new Resource())->single(
            $permitted_resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return Response::notFound(trans('entities.resource'));
        }

        $response = new RequestResourceDelete(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsRequestResourceTypeDelete($permitted_resource_type_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $permitted_resource_types = (new Permission())->permittedResourceTypesForUser($user->id);

        if (in_array($permitted_resource_type_id, $permitted_resource_types, true) === false) {
            return Response::notFound(trans('entities.resource-type'));
        }

        $response = new RequestResourceTypeDelete(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsUpdateProfile(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new UpdateProfile(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsUpdatePassword(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new UpdatePassword(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function user(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $tokens = [];
        foreach ($user->tokens as $token) {
            $tokens[] = [
                'id' => $token->id,
                'name' => $token->name,
                'token' => $token->token,
                'created' => $token->created_at,
                'last_used_at' => $token->last_used_at
            ];
        }

        $user = [
            'id' => $this->hash->user()->encode($user->id),
            'name' => $user->name,
            'email' => $user->email,
            'tokens' => [
                'uri' => route('auth.user.token.list', [], false),
                'count' => count($tokens),
                'collection' => $tokens
            ]
        ];

        return response()->json($user);
    }

    public function tokens(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $tokens = [];
        foreach ($user->tokens as $token) {
            $tokens[] = [
                'id' => $token->id,
                'name' => $token->name,
                'token' => $token->token,
                'created' => $token->created_at,
                'last_used_at' => $token->last_used_at
            ];
        }

        return response()->json($tokens);
    }

    public function token($token_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $tokens = [];
        foreach ($user->tokens as $token) {
            $tokens[$token->id] = [
                'id' => $token->id,
                'name' => $token->name,
                'token' => $token->token,
                'created' => $token->created_at,
                'last_used_at' => $token->last_used_at
            ];
        }

        if (array_key_exists($token_id, $tokens)) {
            return response()->json($tokens[$token_id]);
        }

        return Response::notFound();
    }

    public function optionsUser(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new \App\HttpOptionResponse\Auth\User(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsTokens(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new \App\HttpOptionResponse\Auth\Tokens(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }

    public function optionsToken(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user === null) {
            return Response::authenticationRequired();
        }

        $response = new \App\HttpOptionResponse\Auth\Token(['view'=> true, 'manage'=> true]);

        return $response->create()->response();
    }
}
