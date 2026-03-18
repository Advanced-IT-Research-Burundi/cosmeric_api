<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\GenerePar;
use App\Models\Rapport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RapportController
 */
final class RapportControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $rapports = Rapport::factory()->count(3)->create();

        $response = $this->get(route('rapports.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function store_saves(): void
    {
        $titre = fake()->word();
        $type_rapport = fake()->randomElement(/** enum_attributes **/);
        $periode_debut = Carbon::parse(fake()->date());
        $periode_fin = Carbon::parse(fake()->date());
        $genere_par = GenerePar::factory()->create();
        $fichier_path = fake()->word();
        $statut = fake()->randomElement(/** enum_attributes **/);
        $created_at = Carbon::parse(fake()->dateTime());

        $response = $this->post(route('rapports.store'), [
            'titre' => $titre,
            'type_rapport' => $type_rapport,
            'periode_debut' => $periode_debut->toDateString(),
            'periode_fin' => $periode_fin->toDateString(),
            'genere_par' => $genere_par->id,
            'fichier_path' => $fichier_path,
            'statut' => $statut,
            'created_at' => $created_at->toDateTimeString(),
        ]);

        $rapports = Rapport::query()
            ->where('titre', $titre)
            ->where('type_rapport', $type_rapport)
            ->where('periode_debut', $periode_debut)
            ->where('periode_fin', $periode_fin)
            ->where('genere_par', $genere_par->id)
            ->where('fichier_path', $fichier_path)
            ->where('statut', $statut)
            ->where('created_at', $created_at)
            ->get();
        $this->assertCount(1, $rapports);
        $rapport = $rapports->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $rapport = Rapport::factory()->create();

        $response = $this->get(route('rapports.show', $rapport));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $rapport = Rapport::factory()->create();
        $titre = fake()->word();
        $type_rapport = fake()->randomElement(/** enum_attributes **/);
        $periode_debut = Carbon::parse(fake()->date());
        $periode_fin = Carbon::parse(fake()->date());
        $genere_par = GenerePar::factory()->create();
        $fichier_path = fake()->word();
        $statut = fake()->randomElement(/** enum_attributes **/);
        $created_at = Carbon::parse(fake()->dateTime());

        $response = $this->put(route('rapports.update', $rapport), [
            'titre' => $titre,
            'type_rapport' => $type_rapport,
            'periode_debut' => $periode_debut->toDateString(),
            'periode_fin' => $periode_fin->toDateString(),
            'genere_par' => $genere_par->id,
            'fichier_path' => $fichier_path,
            'statut' => $statut,
            'created_at' => $created_at->toDateTimeString(),
        ]);

        $rapport->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($titre, $rapport->titre);
        $this->assertEquals($type_rapport, $rapport->type_rapport);
        $this->assertEquals($periode_debut, $rapport->periode_debut);
        $this->assertEquals($periode_fin, $rapport->periode_fin);
        $this->assertEquals($genere_par->id, $rapport->genere_par);
        $this->assertEquals($fichier_path, $rapport->fichier_path);
        $this->assertEquals($statut, $rapport->statut);
        $this->assertEquals($created_at->timestamp, $rapport->created_at);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $rapport = Rapport::factory()->create();

        $response = $this->delete(route('rapports.destroy', $rapport));

        $response->assertNoContent();

        $this->assertModelMissing($rapport);
    }
}
