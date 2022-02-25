<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use Stichoza\GoogleTranslate\GoogleTranslate;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        LangController::importLangs();

        $recipes = $this->getRecipesFromApi();

        $this->importRecipes($recipes);
        die;
    }

    private function getRecipesFromApi()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://random-recipes.p.rapidapi.com/ai-quotes/10",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => [
                "x-rapidapi-host: random-recipes.p.rapidapi.com",
                "x-rapidapi-key:" . env("RAPID_API_API_KEY")
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $recipes = json_decode($response);

        return $recipes;
    }

    private function importRecipes(array $recipes)
    {
        foreach ($recipes as $recipe) {
            $steps = [];

            foreach ($recipe->instructions as $instruction) {
                array_push($steps, $instruction->text);
            }

            Recipe::create([
                'title'       => $recipe->title,
                'ingredients' => json_encode($recipe->ingredients),
                'steps'       => json_encode($steps),
                'image'       => $recipe->image,
                'iso_code'    => 'en'
            ]);

            $translator = new GoogleTranslate();
            Recipe::create([
                'title' => $translator->setSource('en')->setTarget('es')->translate($recipe->title),
                'ingredients' => $translator->setSource('en')->setTarget('es')->translate(json_encode($recipe->ingredients)),
                'steps' => $translator->setSource('en')->setTarget('es')->translate(json_encode($steps)),
                'image' => $recipe->image,
                'iso_code' => 'es'
            ]);

            Recipe::create([
                'title' => $translator->setSource('en')->setTarget('it')->translate($recipe->title),
                'ingredients' => $translator->setSource('en')->setTarget('it')->translate(json_encode($recipe->ingredients)),
                'steps' => $translator->setSource('en')->setTarget('it')->translate(json_encode($steps)),
                'image' => $recipe->image,
                'iso_code' => 'it'
            ]);
        }
    }

    public function printRecipesByIsoCode($isoCode)
    {
        $recipes = Recipe::query()->where('iso_code', '=', $isoCode)->get();
        echo json_encode($recipes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreRecipeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecipeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Recipe $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Recipe $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateRecipeRequest $request
     * @param \App\Models\Recipe $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Recipe $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}
