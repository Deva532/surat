<?php

namespace App\Livewire\Superadmin\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;


class Index extends Component
{
    public function render()
    {
        $data = array(
            'title'  => 'Profile Lembaga',
        );
        return view(
            'livewire.superadmin.profile.index',
            $data
        );
    }

    use WithFileUploads;

    public $instansi, $dinas, $nama_sekolah, $kode_lembaga, $alamat, $kecamatan, $kabupaten, $telepon, $email, $kepala_sekolah, $nip_kepala_sekolah, $status, $logo, $logo_pemda, $logo_lama, $logo_pemda_lama;

    public function mount()
    {
        $profile = DB::table('profile')->first();

        if (!$profile) {
            $profileData = [
                'instansi'              => 'Instansi',
                'dinas'                 => 'Dinas',
                'nama_sekolah'          => 'Nama Lembaga',
                'kode_lembaga'          => 'Kode Lembaga',
                'alamat'                => '-',
                'kecamatan'             => '-',
                'kabupaten'             => '-',
                'telepon'               => '-',
                'email'                 => '-',
                'kepala_sekolah'        => '-',
                'nip_kepala_sekolah'    => '-',
                'status'                => 'Definitif',
                'logo'                  => null,
                'logo_pemda'            => null,
            ];

            $profileId = DB::table('profile')->insertGetId($profileData);
            $profile = (object) array_merge(['id' => $profileId], $profileData);
        }
        $this->instansi             = $profile->instansi;
        $this->dinas                = $profile->dinas;
        $this->nama_sekolah         = $profile->nama_sekolah;
        $this->kode_lembaga         = $profile->kode_lembaga;
        $this->alamat               = $profile->alamat;
        $this->kecamatan            = $profile->kecamatan;
        $this->kabupaten            = $profile->kabupaten;
        $this->telepon              = $profile->telepon;
        $this->email                = $profile->email;
        $this->kepala_sekolah       = $profile->kepala_sekolah;
        $this->nip_kepala_sekolah   = $profile->nip_kepala_sekolah;
        $this->status               = $profile->status;
        $this->logo_lama            = $profile->logo;
        $this->logo_pemda_lama      = $profile->logo_pemda;
    }

    public function update()
    {
        $this->validate([
            'instansi'              => 'required',
            'dinas'                 => 'required',
            'nama_sekolah'          => 'required',
            'kode_lembaga'          => 'required',
            'alamat'                => 'required',
            'kecamatan'             => 'required',
            'kabupaten'             => 'required',
            'kepala_sekolah'        => 'required',
            'status'                => 'required',
            'logo'                  => 'nullable|image|max:2048',
            'logo_pemda'            => 'nullable|image|max:2048',
        ]);

        $profile = DB::table('profile')->first();

        if (!$profile) {
            $profileId = DB::table('profile')->insertGetId([
                'instansi'      => $this->instansi,
                'dinas'         => $this->dinas,
                'nama_sekolah'  => $this->nama_sekolah,
                'kode_lembaga'  => $this->kode_lembaga,
                'alamat'        => $this->alamat,
                'kecamatan'     => $this->kecamatan,
                'kabupaten'     => $this->kabupaten,
            ]);
            $profile = (object) ['id' => $profileId];
        }


        $data = [
            'instansi'              => $this->instansi,
            'dinas'                 => $this->dinas,
            'nama_sekolah'          => $this->nama_sekolah,
            'kode_lembaga'          => $this->kode_lembaga,
            'alamat'                => $this->alamat,
            'kecamatan'             => $this->kecamatan,
            'kabupaten'             => $this->kabupaten,
            'telepon'               => $this->telepon,
            'email'                 => $this->email,
            'kepala_sekolah'        => $this->kepala_sekolah,
            'nip_kepala_sekolah'    => $this->nip_kepala_sekolah,
            'status'                => $this->status,
        ];

        // ✅ Upload Logo Sekolah
        if ($this->logo) {
            $path = $this->logo->store('profile', 'public');
            $data['logo'] = $path;
        }


        // ✅ Upload Logo Pemda
        if ($this->logo_pemda) {
            $path = $this->logo_pemda->store('profile', 'public');
            $data['logo_pemda'] = $path;
        }

        DB::table('profile')->where('id', $profile->id)->update($data);

        session()->flash('success', 'profile berhasil diperbarui');
    }
}
