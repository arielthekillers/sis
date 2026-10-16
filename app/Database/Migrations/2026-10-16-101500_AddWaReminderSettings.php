<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaReminderSettings extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $defaultTemplate = "{salam} Bpk/Ibu Wali Murid dari *{nama_siswa}* ({kelas}).\n\n"
            . "Kami informasikan rincian tagihan administrasi sekolah di *{nama_sekolah}* untuk periode *{periode}*:\n\n"
            . "{rincian_tagihan}\n"
            . "💰 *TOTAL TAGIHAN: {total_tagihan}*\n\n"
            . "💳 Pembayaran dapat ditransfer melalui:\n"
            . "{info_bank}\n\n"
            . "ℹ️ _Apabila Bapak/Ibu telah melakukan pembayaran sebelumnya, mohon abaikan pesan ini atau konfirmasi bukti transfer ke nomor ini._\n\n"
            . "_Ref: {kode_unik}_";

        $settings = [
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wa_reminder_day',
                'setting_value' => '10',
                'setting_label' => 'Tanggal Pengingat Tagihan Bulanan (1 - 28)',
                'setting_type'  => 'number',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wa_delay_min',
                'setting_value' => '8',
                'setting_label' => 'Jeda Acak Minimal Pengiriman (Detik Anti-Banned)',
                'setting_type'  => 'number',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wa_delay_max',
                'setting_value' => '15',
                'setting_label' => 'Jeda Acak Maksimal Pengiriman (Detik Anti-Banned)',
                'setting_type'  => 'number',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wa_reminder_template',
                'setting_value' => $defaultTemplate,
                'setting_label' => 'Template Pesan Pengingat Tagihan WhatsApp',
                'setting_type'  => 'textarea',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        foreach ($settings as $s) {
            $exist = $this->db->table('pengaturan')->where('setting_key', $s['setting_key'])->countAllResults();
            if ($exist === 0) {
                $this->db->table('pengaturan')->insert($s);
            }
        }
    }

    public function down()
    {
        $keys = ['wa_reminder_day', 'wa_delay_min', 'wa_delay_max', 'wa_reminder_template'];
        $this->db->table('pengaturan')->whereIn('setting_key', $keys)->delete();
    }
}
