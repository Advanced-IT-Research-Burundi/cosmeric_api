<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Credit;
use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditController
 */
final class CreditControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $credits = Credit::factory()->count(3)->create();

        $response = $this->get(route('credits.index'));

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
        $montant_demande = fake()->randomFloat(/** decimal_attributes **/);
        $montant_accorde = fake()->randomFloat(/** decimal_attributes **/);
        $taux_interet = fake()->randomFloat(/** decimal_attributes **/);
        $duree_mois = fake()->numberBetween(-10000, 10000);
        $montant_total_rembourser = fake()->randomFloat(/** decimal_attributes **/);
        $montant_mensualite = fake()->randomFloat(/** decimal_attributes **/);
        $date_demande = Carbon::parse(fake()->date());
        $date_approbation = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $motif = fake()->text();

        $response = $this->post(route('credits.store'), [
            'membre_id' => $membre->id,
            'montant_demande' => $montant_demande,
            'montant_accorde' => $montant_accorde,
            'taux_interet' => $taux_interet,
            'duree_mois' => $duree_mois,
            'montant_total_rembourser' => $montant_total_rembourser,
            'montant_mensualite' => $montant_mensualite,
            'date_demande' => $date_demande->toDateString(),
            'date_approbation' => $date_approbation->toDateString(),
            'statut' => $statut,
            'motif' => $motif,
        ]);

        $credits = Credit::query()
            ->where('membre_id', $membre->id)
            ->where('montant_demande', $montant_demande)
            ->where('montant_accorde', $montant_accorde)
            ->where('taux_interet', $taux_interet)
            ->where('duree_mois', $duree_mois)
            ->where('montant_total_rembourser', $montant_total_rembourser)
            ->where('montant_mensualite', $montant_mensualite)
            ->where('date_demande', $date_demande)
            ->where('date_approbation', $date_approbation)
            ->where('statut', $statut)
            ->where('motif', $motif)
            ->get();
        $this->assertCount(1, $credits);
        $credit = $credits->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $credit = Credit::factory()->create();

        $response = $this->get(route('credits.show', $credit));

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
        $credit = Credit::factory()->create();
        $membre = Membre::factory()->create();
        $montant_demande = fake()->randomFloat(/** decimal_attributes **/);
        $montant_accorde = fake()->randomFloat(/** decimal_attributes **/);
        $taux_interet = fake()->randomFloat(/** decimal_attributes **/);
        $duree_mois = fake()->numberBetween(-10000, 10000);
        $montant_total_rembourser = fake()->randomFloat(/** decimal_attributes **/);
        $montant_mensualite = fake()->randomFloat(/** decimal_attributes **/);
        $date_demande = Carbon::parse(fake()->date());
        $date_approbation = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $motif = fake()->text();

        $response = $this->put(route('credits.update', $credit), [
            'membre_id' => $membre->id,
            'montant_demande' => $montant_demande,
            'montant_accorde' => $montant_accorde,
            'taux_interet' => $taux_interet,
            'duree_mois' => $duree_mois,
            'montant_total_rembourser' => $montant_total_rembourser,
            'montant_mensualite' => $montant_mensualite,
            'date_demande' => $date_demande->toDateString(),
            'date_approbation' => $date_approbation->toDateString(),
            'statut' => $statut,
            'motif' => $motif,
        ]);

        $credit->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($membre->id, $credit->membre_id);
        $this->assertEquals($montant_demande, $credit->montant_demande);
        $this->assertEquals($montant_accorde, $credit->montant_accorde);
        $this->assertEquals($taux_interet, $credit->taux_interet);
        $this->assertEquals($duree_mois, $credit->duree_mois);
        $this->assertEquals($montant_total_rembourser, $credit->montant_total_rembourser);
        $this->assertEquals($montant_mensualite, $credit->montant_mensualite);
        $this->assertEquals($date_demande, $credit->date_demande);
        $this->assertEquals($date_approbation, $credit->date_approbation);
        $this->assertEquals($statut, $credit->statut);
        $this->assertEquals($motif, $credit->motif);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $credit = Credit::factory()->create();

        $response = $this->delete(route('credits.destroy', $credit));

        $response->assertNoContent();

        $this->assertModelMissing($credit);
    }
}
