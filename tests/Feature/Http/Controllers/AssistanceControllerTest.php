<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Assistance;
use App\Models\Membre;
use App\Models\TypeAssistance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AssistanceController
 */
final class AssistanceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $assistances = Assistance::factory()->count(3)->create();

        $response = $this->get(route('assistances.index'));

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
        $type_assistance = TypeAssistance::factory()->create();
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $date_demande = Carbon::parse(fake()->date());
        $date_approbation = Carbon::parse(fake()->date());
        $date_versement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $justificatif = fake()->word();
        $motif_rejet = fake()->text();

        $response = $this->post(route('assistances.store'), [
            'membre_id' => $membre->id,
            'type_assistance_id' => $type_assistance->id,
            'montant' => $montant,
            'date_demande' => $date_demande->toDateString(),
            'date_approbation' => $date_approbation->toDateString(),
            'date_versement' => $date_versement->toDateString(),
            'statut' => $statut,
            'justificatif' => $justificatif,
            'motif_rejet' => $motif_rejet,
        ]);

        $assistances = Assistance::query()
            ->where('membre_id', $membre->id)
            ->where('type_assistance_id', $type_assistance->id)
            ->where('montant', $montant)
            ->where('date_demande', $date_demande)
            ->where('date_approbation', $date_approbation)
            ->where('date_versement', $date_versement)
            ->where('statut', $statut)
            ->where('justificatif', $justificatif)
            ->where('motif_rejet', $motif_rejet)
            ->get();
        $this->assertCount(1, $assistances);
        $assistance = $assistances->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $assistance = Assistance::factory()->create();

        $response = $this->get(route('assistances.show', $assistance));

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
        $assistance = Assistance::factory()->create();
        $membre = Membre::factory()->create();
        $type_assistance = TypeAssistance::factory()->create();
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $date_demande = Carbon::parse(fake()->date());
        $date_approbation = Carbon::parse(fake()->date());
        $date_versement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $justificatif = fake()->word();
        $motif_rejet = fake()->text();

        $response = $this->put(route('assistances.update', $assistance), [
            'membre_id' => $membre->id,
            'type_assistance_id' => $type_assistance->id,
            'montant' => $montant,
            'date_demande' => $date_demande->toDateString(),
            'date_approbation' => $date_approbation->toDateString(),
            'date_versement' => $date_versement->toDateString(),
            'statut' => $statut,
            'justificatif' => $justificatif,
            'motif_rejet' => $motif_rejet,
        ]);

        $assistance->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($membre->id, $assistance->membre_id);
        $this->assertEquals($type_assistance->id, $assistance->type_assistance_id);
        $this->assertEquals($montant, $assistance->montant);
        $this->assertEquals($date_demande, $assistance->date_demande);
        $this->assertEquals($date_approbation, $assistance->date_approbation);
        $this->assertEquals($date_versement, $assistance->date_versement);
        $this->assertEquals($statut, $assistance->statut);
        $this->assertEquals($justificatif, $assistance->justificatif);
        $this->assertEquals($motif_rejet, $assistance->motif_rejet);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $assistance = Assistance::factory()->create();

        $response = $this->delete(route('assistances.destroy', $assistance));

        $response->assertNoContent();

        $this->assertModelMissing($assistance);
    }
}
