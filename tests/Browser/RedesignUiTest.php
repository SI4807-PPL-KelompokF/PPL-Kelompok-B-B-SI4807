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
                ->waitForText('Telyuji')
                ->waitForText('Layanan Pengujian')
                ->assertPresent('#heroSlides .tu-slide.is-active')
                ->waitForText('TU-LAB-2026-0042');

            $browser->script("document.getElementById('sampleTrackingInput').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->clear('#sampleTrackingInput')
                ->type('#sampleTrackingInput', 'TU-LAB-2026-0099')
                ->click('#trackingForm button[type="submit"]')
                ->waitForText('TU-LAB-2026-0099');

            $browser->waitUntil("document.querySelectorAll('#flowTabs .tu-step').length > 1", 15);
            $browser->script("document.querySelector('#flowTabs .tu-step[data-i=\"1\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->waitForText('Registrasi online')
                ->click('#flowTabs .tu-step[data-i="1"]')
                ->waitForText('Validasi laboran');

            $browser->script("document.getElementById('tab-kimia').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->waitForText('Material & Kimia Terapan')
                ->click('#tab-kimia')
                ->assertAttribute('#tab-kimia', 'aria-selected', 'true');

            $browser->script("document.getElementById('faq-btn-1').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->waitForText('Siapa saja yang dapat mengajukan pengujian sampel di Telyuji?')
                ->click('#faq-btn-1')
                ->assertAttribute('#faq-btn-1', 'aria-expanded', 'true');

            $browser->visit('/login')
                ->waitForText('Masuk ke Telyuji')
                ->assertAttribute('#password', 'type', 'password')
                ->click('button[data-toggle-password]')
                ->assertAttribute('#password', 'type', 'text')
                ->click('button[data-toggle-password]')
                ->assertAttribute('#password', 'type', 'password');
        });
    }
}
