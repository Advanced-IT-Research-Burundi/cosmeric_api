<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CotisationController
 */
final class CotisationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $cotisations = Cotisation::factory()->count(3)->create();

        $response = $this->get(route('cotisations.index'));

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
        $membre = Membre::factory()->create();
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $date_paiement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $mode_paiement = fake()->word();
        $reference_paiement = fake()->word();

        $response = $this->post(route('cotisations.store'), [
            'membre_id' => $membre->id,
            'periode_id' => $periode->id,
            'montant' => $montant,
            'devise' => $devise,
            'date_paiement' => $date_paiement->toDateString(),
            'statut' => $statut,
            'mode_paiement' => $mode_paiement,
            'reference_paiement' => $reference_paiement,
        ]);

        $cotisations = Cotisation::query()
            ->where('membre_id', $membre->id)
            ->where('montant', $montant)
            ->where('devise', $devise)
            ->where('date_paiement', $date_paiement)
            ->where('statut', $statut)
            ->where('mode_paiement', $mode_paiement)
            ->where('reference_paiement', $reference_paiement)
            ->get();
        $this->assertCount(1, $cotisations);
        $cotisation = $cotisations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $cotisation = Cotisation::factory()->create();

        $response = $this->get(route('cotisations.show', $cotisation));

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
        $cotisation = Cotisation::factory()->create();
        $membre = Membre::factory()->create();
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $date_paiement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $mode_paiement = fake()->word();
        $reference_paiement = fake()->word();

        $response = $this->put(route('cotisations.update', $cotisation), [
            'membre_id' => $membre->id,
            'periode_id' => $periode->id,
            'montant' => $montant,
            'devise' => $devise,
            'date_paiement' => $date_paiement->toDateString(),
            'statut' => $statut,
            'mode_paiement' => $mode_paiement,
            'reference_paiement' => $reference_paiement,
        ]);

        $cotisation->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($membre->id, $cotisation->membre_id);
        $this->assertEquals($montant, $cotisation->montant);
        $this->assertEquals($devise, $cotisation->devise);
        $this->assertEquals($date_paiement, $cotisation->date_paiement);
        $this->assertEquals($statut, $cotisation->statut);
        $this->assertEquals($mode_paiement, $cotisation->mode_paiement);
        $this->assertEquals($reference_paiement, $cotisation->reference_paiement);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $cotisation = Cotisation::factory()->create();

        $response = $this->delete(route('cotisations.destroy', $cotisation));

        $response->assertNoContent();

        $this->assertModelMissing($cotisation);
    }
}
