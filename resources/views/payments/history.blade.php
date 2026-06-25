@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Riwayat Nota Supplier</h4>
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No Nota</th>
                    <th>Tanggal Bayar</th>
                    <th>Supplier</th>
                    <th>Periode</th>
                    <th>Total Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>
                        #{{ str_pad($payment->id,5,'0',STR_PAD_LEFT) }}
                    </td>

                    <td>
                        {{ $payment->tanggal_bayar?->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $payment->supplier->nama_supplier }}
                    </td>

                   <td>
    {{ \Carbon\Carbon::parse($payment->periode_awal)->format('d/m/Y') }}
    -
    {{ \Carbon\Carbon::parse($payment->periode_akhir)->format('d/m/Y') }}
</td>

<td>
    {{ $payment->tanggal_bayar
        ? \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d/m/Y')
        : '-' }}
</td>

                    <td>
                        Rp {{ number_format($payment->total_bayar,0,',','.') }}
                    </td>

                    <td class="d-flex gap-2">
    <a href="{{ route('payments.print',$payment->id) }}"
       target="_blank"
       class="btn btn-sm btn-primary">
        👁 Lihat Nota
    </a>

    <form action="{{ route('payments.destroy', $payment->id) }}"
          method="POST"
          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-sm btn-danger">
            🗑 Hapus
        </button>
    </form>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Belum ada nota pembayaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $payments->links() }}

    </div>
</div>

@endsection
