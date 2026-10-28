@extends('layouts.app')

@section('title', 'Preview SPK ' . $spk->spk_number)

@section('content')
<div class="service-show-page">
    <header class="main-header">
        <div class="service-show-heading">
            <div class="service-show-eyebrow">
                <span></span> TRANSPORT OPERATIONS / PREVIEW SPK
            </div>
            <h1>{{ $spk->spk_number }}</h1>
            <p>Preview internal untuk layanan <strong>{{ $service->service_code }}</strong></p>
        </div>

        <div class="service-show-actions">
            <a href="{{ route('services.show', $service) }}" class="service-show-back">
                Kembali ke Detail
            </a>
            <button type="button" class="service-show-back" onclick="window.print()">
                Cetak SPK
            </button>
        </div>
    </header>

    <div class="spk">
        <div class="spkcard">
            <div class="muted">BALI KAMI TOUR · INTERNAL DIGITAL SPK</div>
            <h1>{{ $spk->spk_number }}</h1>
            <p>Layanan: {{ $service->service_code }}</p>
            <p>Status SPK: <strong>{{ $spk->status }}</strong></p>
        </div>

        <div class="spkcard">
            <h3>Detail Layanan</h3>
            <p><strong>Tanggal layanan:</strong>
                {{ $service->start_date ?? '-' }}
                @if (!empty($service->end_date))
                    — {{ $service->end_date }}
                @endif
            </p>
            <p><strong>Jenis layanan:</strong> {{ $service->serviceType->name ?? '-' }}</p>
            <p><strong>Nama pelanggan:</strong> {{ $spk->customer_name ?? '-' }}</p>
            <p><strong>Lokasi penjemputan:</strong> {{ $spk->pickup_location ?? '-' }}</p>
            <p><strong>Catatan:</strong> {{ $spk->notes ?? '-' }}</p>
        </div>

        <div class="spkcard">
            <h3>Driver dan Kendaraan</h3>
            <p><strong>Driver:</strong> {{ $service->driver->name ?? '-' }}</p>
            <p><strong>Kendaraan:</strong> {{ $service->vehicle->name ?? '-' }}</p>
            <p><strong>Nomor polisi:</strong> {{ $service->vehicle->plate_number ?? '-' }}</p>
        </div>

        <div class="spkcard">
            <h3>Itinerary / Stop</h3>
            @forelse ($service->stops->sortBy('sort_order') as $stop)
                <div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <p><strong>{{ $loop->iteration }}. {{ $stop->name ?? 'Stop' }}</strong></p>
                    <p>{{ $stop->address ?? '-' }}</p>
                    @if (!empty($stop->note))
                        <p>{{ $stop->note }}</p>
                    @endif
                    @if (!empty($stop->status))
                        <p>Status: {{ $stop->status }}</p>
                    @endif
                </div>
            @empty
                <p>Belum ada itinerary atau stop.</p>
            @endforelse
        </div>

        <div class="spkcard">
            <h3>Kontak Darurat</h3>
            <p><strong>Nama:</strong> {{ $spk->emergency_name ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $spk->emergency_phone ?? '-' }}</p>
            <p><strong>WhatsApp:</strong> {{ $spk->emergency_whatsapp ?? '-' }}</p>
        </div>

        <p class="muted">Dokumen internal. Halaman ini hanya tersedia melalui aplikasi yang memerlukan login.</p>
    </div>
</div>
@endsection
