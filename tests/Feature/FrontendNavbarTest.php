<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FrontendNavbarTest extends TestCase
{
    use RefreshDatabase;

    // ─── Home Page ────────────────────────────────────────────────────────────

    public function test_home_page_activates_home_nav_link(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Home must have the active colour class and the underline span
        $this->assertStringContainsString('class="relative py-2 text-emerald-600 transition-colors"', $html,
            'Home link should have active class on home page');

        // No other link should have the underline-span directly after "Search Trips" or "My Bookings" text
        $this->assertStringNotContainsString(
            '>Search Trips' . PHP_EOL . '                        <span class="absolute bottom-0',
            $html,
            'Search Trips should NOT be active on home page'
        );
    }

    public function test_home_page_does_not_bold_other_links(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $html = $response->getContent();

        // Search Trips and My Bookings must use the inactive class
        $this->assertStringContainsString(
            '>Search Trips</a>',
            $html
        );
        $this->assertStringContainsString(
            '>My Bookings</a>',
            $html
        );
    }

    // ─── Search Trips Page ────────────────────────────────────────────────────

    public function test_trips_page_activates_search_trips_link(): void
    {
        $response = $this->get('/trips');
        $response->assertOk();
        $html = $response->getContent();

        // Search Trips link should be wrapped in the active <a> with the underline span
        $this->assertStringContainsString(
            'Search Trips',
            $html
        );
        // The active state uses "relative py-2 text-emerald-600" — check it appears before "Search Trips"
        // and that Home does NOT use the active class
        $homeActivePos  = strpos($html, '>Home' . PHP_EOL . '                        <span class="absolute bottom-0');
        $this->assertFalse($homeActivePos, 'Home should NOT be active on trips page');
    }

    public function test_trips_page_home_link_is_not_active(): void
    {
        $response = $this->get('/trips');
        $response->assertOk();
        $html = $response->getContent();

        // Home link should use the non-active inline anchor (no underline span sibling in desktop nav)
        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Home</a>',
            $html,
            'Home must use inactive styling on the trips page'
        );
    }

    // ─── My Bookings Page ─────────────────────────────────────────────────────

    public function test_booking_page_home_link_is_not_active(): void
    {
        $response = $this->get('/booking');
        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Home</a>',
            $html,
            'Home must use inactive styling on the booking page'
        );
    }

    public function test_booking_page_search_trips_link_is_not_active(): void
    {
        $response = $this->get('/booking');
        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString(
            'class="py-2 hover:text-emerald-600 transition-colors">Search Trips</a>',
            $html,
            'Search Trips must use inactive styling on the booking page'
        );
    }

    // ─── Active class appears exactly once per page ───────────────────────────

    public function test_exactly_one_nav_link_has_active_underline_span_on_home(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $html = $response->getContent();

        // Count how many desktop nav links use the underline span
        $count = substr_count($html, '<span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-full"></span>');
        // Two spans: one desktop + one mobile (both Home active on home page)
        $this->assertGreaterThanOrEqual(1, $count, 'At least one active underline span should exist on home page');
        $this->assertLessThanOrEqual(2, $count, 'At most two active spans (desktop + mobile) should exist');
    }

    public function test_exactly_one_nav_link_has_active_underline_span_on_trips(): void
    {
        $response = $this->get('/trips');
        $response->assertOk();
        $html = $response->getContent();

        $count = substr_count($html, '<span class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-full"></span>');
        $this->assertGreaterThanOrEqual(1, $count, 'At least one active underline span should exist on trips page');
        $this->assertLessThanOrEqual(2, $count, 'At most two active spans (desktop + mobile) should exist');
    }
}
