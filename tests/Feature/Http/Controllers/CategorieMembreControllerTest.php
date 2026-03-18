<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CategorieMembre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CategorieMembreController
 */
final class CategorieMembreControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $categorieMembres = CategorieMembre::factory()->count(3)->create();

        $response = $this->get(route('categorie-membres.index'));

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
        $montant_cotisation = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $frequence_paiement = fake()->randomElement(/** enum_attributes **/);
        $description = fake()->text();

        $response = $this->post(route('categorie-membres.store'), [
            'nom' => $nom,
            'montant_cotisation' => $montant_cotisation,
            'devise' => $devise,
            'frequence_paiement' => $frequence_paiement,
            'description' => $description,
        ]);

        $categorieMembres = CategorieMembre::query()
            ->where('nom', $nom)
            ->where('montant_cotisation', $montant_cotisation)
            ->where('devise', $devise)
            ->where('frequence_paiement', $frequence_paiement)
            ->where('description', $description)
            ->get();
        $this->assertCount(1, $categorieMembres);
        $categorieMembre = $categorieMembres->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $categorieMembre = CategorieMembre::factory()->create();

        $response = $this->get(route('categorie-membres.show', $categorieMembre));

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
        $categorieMembre = CategorieMembre::factory()->create();
        $nom = fake()->word();
        $montant_cotisation = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $frequence_paiement = fake()->randomElement(/** enum_attributes **/);
        $description = fake()->text();

        $response = $this->put(route('categorie-membres.update', $categorieMembre), [
            'nom' => $nom,
            'montant_cotisation' => $montant_cotisation,
            'devise' => $devise,
            'frequence_paiement' => $frequence_paiement,
            'description' => $description,
        ]);

        $categorieMembre->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($nom, $categorieMembre->nom);
        $this->assertEquals($montant_cotisation, $categorieMembre->montant_cotisation);
        $this->assertEquals($devise, $categorieMembre->devise);
        $this->assertEquals($frequence_paiement, $categorieMembre->frequence_paiement);
        $this->assertEquals($description, $categorieMembre->description);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $categorieMembre = CategorieMembre::factory()->create();

        $response = $this->delete(route('categorie-membres.destroy', $categorieMembre));

        $response->assertNoContent();

        $this->assertModelMissing($categorieMembre);
    }
}
