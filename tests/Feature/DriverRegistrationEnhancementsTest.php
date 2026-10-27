<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;

class DriverRegistrationEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'name'          => 'System Admin',
            'email'         => 'admin@routeconnect.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);
    }

    /**
     * Test 1: Full Driver Registration with documents, emergency contacts, and consents.
     */
    public function test_driver_can_register_with_documents_emergency_contacts_and_consents(): void
    {
        $cnicFront = UploadedFile::fake()->create('cnic_front.jpg', 500, 'image/jpeg');
        $cnicBack = UploadedFile::fake()->create('cnic_back.jpg', 500, 'image/jpeg');
        $licenseFront = UploadedFile::fake()->create('license_front.png', 500, 'image/png');
        $licenseBack = UploadedFile::fake()->create('license_back.pdf', 500, 'application/pdf');

        $payload = [
            'role'                     => 'driver',
            'driver_submission'        => '1',
            'name'                     => 'Kamran Ahmed',
            'email'                    => 'kamran.driver@routeconnect.com',
            'phone'                    => '0312-3456789',
            'password'                 => 'DriverSecure@2026',
            'password_confirmation'    => 'DriverSecure@2026',
            'gender'                   => 'male',
            'dob'                      => '1990-08-20',
            'cnic'                     => '71501-1234567-9',
            'license_no'               => 'GLT-DL-88221',
            'license_issue_date'       => Carbon::now()->subYears(2)->format('Y-m-d'),
            'license_expiry_date'      => Carbon::now()->addYears(3)->format('Y-m-d'),
            'emergency_contact_name'   => 'Sultan Ahmed',
            'emergency_contact_phone'  => '0300-9876543',
            'bio'                      => '10 years experience on Karakoram Highway routes.',
            'cnic_front_image'         => $cnicFront,
            'cnic_back_image'          => $cnicBack,
            'license_front_image'      => $licenseFront,
            'license_back_image'       => $licenseBack,
            'terms_accepted'           => '1',
            'privacy_accepted'         => '1',
            'declaration_confirmed'    => '1',
            'verification_consent'     => '1',
        ];

        $response = $this->post(route('driver.register.submit'), $payload);

        $response->assertRedirect(route('driver.pending'));
        $this->assertAuthenticated();

        $driver = User::where('email', 'kamran.driver@routeconnect.com')->firstOrFail();
        $this->assertEquals(2, $driver->user_type);
        $this->assertEquals('pending', $driver->driver_status);
        $this->assertEquals('71501-1234567-9', $driver->cnic);
        $this->assertEquals('GLT-DL-88221', $driver->license_no);
        $this->assertEquals('Sultan Ahmed', $driver->emergency_contact_name);
        $this->assertEquals('0300-9876543', $driver->emergency_contact_phone);

        // Verify audit timestamps
        $this->assertNotNull($driver->terms_accepted_at);
        $this->assertNotNull($driver->privacy_accepted_at);
        $this->assertNotNull($driver->declaration_confirmed_at);
        $this->assertNotNull($driver->verification_consent_at);

        // Verify private storage
        $this->assertNotNull($driver->cnic_front_image);
        $this->assertNotNull($driver->cnic_back_image);
        $this->assertNotNull($driver->license_front_image);
        $this->assertNotNull($driver->license_back_image);

        Storage::disk('local')->assertExists($driver->cnic_front_image);
        Storage::disk('local')->assertExists($driver->cnic_back_image);
        Storage::disk('local')->assertExists($driver->license_front_image);
        Storage::disk('local')->assertExists($driver->license_back_image);
    }

    /**
     * Test 2: Duplicate email, CNIC, and driving licence checks.
     */
    public function test_duplicate_email_cnic_and_license_are_rejected(): void
    {
        User::factory()->create([
            'email'      => 'existing.driver@routeconnect.com',
            'cnic'       => '71501-5555555-5',
            'license_no' => 'DL-EXISTING-1',
            'user_type'  => 2,
        ]);

        $basePayload = [
            'role'                     => 'driver',
            'driver_submission'        => '1',
            'name'                     => 'Another Driver',
            'phone'                    => '0315-1112233',
            'password'                 => 'ValidPassword123',
            'password_confirmation'    => 'ValidPassword123',
            'gender'                   => 'male',
            'dob'                      => '1992-01-01',
            'license_issue_date'       => '2022-01-01',
            'license_expiry_date'      => Carbon::now()->addYears(2)->format('Y-m-d'),
            'emergency_contact_name'   => 'Brother',
            'emergency_contact_phone'  => '0300-1111111',
            'cnic_front_image'         => UploadedFile::fake()->create('f.jpg', 200, 'image/jpeg'),
            'cnic_back_image'          => UploadedFile::fake()->create('b.jpg', 200, 'image/jpeg'),
            'license_front_image'      => UploadedFile::fake()->create('lf.jpg', 200, 'image/jpeg'),
            'license_back_image'       => UploadedFile::fake()->create('lb.jpg', 200, 'image/jpeg'),
            'terms_accepted'           => '1',
            'privacy_accepted'         => '1',
            'declaration_confirmed'    => '1',
            'verification_consent'     => '1',
        ];

        // 1. Duplicate email
        $resEmail = $this->post(route('driver.register.submit'), array_merge($basePayload, [
            'email'      => 'existing.driver@routeconnect.com',
            'cnic'       => '71501-9999999-9',
            'license_no' => 'DL-NEW-111',
        ]));
        $resEmail->assertSessionHasErrors(['email']);

        // 2. Duplicate CNIC
        $resCnic = $this->post(route('driver.register.submit'), array_merge($basePayload, [
            'email'      => 'new.unique1@routeconnect.com',
            'cnic'       => '71501-5555555-5',
            'license_no' => 'DL-NEW-222',
        ]));
        $resCnic->assertSessionHasErrors(['cnic']);

        // 3. Duplicate License No
        $resLic = $this->post(route('driver.register.submit'), array_merge($basePayload, [
            'email'      => 'new.unique2@routeconnect.com',
            'cnic'       => '71501-8888888-8',
            'license_no' => 'DL-EXISTING-1',
        ]));
        $resLic->assertSessionHasErrors(['license_no']);
    }

    /**
     * Test 3: Password must be at least 8 characters.
     */
    public function test_short_password_is_rejected(): void
    {
        $res = $this->post(route('driver.register.submit'), [
            'role'                  => 'driver',
            'name'                  => 'Short Pass Driver',
            'email'                 => 'short@example.com',
            'phone'                 => '0300-1234567',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /**
     * Test 4: Expired driving licence or future issue date is rejected.
     */
    public function test_invalid_license_dates_are_rejected(): void
    {
        $payload = [
            'role'                     => 'driver',
            'driver_submission'        => '1',
            'name'                     => 'Date Test Driver',
            'email'                    => 'dates@example.com',
            'phone'                    => '0300-1234567',
            'password'                 => 'Password12345',
            'password_confirmation'    => 'Password12345',
            'gender'                   => 'male',
            'dob'                      => '1995-01-01',
            'cnic'                     => '71501-1122334-1',
            'license_no'               => 'GLT-DL-DATE-1',
            'license_issue_date'       => Carbon::now()->addDays(5)->format('Y-m-d'), // in future!
            'license_expiry_date'      => Carbon::now()->subDays(10)->format('Y-m-d'), // expired!
            'emergency_contact_name'   => 'Contact',
            'emergency_contact_phone'  => '0300-9999999',
            'cnic_front_image'         => UploadedFile::fake()->create('f.jpg', 200, 'image/jpeg'),
            'cnic_back_image'          => UploadedFile::fake()->create('b.jpg', 200, 'image/jpeg'),
            'license_front_image'      => UploadedFile::fake()->create('lf.jpg', 200, 'image/jpeg'),
            'license_back_image'       => UploadedFile::fake()->create('lb.jpg', 200, 'image/jpeg'),
            'terms_accepted'           => '1',
            'privacy_accepted'         => '1',
            'declaration_confirmed'    => '1',
            'verification_consent'     => '1',
        ];

        $res = $this->post(route('driver.register.submit'), $payload);
        $res->assertSessionHasErrors(['license_issue_date', 'license_expiry_date']);
    }

    /**
     * Test 5: Only Admins can view and download private driver verification documents.
     */
    public function test_only_admin_can_access_driver_identity_documents(): void
    {
        $filePath = UploadedFile::fake()->create('private_id.jpg', 200, 'image/jpeg')->store('driver_documents/cnic', 'local');

        $driver = User::factory()->create([
            'user_type'        => 2,
            'driver_status'    => 'pending',
            'cnic_front_image' => $filePath,
        ]);

        $passenger = User::factory()->create(['user_type' => 0]);

        // 1. Unauthenticated guest cannot view document
        $guestRes = $this->get(route('admin.drivers.document', [$driver->id, 'cnic_front']));
        $guestRes->assertRedirect(route('login'));

        // 2. Passenger cannot view document (forbidden)
        $passRes = $this->actingAs($passenger)->get(route('admin.drivers.document', [$driver->id, 'cnic_front']));
        $passRes->assertStatus(403);

        // 3. Another driver cannot view document (forbidden)
        $driverRes = $this->actingAs($driver)->get(route('admin.drivers.document', [$driver->id, 'cnic_front']));
        $driverRes->assertStatus(403);

        // 4. Admin CAN view document
        $adminViewRes = $this->actingAs($this->admin)->get(route('admin.drivers.document', [$driver->id, 'cnic_front']));
        $adminViewRes->assertStatus(200);

        // 5. Admin CAN download document
        $adminDownRes = $this->actingAs($this->admin)->get(route('admin.drivers.document', [$driver->id, 'cnic_front']) . '?download=1');
        $adminDownRes->assertStatus(200);
        $adminDownRes->assertHeader('content-disposition');
    }

    /**
     * Test 6: Legal pages load successfully.
     */
    public function test_terms_and_privacy_pages_load_successfully(): void
    {
        $termsRes = $this->get(route('terms'));
        $termsRes->assertStatus(200);
        $termsRes->assertSee('Terms & Conditions', false);

        $privacyRes = $this->get(route('privacy'));
        $privacyRes->assertStatus(200);
        $privacyRes->assertSee('Document Protection Policy');
    }
}
