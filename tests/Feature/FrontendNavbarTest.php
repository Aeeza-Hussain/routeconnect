<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FrontendNavbarTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_highlights_home_nav_link(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        // Home has the active indicator line
        $response->assertSee('Home</a>', false);
        $this->assertStringContainsString(
            'class="relative py-2 text-emerald-600 transition-colors after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-emerald-600 after:rounded-full">Home</a>',
            $response->getContent()
        );
        // Search trips does not have active classes
        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Search Trips</a>',
            $response->getContent()
        );
    }

    public function test_trips_page_highlights_search_trips_nav_link_not_home(): void
    {
        $response = $this->get('/trips');
        $response->assertOk();

        // Search Trips has the active indicator line
        $this->assertStringContainsString(
            'class="relative py-2 text-emerald-600 transition-colors after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-emerald-600 after:rounded-full">Search Trips</a>',
            $response->getContent()
        );
        // Home does not have the active indicator line
        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Home</a>',
            $response->getContent()
        );
    }

    public function test_booking_page_highlights_my_bookings_nav_link_not_home(): void
    {
        $response = $this->get('/booking');
        $response->assertOk();

        // My Bookings has the active indicator line
        $this->assertStringContainsString(
            'class="relative py-2 text-emerald-600 transition-colors after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-emerald-600 after:rounded-full">My Bookings</a>',
            $response->getContent()
        );
        // Home does not have the active indicator line
        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Home</a>',
            $response->getContent()
        );
    }
}
