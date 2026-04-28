<?php

namespace App\Domain\Sgc\Contratada\app\Controller;
use App\Shared\Http\Controllers\Controller;

use App\Domain\Sgc\Contratada\app\Requests\StoreLayerRequest;
use App\Domain\Sgc\Contratada\app\Jobs\ConvertShapefileToTiles;
use App\Models\SgcvwLayer as Layer;
use Illuminate\Support\Facades\Storage;

class LayerController extends Controller
{
    public function index()
    {
        return inertia('Sgc/Contratada/Mapa/Index', [
            'layers' => Layer::latest()->get()
        ]);
    }
    public function mapa()
    {
        return inertia('Sgc/Contratada/Mapa/Mapa', [
            'layers' => Layer::latest()->get()
        ]);
    }

    public function store(StoreLayerRequest $request)
    {
        $path = $request->file('file')->store('layers');

        $layer = Layer::create([
            'name' => $request->name,
            'file_path' => $path,
            'status' => 'pending'
        ]);

        dispatch(new ConvertShapefileToTiles($layer));

        return back()->with('success', 'Camada enviada e conversão iniciada.');
    }
}
