<?php

namespace App\Notifications;

use App\Models\KGB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KgbJatuhTempoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public KGB $kgb,
        public int $hari,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification (disimpan ke tabel notifications).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => 'KGB Jatuh Tempo',
            'pesan' => sprintf(
                'KGB %s (tmt %s) akan jatuh tempo dalam %d hari — segera proses kenaikan gaji berkala.',
                $this->kgb->pegawai?->nama ?? 'Pegawai',
                $this->kgb->tmt_kgb?->format('d-m-Y'),
                $this->hari,
            ),
            'kgb_id' => $this->kgb->id,
            'pegawai_id' => $this->kgb->pegawai_id,
            'pegawai_nama' => $this->kgb->pegawai?->nama,
            'tmt_kgb' => $this->kgb->tmt_kgb?->toDateString(),
            'url' => url("/notifikasi_kgb/data_notifikasi_kgb"),
        ];
    }
}
