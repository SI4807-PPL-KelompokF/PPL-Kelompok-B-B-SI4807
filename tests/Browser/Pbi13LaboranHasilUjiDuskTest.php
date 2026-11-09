<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi13LaboranHasilUjiDuskTest extends DuskTestCase
{
    /**
     * Pengujian Dusk PBI-13 (UC-13): Laboran melihat antrean tiket, membuka drawer revisi,
     * dan memverifikasi komponen dropzone serta Audit Trail.
     */
    public function testLaboranCanInspectQueueAndOpenRevisionDrawer()
    {
        $laboran = User::updateOrCreate(
            ['email' => 'laboran@telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => 'laboran',
            ]
        );

        $this->browse(function (Browser $browser) use ($laboran) {
            $browser->loginAs($laboran)
                ->visit('/laboran/pengujian')
                ->waitForText('Unggah & Revisi Hasil Uji')
                ->assertPresent('@queue-table')
                ->assertPresent('@upload-workspace-panel')
                ->assertPresent('@dropzone-normal')
                ->assertPresent('@submit-upload-button')
                ->assertPresent('@audit-trail-table')
                ->waitForText('TLY-20261015-089');

            $browser->script("document.querySelector('[dusk=\"action-upload-TLY-20261013-054\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->click('@action-upload-TLY-20261013-054')
                ->waitForText('Mode revisi')
                ->assertVisible('@revision-mode-banner')
                ->assertSee('LHU-TLY-20261013-054.pdf');
        });
    }
}
