<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CotisationMensuelle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CotisationMensuelleController
 */
final class CotisationMensuelleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $cotisationMensuelles = CotisationMensuelle::factory()->count(3)->create();

        $response = $this->get(route('cotisation-mensuelles.index'));

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
        $response = $this->post(route('cotisation-mensuelles.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(cotisationMensuelles, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $cotisationMensuelle = CotisationMensuelle::factory()->create();

        $response = $this->get(route('cotisation-mensuelles.show', $cotisationMensuelle));

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
        $cotisationMensuelle = CotisationMensuelle::factory()->create();

        $response = $this->put(route('cotisation-mensuelles.update', $cotisationMensuelle));

        $cotisationMensuelle->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $cotisationMensuelle = CotisationMensuelle::factory()->create();

        $response = $this->delete(route('cotisation-mensuelles.destroy', $cotisationMensuelle));

        $response->assertNoContent();

        $this->assertModelMissing($cotisationMensuelle);
    }
}
