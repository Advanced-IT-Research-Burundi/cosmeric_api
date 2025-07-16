<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Membre;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TransactionController
 */
final class TransactionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $transactions = Transaction::factory()->count(3)->create();

        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransactionController::class,
            'store',
            \App\Http\Requests\TransactionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $membre = Membre::factory()->create();
        $type_transaction = fake()->randomElement(/** enum_attributes **/);
        $reference_transaction = fake()->numberBetween(-10000, 10000);
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $sens = fake()->randomElement(/** enum_attributes **/);
        $date_transaction = Carbon::parse(fake()->date());
        $description = fake()->text();
        $created_at = Carbon::parse(fake()->dateTime());

        $response = $this->post(route('transactions.store'), [
            'membre_id' => $membre->id,
            'type_transaction' => $type_transaction,
            'reference_transaction' => $reference_transaction,
            'montant' => $montant,
            'devise' => $devise,
            'sens' => $sens,
            'date_transaction' => $date_transaction->toDateString(),
            'description' => $description,
            'created_at' => $created_at->toDateTimeString(),
        ]);

        $transactions = Transaction::query()
            ->where('membre_id', $membre->id)
            ->where('type_transaction', $type_transaction)
            ->where('reference_transaction', $reference_transaction)
            ->where('montant', $montant)
            ->where('devise', $devise)
            ->where('sens', $sens)
            ->where('date_transaction', $date_transaction)
            ->where('description', $description)
            ->where('created_at', $created_at)
            ->get();
        $this->assertCount(1, $transactions);
        $transaction = $transactions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('transactions.show', $transaction));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransactionController::class,
            'update',
            \App\Http\Requests\TransactionUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $transaction = Transaction::factory()->create();
        $membre = Membre::factory()->create();
        $type_transaction = fake()->randomElement(/** enum_attributes **/);
        $reference_transaction = fake()->numberBetween(-10000, 10000);
        $montant = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->randomElement(/** enum_attributes **/);
        $sens = fake()->randomElement(/** enum_attributes **/);
        $date_transaction = Carbon::parse(fake()->date());
        $description = fake()->text();
        $created_at = Carbon::parse(fake()->dateTime());

        $response = $this->put(route('transactions.update', $transaction), [
            'membre_id' => $membre->id,
            'type_transaction' => $type_transaction,
            'reference_transaction' => $reference_transaction,
            'montant' => $montant,
            'devise' => $devise,
            'sens' => $sens,
            'date_transaction' => $date_transaction->toDateString(),
            'description' => $description,
            'created_at' => $created_at->toDateTimeString(),
        ]);

        $transaction->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($membre->id, $transaction->membre_id);
        $this->assertEquals($type_transaction, $transaction->type_transaction);
        $this->assertEquals($reference_transaction, $transaction->reference_transaction);
        $this->assertEquals($montant, $transaction->montant);
        $this->assertEquals($devise, $transaction->devise);
        $this->assertEquals($sens, $transaction->sens);
        $this->assertEquals($date_transaction, $transaction->date_transaction);
        $this->assertEquals($description, $transaction->description);
        $this->assertEquals($created_at->timestamp, $transaction->created_at);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->delete(route('transactions.destroy', $transaction));

        $response->assertNoContent();

        $this->assertModelMissing($transaction);
    }
}
