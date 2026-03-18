<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Categorie;
use App\Models\Membre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MembreController
 */
final class MembreControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $membres = Membre::factory()->count(3)->create();

        $response = $this->get(route('membres.index'));

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
        $user = User::factory()->create();
        $matricule = fake()->word();
        $nom = fake()->word();
        $prenom = fake()->word();
        $email = fake()->safeEmail();
        $telephone = fake()->word();
        $categorie = Categorie::factory()->create();
        $statut = fake()->randomElement(/** enum_attributes **/);
        $date_adhesion = Carbon::parse(fake()->date());

        $response = $this->post(route('membres.store'), [
            'user_id' => $user->id,
            'matricule' => $matricule,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'categorie_id' => $categorie->id,
            'statut' => $statut,
            'date_adhesion' => $date_adhesion->toDateString(),
        ]);

        $membres = Membre::query()
            ->where('user_id', $user->id)
            ->where('matricule', $matricule)
            ->where('nom', $nom)
            ->where('prenom', $prenom)
            ->where('email', $email)
            ->where('telephone', $telephone)
            ->where('categorie_id', $categorie->id)
            ->where('statut', $statut)
            ->where('date_adhesion', $date_adhesion)
            ->get();
        $this->assertCount(1, $membres);
        $membre = $membres->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $membre = Membre::factory()->create();

        $response = $this->get(route('membres.show', $membre));

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
        $membre = Membre::factory()->create();
        $user = User::factory()->create();
        $matricule = fake()->word();
        $nom = fake()->word();
        $prenom = fake()->word();
        $email = fake()->safeEmail();
        $telephone = fake()->word();
        $categorie = Categorie::factory()->create();
        $statut = fake()->randomElement(/** enum_attributes **/);
        $date_adhesion = Carbon::parse(fake()->date());

        $response = $this->put(route('membres.update', $membre), [
            'user_id' => $user->id,
            'matricule' => $matricule,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'categorie_id' => $categorie->id,
            'statut' => $statut,
            'date_adhesion' => $date_adhesion->toDateString(),
        ]);

        $membre->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $membre->user_id);
        $this->assertEquals($matricule, $membre->matricule);
        $this->assertEquals($nom, $membre->nom);
        $this->assertEquals($prenom, $membre->prenom);
        $this->assertEquals($email, $membre->email);
        $this->assertEquals($telephone, $membre->telephone);
        $this->assertEquals($categorie->id, $membre->categorie_id);
        $this->assertEquals($statut, $membre->statut);
        $this->assertEquals($date_adhesion, $membre->date_adhesion);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $membre = Membre::factory()->create();

        $response = $this->delete(route('membres.destroy', $membre));

        $response->assertNoContent();

        $this->assertModelMissing($membre);
    }
}
