<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NRK</th>
            <th>Nama Karyawan</th>
            <th>No. Registrasi</th>
            <th>Jenis</th>
            <th>Tgl Terbit</th>
            <th>Tgl Berakhir</th>
            <th>Tgl Peringatan</th>
            <th>Peringatan</th>
            <th>Catatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dokumenKaryawan as $index => $dokumen)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dokumen->karyawan->NrkKry ?? '-' }}</td>
                <td>{{ $dokumen->karyawan->NamaKry ?? '-' }}</td>
                <td>{{ $dokumen->NoRegDok }}</td>
                <td>{{ $dokumen->JenisDok }}</td>
                <td>
                    @if ($dokumen->TglTerbitDok)
                        {{ \Carbon\Carbon::parse($dokumen->TglTerbitDok)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($dokumen->TglBerakhirDok)
                        {{ \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($dokumen->TglPengingat)
                        {{ \Carbon\Carbon::parse($dokumen->TglPengingat)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $dokumen->MasaPengingat ?: '-' }}</td>
                <td>{{ $dokumen->KetDok ?: '-' }}</td>
                <td>{{ $dokumen->StatusDok ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($filter)
<table style="margin-top: 20px;">
    <tr>
        <td colspan="2"><strong>Informasi Filter yang Diterapkan:</strong></td>
    </tr>
    @if(isset($filter['noreg']) && $filter['noreg'])
    <tr>
        <td>No. Registrasi</td>
        <td>{{ $filter['noreg'] }}</td>
    </tr>
    @endif
    @if(isset($filter['karyawan']) && $filter['karyawan'])
    <tr>
        <td>Nama Karyawan</td>
        <td>{{ $filter['karyawan'] }}</td>
    </tr>
    @endif
    @if(isset($filter['jenis']) && $filter['jenis'])
    <tr>
        <td>Jenis Dokumen</td>
        <td>{{ $filter['jenis'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_terbit_from']) && $filter['tgl_terbit_from'])
    <tr>
        <td>Tanggal Terbit (Dari)</td>
        <td>{{ $filter['tgl_terbit_from'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_terbit_to']) && $filter['tgl_terbit_to'])
    <tr>
        <td>Tanggal Terbit (Sampai)</td>
        <td>{{ $filter['tgl_terbit_to'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berakhir_from']) && $filter['tgl_berakhir_from'])
    <tr>
        <td>Tanggal Berakhir (Dari)</td>
        <td>{{ $filter['tgl_berakhir_from'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berakhir_to']) && $filter['tgl_berakhir_to'])
    <tr>
        <td>Tanggal Berakhir (Sampai)</td>
        <td>{{ $filter['tgl_berakhir_to'] }}</td>
    </tr>
    @endif
    @if(isset($filter['status']) && $filter['status'])
    <tr>
        <td>Status</td>
        <td>
            @if($filter['status'] == 'Warning')
                Akan Expired
            @else
                {{ $filter['status'] }}
            @endif
        </td>
    </tr>
    @endif
    <tr>
        <td>Tanggal Export</td>
        <td>{{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>
@endif