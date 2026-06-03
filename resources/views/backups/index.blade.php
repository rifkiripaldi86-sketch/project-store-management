@extends('layouts.app')
@section('title', 'Backup Database')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fas fa-database me-2"></i> Backup Database</h3>
    <form method="POST" action="{{ route('backup.create') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Backup Manual</button>
    </form>
</div>
<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr><th>No</th><th>Nama File</th><th>Tanggal</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($backups as $index => $backup)
                <tr>
                    <td>{{ $index + $backups->firstItem() }}</td>
                    <td>{{ $backup->file_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($backup->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('backup.download', $backup) }}" class="btn btn-sm btn-primary"><i class="fas fa-download"></i> Download</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Belum ada backup</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $backups->links() }}
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> Backup otomatis setiap hari jam 01:00. File tersimpan di storage/app/backups/
        </div>
    </div>
</div>
@endsection