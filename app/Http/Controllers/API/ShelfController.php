<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shelf\IndexShelf;
use App\Http\Requests\Shelf\StoreShelf;
use App\Http\Requests\Shelf\UpdateShelf;
use App\Http\Requests\Shelf\DestroyShelf;
use App\Models\Shelf;
use App\Repositories\Shelves;
use Illuminate\Http\Request;
use Savannabits\JetstreamInertiaGenerator\Helpers\ApiResponse;
use Savannabits\Pagetables\Column;
use Savannabits\Pagetables\Pagetables;
use Yajra\DataTables\DataTables;

class ShelfController  extends Controller
{
    private ApiResponse $api;
    private Shelves $repo;
    public function __construct(ApiResponse $apiResponse, Shelves $repo)
    {
        $this->api = $apiResponse;
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource (paginated).
     * @return columnsToQuery \Illuminate\Http\JsonResponse
     */
    public function index(IndexShelf $request)
    {
        $query = Shelf::query(); // You can extend this however you want.
        $cols = [
            Column::name('id')->title('Id')->sort()->searchable(),
            Column::name('name')->title('Name')->sort()->searchable(),
            Column::name('updated_at')->title('Updated At')->sort()->searchable(),
            
            Column::name('actions')->title('')->raw()
        ];
        $data = Pagetables::of($query)->columns($cols)->make(true);
        return $this->api->success()->message("List of Shelves")->payload($data)->send();
    }

    public function dt(Request $request) {
        $query = Shelf::query()->select(Shelf::getModel()->getTable().'.*'); // You can extend this however you want.
        return $this->repo::dt($query);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreShelf $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreShelf $request)
    {
        try {
            $data = $request->sanitizedObject();
            $shelf = $this->repo::store($data);
            return $this->api->success()->message('Shelf Created')->payload($shelf)->send();
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return $this->api->failed()->message($exception->getMessage())->payload([])->code(500)->send();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Shelf $shelf
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Shelf $shelf)
    {
        try {
            $payload = $this->repo::init($shelf)->show($request);
            return $this->api->success()
                        ->message("Shelf $shelf->id")
                        ->payload($payload)->send();
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return $this->api->failed()->message($exception->getMessage())->send();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateShelf $request
     * @param {$modelBaseName} $shelf
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateShelf $request, Shelf $shelf)
    {
        try {
            $data = $request->sanitizedObject();
            $res = $this->repo::init($shelf)->update($data);
            return $this->api->success()->message("Shelf has been updated")->payload($res)->code(200)->send();
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return $this->api->failed()->code(400)->message($exception->getMessage())->send();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Shelf $shelf
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(DestroyShelf $request, Shelf $shelf)
    {
        $res = $this->repo::init($shelf)->destroy();
        return $this->api->success()->message("Shelf has been deleted")->payload($res)->code(200)->send();
    }

}
