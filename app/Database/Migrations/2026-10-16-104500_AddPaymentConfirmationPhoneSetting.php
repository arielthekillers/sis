<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentConfirmationPhoneSetting extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();

        // 1. Cek apakah payment_confirmation_phone sudah ada
        $check = $db->table('pengaturan')->where('setting_key', 'payment_confirmation_phone')->countAllResults();
        if ($check === 0) {
            $db->table('pengaturan')->insert([
                'setting_group' => 'keuangan',
                'setting_key'   => 'payment_confirmation_phone',
                'setting_value' => '081234567890',
                'setting_label' => 'No. WhatsApp Konfirmasi Pembayaran (Bendahara / TU)',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        // 2. Perbarui default wa_reminder_template agar memuat info datang langsung & nomor konfirmasi
        $newTemplate = "{salam} Bpk/Ibu Wali Murid dari *{nama_siswa}* ({kelas}).\n\n"
            . "Kami informasikan rincian tagihan administrasi sekolah di *{nama_sekolah}* untuk periode *{periode}*:\n\n"
            . "{rincian_tagihan}\n"
            . "💰 *TOTAL TAGIHAN: {total_tagihan}*\n\n"
            . "💳 Pembayaran dapat ditransfer melalui:\n"
            . "{info_bank}\n"
            . "atau dapat dibayarkan langsung secara tunai di loket kasir/tata usaha sekolah.\n\n"
            . "📲 Konfirmasi bukti transfer pembayaran melalui WhatsApp ke:\n"
            . "*{nomor_konfirmasi}*\n\n"
            . "ℹ️ _Apabila Bapak/Ibu telah melakukan pembayaran sebelumnya, mohon abaikan pesan ini._\n\n"
            . "_Ref: {kode_unik}_";

        $db->table('pengaturan')
            ->where('setting_key', 'wa_reminder_template')
            ->update([
                'setting_value' => $newTemplate,
                'updated_at'    => $now,
            ]);
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('pengaturan')->where('setting_key', 'payment_confirmation_phone')->delete();
    }
}
