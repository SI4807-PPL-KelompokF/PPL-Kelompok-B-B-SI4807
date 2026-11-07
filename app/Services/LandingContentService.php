<?php

namespace App\Services;

class LandingContentService
{
    /**
     * Daftar slide foto fasilitas laboratorium dan gedung kampus pada Hero Section.
     */
    public function heroSlides(): array
    {
        return [
            ['src' => asset('images/buildings/tult-tower.jpg'), 'alt' => 'Fasad Menara TULT Telkom University'],
            ['src' => asset('images/buildings/btp.jpg'), 'alt' => 'Kawasan Bandung Techno Park Telkom University'],
            ['src' => asset('images/buildings/gku.jpg'), 'alt' => 'Gedung Kuliah Umum Telkom University'],
            ['src' => asset('images/buildings/gedung-bangkit.jpg'), 'alt' => 'Gedung Bangkit Rektorat Telkom University'],
            ['src' => asset('images/buildings/lab-kedokteran.jpg'), 'alt' => 'Laboratorium Biosains dan Medis Telkom University'],
            ['src' => asset('images/buildings/lab-fabrikasi.jpg'), 'alt' => 'Laboratorium Fabrikasi dan Prototipe Telkom University'],
            ['src' => asset('images/buildings/lab-praktikum.jpg'), 'alt' => 'Laboratorium Elektronika dan Instrumen Telkom University'],
        ];
    }

    /**
     * Daftar tahapan Alur Standar Pengujian Sampel (5 tahap).
     */
    public function flowSteps(): array
    {
        return [
            [
                'n' => '01',
                'title' => 'Pengajuan',
                'sub' => 'Registrasi online',
                'sc' => '#ED1E28',
                'st' => '#ED1E28',
                'items' => [
                    'Pilih instrumen & parameter pengujian yang dibutuhkan',
                    'Unggah dokumen pendukung (Surat Pengantar / MSDS)',
                    'Tentukan jumlah & sifat spesimen sampel',
                    'Dapatkan Nomor Resi & E-Ticket Pengujian resmi',
                ],
            ],
            [
                'n' => '02',
                'title' => 'Verifikasi',
                'sub' => 'Validasi laboran',
                'sc' => '#F26A2D',
                'st' => '#C2410C',
                'items' => [
                    'Pemeriksaan kelayakan berkas online oleh laboran',
                    'Penyerahan fisik spesimen di laboratorium terkait',
                    'Validasi kondisi spesimen & kelayakan wadah uji',
                    'Penetapan jadwal antrean dan kesiapan instrumen',
                ],
            ],
            [
                'n' => '03',
                'title' => 'Preparasi',
                'sub' => 'Persiapan & kalibrasi',
                'sc' => '#F7D488',
                'st' => '#B45309',
                'items' => [
                    'Preparasi kimiawi & fisik spesimen oleh tim laboran',
                    'Kalibrasi instrumen uji sesuai SOP baku',
                    'Blank testing & setup baseline pengukuran alat',
                    'Pencatatan log book kondisi lingkungan lab',
                ],
            ],
            [
                'n' => '04',
                'title' => 'Pengujian',
                'sub' => 'Eksekusi instrumen',
                'sc' => '#EAEFB1',
                'st' => '#3F6212',
                'items' => [
                    'Pengujian dengan alat laboratorium terakreditasi',
                    'Akumulasi data spektrum / pengukuran kuantitatif',
                    'Replikasi pengujian untuk menjamin presisi data',
                    'Input rekapitulasi data hasil uji oleh laboran',
                ],
            ],
            [
                'n' => '05',
                'title' => 'Sertifikasi',
                'sub' => 'Penerbitan LHU resmi',
                'sc' => '#E9F7CA',
                'st' => '#047857',
                'items' => [
                    'Validasi data hasil pengujian oleh Kepala Lab',
                    'Pengesahan dokumen digital dengan QR Barcode resmi',
                    'Laporan Hasil Uji (LHU) terbit di sistem Telyuji',
                    'Unduh sertifikat PDF resmi & pengambilan residu sampel',
                ],
            ],
        ];
    }

    /**
     * Daftar katalog instrumen laboratorium lintas rumpun keilmuan.
     */
    public function catalogInstruments(): array
    {
        return [
            [
                'category' => 'kimia',
                'title' => 'Spektrofotometer UV-Vis',
                'location' => 'Lab Material Maju',
                'desc' => 'Pengukuran absorbansi larutan, penentuan konsentrasi sampel cair, dan analisis spektral panjang gelombang 190–1100 nm.',
                'sla' => '2-3 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-kedokteran.jpg'),
            ],
            [
                'category' => 'kimia',
                'title' => 'FTIR Spectrometer',
                'location' => 'Lab Kimia Terpadu',
                'desc' => 'Identifikasi gugus fungsi senyawa organik, polimer, dan analisis struktur molekuler material serbuk maupun film tipis.',
                'sla' => '3-4 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-praktikum.jpg'),
            ],
            [
                'category' => 'kimia',
                'title' => 'Universal Testing Machine (UTM)',
                'location' => 'Lab Uji Mekanik',
                'desc' => 'Pengujian mekanik material meliputi uji tarik (tensile), uji tekan (compression), dan bending spesimen standar ASTM.',
                'sla' => '3-5 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-fabrikasi.jpg'),
            ],
            [
                'category' => 'telekomunikasi',
                'title' => 'Vector Network Analyzer (VNA)',
                'location' => 'Lab Gelombang Mikro FTE',
                'desc' => 'Analisis parameter S (S11, S21), impedansi frekuensi tinggi, dan karakterisasi rangkaian gelombang mikro hingga 14 GHz.',
                'sla' => '2-4 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/btp.jpg'),
            ],
            [
                'category' => 'telekomunikasi',
                'title' => 'Anechoic Chamber & Antena Testing',
                'location' => 'Lab Antena & Propagasi',
                'desc' => 'Pengukuran pola radiasi antena (radiation pattern), gain antena, dan polarisasi tanpa interferensi sinyal eksternal.',
                'sla' => '3-5 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-fabrikasi.jpg'),
            ],
            [
                'category' => 'telekomunikasi',
                'title' => 'Digital Oscilloscope 1 GHz',
                'location' => 'Lab Embedded & IoT',
                'desc' => 'Verifikasi integritas sinyal digital kecepatan tinggi, analisis protokol komunikasi serial I2C/SPI/UART.',
                'sla' => '1-2 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1581092162384-8987c1d64718?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/btp.jpg'),
            ],
            [
                'category' => 'bioteknologi',
                'title' => 'Automated Water Quality Analyzer',
                'location' => 'Lab Rekayasa Lingkungan',
                'desc' => 'Pengujian parameter COD, BOD, TSS, kekeruhan, dan keasaman sampel perairan lingkungan secara terotomasi.',
                'sla' => '2-4 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-kedokteran.jpg'),
            ],
            [
                'category' => 'bioteknologi',
                'title' => 'Inkubator Mikrobiologi Presisi',
                'location' => 'Lab Biosains & Terapan',
                'desc' => 'Kultivasi bakteri dan mikroorganisme dengan kontrol suhu stabil 0.1°C untuk uji antibakteri dan bioassay.',
                'sla' => '3-5 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-kedokteran.jpg'),
            ],
            [
                'category' => 'bioteknologi',
                'title' => 'Centrifuge Kecepatan Tinggi',
                'location' => 'Lab Sentral Riset',
                'desc' => 'Separasi fraksi seluler, presipitasi protein, dan isolasi DNA/RNA dengan pendinginan suhu terkontrol.',
                'sla' => '1-2 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-praktikum.jpg'),
            ],
            [
                'category' => 'komputasi',
                'title' => 'Cluster Server Simulasi Pemodelan',
                'location' => 'Lab High Performance Computing',
                'desc' => 'Komputasi paralel untuk simulasi dinamika fluida (CFD), kalkulasi kimia komputasi DFT, dan pemodelan numerik.',
                'sla' => '2-5 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/btp.jpg'),
            ],
            [
                'category' => 'komputasi',
                'title' => 'Testing Rig Embedded AI & Edge',
                'location' => 'Lab AI & Robotika',
                'desc' => 'Uji performa inferensi model kecerdasan buatan, konsumsi daya instrumen, dan stabilitas pada perangkat edge.',
                'sla' => '2-3 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-fabrikasi.jpg'),
            ],
            [
                'category' => 'komputasi',
                'title' => 'Stasiun Kerja Citra Biomedis',
                'location' => 'Lab Rekayasa Biomedika',
                'desc' => 'Pemrosesan citra medis DICOM, segmentasi organ 3D, dan pelatihan deep learning klasifikasi data laboratorium.',
                'sla' => '1-3 Hari Kerja',
                'image' => 'https://images.unsplash.com/photo-1530497610245-94d3c16cda28?auto=format&fit=crop&w=800&q=80',
                'fallback' => asset('images/buildings/lab-kedokteran.jpg'),
            ],
        ];
    }

    /**
     * Daftar Pertanyaan Umum (FAQ) pada halaman beranda.
     */
    public function faqs(): array
    {
        return [
            [
                'question' => 'Siapa saja yang dapat mengajukan pengujian sampel di Telyuji?',
                'answer' => 'Seluruh mahasiswa, dosen, dan peneliti aktif Telkom University dapat mengajukan pengujian menggunakan akun kampus untuk tugas akhir maupun riset.',
            ],
            [
                'question' => 'Apakah layanan pengujian ini dikenakan biaya?',
                'answer' => 'Gratis (100% bebas biaya) bagi seluruh sivitas akademika internal Telkom University sesuai kebijakan fasilitas riset kampus.',
            ],
            [
                'question' => 'Di mana lokasi penyerahan fisik sampel di kampus?',
                'answer' => 'Sampel diserahkan langsung ke Ruang Laboran di Gedung TULT atau laboratorium fakultas terkait sesuai lokasi yang tertera pada E-Ticket Anda.',
            ],
            [
                'question' => 'Berapa lama rata-rata durasi waktu pengujian sampel?',
                'answer' => 'Rata-rata 2–5 hari kerja sejak sampel fisik divalidasi laboran, dan dokumen LHU resmi dapat langsung diunduh dari dashboard.',
            ],
        ];
    }

    /**
     * Daftar label instrumen pada pita berjalan (marquee ribbon) di bagian CTA.
     */
    public function ribbonInstruments(): array
    {
        return [
            'UV-Vis',
            'FTIR',
            'UTM',
            'VNA',
            'Oscilloscope',
            'Anechoic Chamber',
            'Water Quality',
            'Incubator',
            'Centrifuge',
            'HPC Cluster',
            'Edge AI Rig',
            'Biomedical Imaging',
        ];
    }
}
