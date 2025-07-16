<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ConfigurationController
 */
final class ConfigurationControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $configurations = Configuration::factory()->count(3)->create();

        $response = $this->get(route('configurations.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ConfigurationController::class,
            'store',
            \App\Http\Requests\ConfigurationStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $cle = fake()->word();
        $valeur = fake()->text();
        $description = fake()->text();

        $response = $this->post(route('configurations.store'), [
            'cle' => $cle,
            'valeur' => $valeur,
            'description' => $description,
        ]);

        $configurations = Configuration::query()
            ->where('cle', $cle)
            ->where('valeur', $valeur)
            ->where('description', $description)
            ->get();
        $this->assertCount(1, $configurations);
        $configuration = $configurations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $configuration = Configuration::factory()->create();

        $response = $this->get(route('configurations.show', $configuration));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ConfigurationController::class,
            'update',
            \App\Http\Requests\ConfigurationUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $configuration = Configuration::factory()->create();
        $cle = fake()->word();
        $valeur = fake()->text();
        $description = fake()->text();

        $response = $this->put(route('configurations.update', $configuration), [
            'cle' => $cle,
            'valeur' => $valeur,
            'description' => $description,
        ]);

        $configuration->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($cle, $configuration->cle);
        $this->assertEquals($valeur, $configuration->valeur);
        $this->assertEquals($description, $configuration->description);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $configuration = Configuration::factory()->create();

        $response = $this->delete(route('configurations.destroy', $configuration));

        $response->assertNoContent();

        $this->assertModelMissing($configuration);
    }
}
