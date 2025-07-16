<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Credit;
use App\Models\Remboursement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RemboursementController
 */
final class RemboursementControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $remboursements = Remboursement::factory()->count(3)->create();

        $response = $this->get(route('remboursements.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RemboursementController::class,
            'store',
            \App\Http\Requests\RemboursementStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $credit = Credit::factory()->create();
        $numero_echeance = fake()->numberBetween(-10000, 10000);
        $montant_prevu = fake()->randomFloat(/** decimal_attributes **/);
        $montant_paye = fake()->randomFloat(/** decimal_attributes **/);
        $date_echeance = Carbon::parse(fake()->date());
        $date_paiement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $penalite = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->post(route('remboursements.store'), [
            'credit_id' => $credit->id,
            'numero_echeance' => $numero_echeance,
            'montant_prevu' => $montant_prevu,
            'montant_paye' => $montant_paye,
            'date_echeance' => $date_echeance->toDateString(),
            'date_paiement' => $date_paiement->toDateString(),
            'statut' => $statut,
            'penalite' => $penalite,
        ]);

        $remboursements = Remboursement::query()
            ->where('credit_id', $credit->id)
            ->where('numero_echeance', $numero_echeance)
            ->where('montant_prevu', $montant_prevu)
            ->where('montant_paye', $montant_paye)
            ->where('date_echeance', $date_echeance)
            ->where('date_paiement', $date_paiement)
            ->where('statut', $statut)
            ->where('penalite', $penalite)
            ->get();
        $this->assertCount(1, $remboursements);
        $remboursement = $remboursements->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $remboursement = Remboursement::factory()->create();

        $response = $this->get(route('remboursements.show', $remboursement));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RemboursementController::class,
            'update',
            \App\Http\Requests\RemboursementUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $remboursement = Remboursement::factory()->create();
        $credit = Credit::factory()->create();
        $numero_echeance = fake()->numberBetween(-10000, 10000);
        $montant_prevu = fake()->randomFloat(/** decimal_attributes **/);
        $montant_paye = fake()->randomFloat(/** decimal_attributes **/);
        $date_echeance = Carbon::parse(fake()->date());
        $date_paiement = Carbon::parse(fake()->date());
        $statut = fake()->randomElement(/** enum_attributes **/);
        $penalite = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->put(route('remboursements.update', $remboursement), [
            'credit_id' => $credit->id,
            'numero_echeance' => $numero_echeance,
            'montant_prevu' => $montant_prevu,
            'montant_paye' => $montant_paye,
            'date_echeance' => $date_echeance->toDateString(),
            'date_paiement' => $date_paiement->toDateString(),
            'statut' => $statut,
            'penalite' => $penalite,
        ]);

        $remboursement->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($credit->id, $remboursement->credit_id);
        $this->assertEquals($numero_echeance, $remboursement->numero_echeance);
        $this->assertEquals($montant_prevu, $remboursement->montant_prevu);
        $this->assertEquals($montant_paye, $remboursement->montant_paye);
        $this->assertEquals($date_echeance, $remboursement->date_echeance);
        $this->assertEquals($date_paiement, $remboursement->date_paiement);
        $this->assertEquals($statut, $remboursement->statut);
        $this->assertEquals($penalite, $remboursement->penalite);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $remboursement = Remboursement::factory()->create();

        $response = $this->delete(route('remboursements.destroy', $remboursement));

        $response->assertNoContent();

        $this->assertModelMissing($remboursement);
    }
}
