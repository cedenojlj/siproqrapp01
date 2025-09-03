<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Livewire\Customer\Create;
use App\Livewire\Customer\Edit;
use App\Livewire\Customer\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function the_customer_index_page_can_be_rendered()
    {
        $this->get(route('customers.index'))->assertStatus(200);
    }

    /** @test */
    public function the_customer_create_page_can_be_rendered()
    {
        $this->get(route('customers.create'))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_valid_customer()
    {
        Livewire::test(Create::class)
            ->set('name', 'John Doe')
            ->set('email', 'john.doe@example.com')
            ->set('phone', '1234567890')
            ->set('address', '123 Main St')
            ->call('save')
            ->assertRedirect(route('customers.index'));

        $this->assertTrue(Customer::where('email', 'john.doe@example.com')->exists());
    }

    /** @test */
    public function name_is_required_for_creation()
    {
        Livewire::test(Create::class)
            ->set('email', 'john.doe@example.com')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        Livewire::test(Create::class)
            ->set('name', 'John Doe')
            ->set('email', 'not-an-email')
            ->call('save')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_must_be_unique()
    {
        $existingCustomer = Customer::factory()->create();

        Livewire::test(Create::class)
            ->set('name', 'Jane Doe')
            ->set('email', $existingCustomer->email)
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function the_customer_edit_page_can_be_rendered()
    {
        $customer = Customer::factory()->create();
        $this->get(route('customers.edit', $customer))
            ->assertStatus(200)
            ->assertSee($customer->name);
    }

    /** @test */
    public function it_can_update_a_customer()
    {
        $customer = Customer::factory()->create();

        Livewire::test(Edit::class, ['customer' => $customer])
            ->set('name', 'Jane Doe')
            ->set('email', 'jane.doe@example.com')
            ->call('update')
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com'
        ]);
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = Customer::factory()->create();

        Livewire::test(Index::class)
            ->call('delete', $customer->id);

        $this->assertModelMissing($customer);
    }

    /** @test */
    public function search_by_name_or_email_works_correctly()
    {
        $customerA = Customer::factory()->create(['name' => 'Alpha Customer', 'email' => 'alpha@test.com']);
        $customerB = Customer::factory()->create(['name' => 'Beta Customer', 'email' => 'beta@test.com']);

        Livewire::test(Index::class)
            ->set('search', 'Alpha')
            ->assertSee($customerA->name)
            ->assertDontSee($customerB->name);

        Livewire::test(Index::class)
            ->set('search', 'beta@test.com')
            ->assertSee($customerB->name)
            ->assertDontSee($customerA->name);
    }
}