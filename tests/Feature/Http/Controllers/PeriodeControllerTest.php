<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PeriodeController
 */
final class PeriodeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $periodes = Periode::factory()->count(3)->create();

        $response = $this->get(route('periodes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodeController::class,
            'store',
            \App\Http\Requests\PeriodeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $mois = fake()->numberBetween(-10000, 10000);
        $annee = fake()->numberBetween(-10000, 10000);
        $statut = fake()->randomElement(/** enum_attributes **/);
        $date_debut = Carbon::parse(fake()->date());
        $date_fin = Carbon::parse(fake()->date());

        $response = $this->post(route('periodes.store'), [
            'mois' => $mois,
            'annee' => $annee,
            'statut' => $statut,
            'date_debut' => $date_debut->toDateString(),
            'date_fin' => $date_fin->toDateString(),
        ]);

        $periodes = Periode::query()
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->where('statut', $statut)
            ->where('date_debut', $date_debut)
            ->where('date_fin', $date_fin)
            ->get();
        $this->assertCount(1, $periodes);
        $periode = $periodes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $periode = Periode::factory()->create();

        $response = $this->get(route('periodes.show', $periode));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodeController::class,
            'update',
            \App\Http\Requests\PeriodeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $periode = Periode::factory()->create();
        $mois = fake()->numberBetween(-10000, 10000);
        $annee = fake()->numberBetween(-10000, 10000);
        $statut = fake()->randomElement(/** enum_attributes **/);
        $date_debut = Carbon::parse(fake()->date());
        $date_fin = Carbon::parse(fake()->date());

        $response = $this->put(route('periodes.update', $periode), [
            'mois' => $mois,
            'annee' => $annee,
            'statut' => $statut,
            'date_debut' => $date_debut->toDateString(),
            'date_fin' => $date_fin->toDateString(),
        ]);

        $periode->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($mois, $periode->mois);
        $this->assertEquals($annee, $periode->annee);
        $this->assertEquals($statut, $periode->statut);
        $this->assertEquals($date_debut, $periode->date_debut);
        $this->assertEquals($date_fin, $periode->date_fin);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $periode = Periode::factory()->create();

        $response = $this->delete(route('periodes.destroy', $periode));

        $response->assertNoContent();

        $this->assertModelMissing($periode);
    }
}
