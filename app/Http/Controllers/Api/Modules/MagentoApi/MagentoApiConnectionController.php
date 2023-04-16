<?php

namespace App\Http\Controllers\Api\Modules\MagentoApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\MagentoConnectionResource;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionDestroyRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionIndexRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionStoreRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionUpdateRequest;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Tags\Tag;

class MagentoApiConnectionController extends Controller
{
    public function index(MagentoApiConnectionIndexRequest $request): AnonymousResourceCollection
    {
        $query = MagentoConnection::getSpatieQueryBuilder();
        return MagentoConnectionResource::collection($this->getPaginatedResult($query));
    }

    public function store(MagentoApiConnectionStoreRequest $request): MagentoConnectionResource
    {
        $connection = new MagentoConnection();
        $connection->fill($request->only($connection->getFillable()));
        $connection->save();

        if($request->tag) {
            $tag = Tag::findOrCreate($request->tag);
            $connection->attachTag($tag);
            $connection->inventory_source_warehouse_tag_id = $tag->id;
            $connection->save();
        }

        return new MagentoConnectionResource($connection);
    }

    public function update(MagentoApiConnectionUpdateRequest $request, MagentoConnection $connection)
    {
        $connection->fill($request->validated());

        if ($request->tag) {
            $tag = Tag::findOrCreate($request->tag);
            $connection->inventory_source_warehouse_tag_id = $tag->id;

            $connection->tags()->sync([$tag->id]);
        }

        return new MagentoConnectionResource($connection);
    }

    public function destroy(MagentoApiConnectionDestroyRequest $request, MagentoConnection $connection)
    {
        $connection->delete();
        return response('ok');
    }
}
