<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\IndexStock;
use App\Http\Requests\Stock\StoreStock;
use App\Http\Requests\Stock\UpdateStock;
use App\Http\Requests\Stock\DestroyStock;
use App\Models\Stock;
use App\Repositories\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\Html\Column;

class StockController  extends Controller
{
    private Stocks $repo;
    public function __construct(Stocks $repo)
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
        $this->authorize('viewAny', Stock::class);
        return Inertia::render('Stocks/Index',[
            "can" => [
                "viewAny" => \Auth::user()->can('viewAny', Stock::class),
                "create" => \Auth::user()->can('create', Stock::class),
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
        $this->authorize('create', Stock::class);
        return Inertia::render("Stocks/Create",[
            "can" => [
            "viewAny" => \Auth::user()->can('viewAny', Stock::class),
            "create" => \Auth::user()->can('create', Stock::class),
            ]
        ]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param StoreStock $request
    * @return \Illuminate\Http\RedirectResponse
    */
    public function store(StoreStock $request)
    {
        try {
            $data = $request->sanitizedObject();
            $stock = $this->repo::store($data);
            return back()->with(['success' => "The Stock was created succesfully."]);
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
    * @param Stock $stock
    * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
    */
    public function show(Request $request, Stock $stock)
    {
        try {
            $this->authorize('view', $stock);
            $model = $this->repo::init($stock)->show($request);
            return Inertia::render("Stocks/Show", ["model" => $model]);
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
    * @param Stock $stock
    * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
    */
    public function edit(Request $request, Stock $stock)
    {
        try {
            $this->authorize('update', $stock);
            //Fetch relationships
            



        $stock->load([
            'shelf',
        ]);
                        return Inertia::render("Stocks/Edit", ["model" => $stock]);
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
    * @param UpdateStock $request
    * @param {$modelBaseName} $stock
    * @return \Illuminate\Http\RedirectResponse
    */
    public function update(UpdateStock $request, Stock $stock)
    {
        try {
            $data = $request->sanitizedObject();
            $res = $this->repo::init($stock)->update($data);
            return back()->with(['success' => "The Stock was updated succesfully."]);
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
    * @param Stock $stock
    * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(DestroyStock $request, Stock $stock)
    {
        $res = $this->repo::init($stock)->destroy();
        if ($res) {
            return back()->with(['success' => "The Stock was deleted succesfully."]);
        }
        else {
            return back()->with(['error' => "The Stock could not be deleted."]);
        }
    }
}
