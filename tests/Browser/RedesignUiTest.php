<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RedesignUiTest extends DuskTestCase
{
    /**
     * Verify redesigned landing page interactive components and auth UI.
     *
     * @return void
     */
    public function testRedesignLandingAndAuthInteractions()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Telyuji')
                ->assertSee('Layanan Pengujian')
                ->assertPresent('#heroSlides .tu-slide.is-active')
                ->waitForText('TU-LAB-2026-0042');

            $browser->script("document.getElementById('sampleTrackingInput').scrollIntoView({block: 'center'});");
            $browser->clear('#sampleTrackingInput')
                ->type('#sampleTrackingInput', 'TU-LAB-2026-0099')
                ->click('#trackingForm button[type="submit"]')
                ->waitForText('TU-LAB-2026-0099');

            $browser->waitFor('#flow-tab-1');
            $browser->script("document.getElementById('flow-tab-1').scrollIntoView({block: 'center'});");
            $browser->click('#flow-tab-1')
                ->waitForText('Validasi laboran');

            $browser->script("document.getElementById('tab-kimia').scrollIntoView({block: 'center'});");
            $browser->click('#tab-kimia')
                ->assertAttribute('#tab-kimia', 'aria-selected', 'true');

            $browser->script("document.getElementById('faq-btn-1').scrollIntoView({block: 'center'});");
            $browser->click('#faq-btn-1')
                ->assertAttribute('#faq-btn-1', 'aria-expanded', 'true');

            $browser->visit('/login')
                ->waitForText('Masuk ke Telyuji')
                ->assertAttribute('#password', 'type', 'password')
                ->click('button[data-toggle-password="password"]')
                ->assertAttribute('#password', 'type', 'text')
                ->click('button[data-toggle-password="password"]')
                ->assertAttribute('#password', 'type', 'password');
        });
    }
}

