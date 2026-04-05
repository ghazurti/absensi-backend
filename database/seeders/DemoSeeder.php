<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Shift;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Lembur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tambah Departemen
        $depts = [
            ['kode' => 'PLU', 'nama' => 'Poli Umum', 'keterangan' => 'Pelayanan kesehatan umum'],
            ['kode' => 'IGD', 'nama' => 'IGD', 'keterangan' => 'Instalasi Gawat Darurat'],
            ['kode' => 'FAR', 'nama' => 'Farmasi', 'keterangan' => 'Unit Pelayanan Obat'],
            ['kode' => 'ADM', 'nama' => 'Administrasi', 'keterangan' => 'Unit Manajemen & SDM'],
        ];

        foreach ($depts as $dept) {
            Department::updateOrCreate(['kode' => $dept['kode']], $dept);
        }

        // 2. Tambah Pegawai Dummy
        $pegawais = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@demo.com',
                'nip' => '198501012010011001',
                'jabatan' => 'Perawat Senior',
                'unit' => 'IGD',
                'role' => 'pegawai',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@demo.com',
                'nip' => '199002022015022002',
                'jabatan' => 'Staff Administrasi',
                'unit' => 'Administrasi',
                'role' => 'pegawai',
            ],
            [
                'name' => 'dr. Andi Wijaya',
                'email' => 'andi@demo.com',
                'nip' => '198003032008011003',
                'jabatan' => 'Dokter Umum',
                'unit' => 'Poli Umum',
                'role' => 'pegawai',
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya@demo.com',
                'nip' => '199204042018022004',
                'jabatan' => 'Apoteker',
                'unit' => 'Farmasi',
                'role' => 'pegawai',
            ],
        ];

        foreach ($pegawais as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                array_merge($p, [
                    'password' => Hash::make('password'),
                ])
            );

            // 3. Tambah Jadwal Shift (7 hari terakhir s/d 7 hari kedepan)
            $startDate = now()->subDays(7);
            $endDate = now()->addDays(7);
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $isWeekend = $date->isSaturday() || $date->isSunday();
                
                // Lewati akhir pekan untuk administrasi
                if ($p['unit'] == 'Administrasi' && $isWeekend) continue;

                $shift = Shift::updateOrCreate(
                    ['user_id' => $user->id, 'tanggal' => $date->format('Y-m-d')],
                    [
                        'jenis_shift' => 'Pagi',
                        'jam_masuk' => '08:00',
                        'jam_keluar' => $date->isFriday() ? '17:00' : '16:00',
                        'keterangan' => 'Shift Demo',
                    ]
                );

                // 4. Tambah Riwayat Absensi (Untuk 7 hari terakhir saja)
                if ($date->lt(now()->startOfDay())) {
                    Absensi::updateOrCreate(
                        ['user_id' => $user->id, 'tanggal' => $date->format('Y-m-d')],
                        [
                            'shift_id' => $shift->id,
                            'check_in' => $date->copy()->setTime(7, 50 + rand(0, 20), 0),
                            'check_out' => $date->copy()->setTime(16, 0 + rand(0, 10), 0),
                            'status' => rand(1, 10) > 8 ? 'terlambat' : 'hadir',
                            'keterangan' => 'Absensi Demo',
                        ]
                    );
                }
            }
        }

        // 5. Tambah Pengajuan Demo (Pending)
        $firstPegawai = User::where('email', 'budi@demo.com')->first();
        if ($firstPegawai) {
            Izin::create([
                'user_id' => $firstPegawai->id,
                'tanggal_mulai' => now()->addDays(2),
                'tanggal_selesai' => now()->addDays(3),
                'jenis' => 'cuti',
                'keterangan' => 'Acara keluarga (Demo)',
                'status' => 'pending',
            ]);

            Lembur::create([
                'user_id' => $firstPegawai->id,
                'tanggal' => now()->subDay(),
                'jam_mulai' => '16:00',
                'jam_selesai' => '19:00',
                'keterangan' => 'Menyelesaikan laporan pasien (Demo)',
                'status' => 'pending',
            ]);
        }
    }
}
