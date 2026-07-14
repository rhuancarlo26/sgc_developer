<?php
namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;

use App\Shared\Http\Controllers\Controller;

use Inertia\Inertia;

class MapPageController extends Controller
{
    /**
     * Página de visualização do mapa
     */
    public function viewer()
    {
        // renderiza MapViewer.vue em resources/js/Pages/Sgc/Contratada/Produtos/Espeleologia/MapViewer.vue
        return Inertia::render('Sgc/Contratada/Produtos/Espeleologia/MapViewer');
    }

    /**
     * Página de cadastro de shapefiles
     */
    public function create()
    {
        return Inertia::render('Sgc/Contratada/Produtos/Espeleologia/MapCadastrarShape');
    }
}
