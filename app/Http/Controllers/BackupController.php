<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(10);
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        $filename = 'backup_' . now()->format('Ymd_His') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Gunakan mysqldump dengan config() agar tetap bekerja setelah config:cache
        $dbConfig = config('database.connections.mysql');

        $command = sprintf(
            'mysqldump --user=%s --host=%s --port=%s %s > %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['port'] ?? '3306'),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($path)
        );

        // Kirim password via environment variable agar tidak terekspose di process list
        putenv('MYSQL_PWD=' . $dbConfig['password']);
        exec($command, $output, $returnVar);
        putenv('MYSQL_PWD');

        if ($returnVar === 0) {
            $backup = Backup::create([
                'file_name' => $filename,
                'file_path' => 'backups/' . $filename,
            ]);
            return redirect()->route('backup.index')->with('success', 'Backup berhasil dibuat');
        } else {
            return back()->with('error', 'Gagal membuat backup');
        }
    }

    public function download($id)
    {
        $backup = Backup::findOrFail($id);
        $filePath = storage_path('app/' . $backup->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $backup->file_name);
        } else {
            return back()->with('error', 'File tidak ditemukan');
        }
    }
}