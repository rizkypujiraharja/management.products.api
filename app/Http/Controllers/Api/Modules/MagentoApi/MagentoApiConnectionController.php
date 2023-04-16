<?php

namespace App\Http\Controllers\Api\Modules\MagentoApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\MagentoConnectionResource;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionDestroyRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionIndexRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionStoreRequest;
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
        $config = new MagentoConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        if($request->tag) {
            $tag = Tag::findOrCreate($request->tag);
            $config->attachTag($tag);
            $config->inventory_source_warehouse_tag_id = $tag->id;
            $config->save();
        }

        return new MagentoConnectionResource($config);
    }

    public function destroy(MagentoApiConnectionDestroyRequest $request, $id)
    {
        $connection = MagentoConnection::findOrFail($id);
        $connection->delete();
        return response('ok');
    }
}
