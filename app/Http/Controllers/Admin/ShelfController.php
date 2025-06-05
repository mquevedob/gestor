<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shelf\IndexShelf;
use App\Http\Requests\Shelf\StoreShelf;
use App\Http\Requests\Shelf\UpdateShelf;
use App\Http\Requests\Shelf\DestroyShelf;
use App\Models\Shelf;
use App\Repositories\Shelves;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\Html\Column;

class ShelfController  extends Controller
{
    private Shelves $repo;
    public function __construct(Shelves $repo)
    {
        $this->repo = $repo;
    }

    /**
    * Display a listing of the resource.
    *
    * @param Request $request
    * @return  \Inertia\Response
    * @throws \Illuminate\Auth\Access\AuthorizationException
    */
    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Shelf::class);
        return Inertia::render('Shelves/Index',[
            "can" => [
                "viewAny" => \Auth::user()->can('viewAny', Shelf::class),
                "create" => \Auth::user()->can('create', Shelf::class),
            ],
            "columns" => $this->repo::dtColumns(),
        ]);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Inertia\Response
    */
    public function create()
    {
        $this->authorize('create', Shelf::class);
        return Inertia::render("Shelves/Create",[
            "can" => [
            "viewAny" => \Auth::user()->can('viewAny', Shelf::class),
            "create" => \Auth::user()->can('create', Shelf::class),
            ]
        ]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param StoreShelf $request
    * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StoreShelf $request)
    {
        try {
            $data = $request->sanitizedObject();
            $shelf = $this->repo::store($data);
            return back()->with(['success' => "The Shelf was created succesfully."]);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return back()->with([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
    * Display the specified resource.
    *
    * @param Request $request
    * @param Shelf $shelf
    * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
    */
    public function show(Request $request, Shelf $shelf)
    {
        try {
            $this->authorize('view', $shelf);
            $model = $this->repo::init($shelf)->show($request);
            return Inertia::render("Shelves/Show", ["model" => $model]);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return back()->with([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
    * Show Edit Form for the specified resource.
    *
    * @param Request $request
    * @param Shelf $shelf
    * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
    */
    public function edit(Request $request, Shelf $shelf)
    {
        try {
            $this->authorize('update', $shelf);
            //Fetch relationships
            

                        return Inertia::render("Shelves/Edit", ["model" => $shelf]);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return back()->with([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
    * Update the specified resource in storage.
    *
    * @param UpdateShelf $request
    * @param {$modelBaseName} $shelf
    * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdateShelf $request, Shelf $shelf)
    {
        try {
            $data = $request->sanitizedObject();
            $res = $this->repo::init($shelf)->update($data);
            return back()->with(['success' => "The Shelf was updated succesfully."]);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return back()->with([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param Shelf $shelf
    * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(DestroyShelf $request, Shelf $shelf)
    {
        $res = $this->repo::init($shelf)->destroy();
        if ($res) {
            return back()->with(['success' => "The Shelf was deleted succesfully."]);
        }
        else {
            return back()->with(['error' => "The Shelf could not be deleted."]);
        }
    }
}
