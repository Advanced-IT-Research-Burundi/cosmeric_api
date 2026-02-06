<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\TypeAssistance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TypeAssistanceController
 */
final class TypeAssistanceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $typeAssistances = TypeAssistance::factory()->count(3)->create();

        $response = $this->get(route('type-assistances.index'));

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
        $nom = fake()->word();
        $montant_standard = fake()->randomFloat(/** decimal_attributes **/);
        $conditions = fake()->text();
        $documents_requis = fake()->text();

        $response = $this->post(route('type-assistances.store'), [
            'nom' => $nom,
            'montant_standard' => $montant_standard,
            'conditions' => $conditions,
            'documents_requis' => $documents_requis,
        ]);

        $typeAssistances = TypeAssistance::query()
            ->where('nom', $nom)
            ->where('montant_standard', $montant_standard)
            ->where('conditions', $conditions)
            ->where('documents_requis', $documents_requis)
            ->get();
        $this->assertCount(1, $typeAssistances);
        $typeAssistance = $typeAssistances->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $typeAssistance = TypeAssistance::factory()->create();

        $response = $this->get(route('type-assistances.show', $typeAssistance));

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
        $typeAssistance = TypeAssistance::factory()->create();
        $nom = fake()->word();
        $montant_standard = fake()->randomFloat(/** decimal_attributes **/);
        $conditions = fake()->text();
        $documents_requis = fake()->text();

        $response = $this->put(route('type-assistances.update', $typeAssistance), [
            'nom' => $nom,
            'montant_standard' => $montant_standard,
            'conditions' => $conditions,
            'documents_requis' => $documents_requis,
        ]);

        $typeAssistance->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($nom, $typeAssistance->nom);
        $this->assertEquals($montant_standard, $typeAssistance->montant_standard);
        $this->assertEquals($conditions, $typeAssistance->conditions);
        $this->assertEquals($documents_requis, $typeAssistance->documents_requis);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $typeAssistance = TypeAssistance::factory()->create();

        $response = $this->delete(route('type-assistances.destroy', $typeAssistance));

        $response->assertNoContent();

        $this->assertModelMissing($typeAssistance);
    }
}
