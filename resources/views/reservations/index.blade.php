@extends('layouts.app')
@section('title', 'Permintaan Transportasi')
@section('content')
<style>
.resv-wrap{max-width:1200px;margin:auto;color:var(--tms-text)}.resv-head{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:24px}.resv-head h1{margin:0 0 8px;font-family:Manrope,sans-serif}.resv-muted{color:var(--tms-muted);margin:0}.resv-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;border:0;border-radius:10px;background:var(--tms-navy);color:#fff;text-decoration:none;font-weight:700;cursor:pointer}.resv-card{background:#fff;border:1px solid var(--tms-border);border-radius:16px;padding:20px;margin-bottom:16px;box-shadow:0 8px 24px #10233808}.resv-table{width:100%;border-collapse:collapse}.resv-table th,.resv-table td{text-align:left;padding:14px 10px;border-bottom:1px solid var(--tms-border);vertical-align:top}.resv-table th{color:var(--tms-muted);font-size:12px;text-transform:uppercase;letter-spacing:.06em}.resv-status{display:inline-block;border-radius:20px;padding:5px 10px;background:#eef2f7;color:#334155;font-size:12px;font-weight:700}.resv-status.Pending{background:#fff3d9;color:#8b5b00}.resv-status.Approved{background:#dff8ed;color:#08734b}.resv-status.Rejected,.resv-status.Cancelled{background:#ffe5e8;color:#a51d35}.resv-link{color:#087c79;font-weight:700;text-decoration:none}.resv-scroll{overflow-x:auto}@media(max-width:700px){.resv-head{align-items:flex-start;flex-direction:column}.resv-card{padding:12px}}
</style>
<div class="resv-wrap">
 <div class="resv-head"><div><h1>Permintaan Transportasi</h1><p class="resv-muted">Ajukan kebutuhan transportasi dan pantau keputusan admin.</p></div><a class="resv-btn" href="{{ route('reservations.create') }}">＋ Buat Permintaan</a></div>
 <section class="resv-card"><div class="resv-scroll"><table class="resv-table"><thead><tr><th>Nomor</th><th>Jadwal</th><th>Pelanggan</th><th>Layanan</th><th>Status</th><th></th></tr></thead><tbody>
 @forelse($reservations as $r)
 <tr><td><strong>{{ $r->reservation_number }}</strong><br><small class="resv-muted">{{ $r->created_at?->format('d M Y H:i') }}</small></td><td>{{ $r->service_date->format('d M Y') }}<br><small class="resv-muted">s/d {{ $r->service_end_date->format('d M Y') }}</small></td><td>{{ $r->customer_name }}</td><td>{{ $r->serviceType?->name ?? '—' }}</td><td><span class="resv-status {{ $r->status }}">{{ $r->status }}</span></td><td><a class="resv-link" href="{{ route('reservations.show',$r) }}">Detail →</a></td></tr>
 @empty<tr><td colspan="6" class="resv-muted">Belum ada permintaan transportasi.</td></tr>@endforelse
 </tbody></table></div><div style="margin-top:18px">{{ $reservations->links() }}</div></section>
</div>
@endsection
