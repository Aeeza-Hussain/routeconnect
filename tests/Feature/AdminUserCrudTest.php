<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminUserCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'          => 'Admin Person',
            'email'         => 'admin@test.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        $this->passenger = User::factory()->create([
            'name'          => 'Passenger Person',
            'email'         => 'passenger@test.com',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Driver Person',
            'email'         => 'driver@test.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);
    }

    public function test_guests_cannot_access_admin_users(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');
    }

    public function test_passengers_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->passenger)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_drivers_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->driver)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_users_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Admin Person');
        $response->assertSee('Passenger Person');
        $response->assertSee('Driver Person');
    }

    public function test_admin_can_filter_users_by_type(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users?type=2');
        $response->assertStatus(200);
        $response->assertSee('Driver Person');
        $response->assertDontSee('Passenger Person');
    }

    public function test_admin_can_search_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users?search=Passenger');
        $response->assertStatus(200);
        $response->assertSee('Passenger Person');
        $response->assertDontSee('Driver Person');
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');
        $response->assertStatus(200);
        $response->assertSee('Create User Account');
    }

    public function test_admin_can_create_a_passenger_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name'                  => 'New Passenger',
            'email'                 => 'newpass@test.com',
            'phone'                 => '03112233445',
            'user_type'             => 0,
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'gender'                => 'female',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email'         => 'newpass@test.com',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        $created = User::where('email', 'newpass@test.com')->first();
        $this->assertTrue(Hash::check('secret123', $created->password));
    }

    public function test_admin_can_create_a_driver_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name'                  => 'New Driver User',
            'email'                 => 'newdriver@test.com',
            'phone'                 => '03223344556',
            'user_type'             => 2,
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'gender'                => 'male',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email'         => 'newdriver@test.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);
    }

    public function test_admin_can_view_user_details(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/users/{$this->driver->id}");
        $response->assertStatus(200);
        $response->assertSee($this->driver->name);
        $response->assertSee($this->driver->email);
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/users/{$this->passenger->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit: ' . $this->passenger->name);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $oldPasswordHash = $this->passenger->password;

        $response = $this->actingAs($this->admin)->put("/admin/users/{$this->passenger->id}", [
            'name'      => 'Updated Passenger Name',
            'email'     => 'passenger@test.com',
            'user_type' => 0,
            'phone'     => '03998877665',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $this->passenger->refresh();
        $this->assertEquals('Updated Passenger Name', $this->passenger->name);
        $this->assertEquals('03998877665', $this->passenger->phone);
        $this->assertEquals($oldPasswordHash, $this->passenger->password);
    }

    public function test_admin_can_update_user_with_new_password(): void
    {
        $response = $this->actingAs($this->admin)->put("/admin/users/{$this->passenger->id}", [
            'name'                  => 'Passenger Name',
            'email'                 => 'passenger@test.com',
            'user_type'             => 0,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/admin/users');
        $this->passenger->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->passenger->password));
    }

    public function test_admin_can_delete_another_user(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->passenger->id}");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $this->passenger->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
