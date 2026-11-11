<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCctvTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_kamera' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_kamera' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'ip_public' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'port' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 554,
            ],
            'stream_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tipe_stream' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'placeholder', // placeholder, embed, hls, mjpeg, rtsp
            ],
            'jadwal_operasional' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => '07.30 - 16.30 WIB',
            ],
            'resolusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '1080p FHD',
            ],
            'target_program' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Fullday', // Fullday, Semua
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'aktif', // aktif, nonaktif, maintenance
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_kamera');
        $this->forge->addKey('status');
        $this->forge->createTable('cctv_kamera', true);

        // Seed 4 sample cameras for school environment
        $now = date('Y-m-d H:i:s');
        $data = [
            [
                'kode_kamera'        => 'CAM-01',
                'nama_kamera'        => 'Ruang Kelas Utama Kinder',
                'lokasi'             => 'Indoor - Lantai 1',
                'ip_public'          => '103.148.12.34',
                'port'               => 554,
                'stream_url'         => '',
                'tipe_stream'        => 'placeholder',
                'jadwal_operasional' => '07.30 - 16.30 WIB',
                'resolusi'           => '1080p FHD',
                'target_program'     => 'Fullday',
                'keterangan'         => 'Area kegiatan belajar mengajar, circle time, dan interaksi ananda bersama pendidik.',
                'status'             => 'aktif',
                'urutan'             => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'kode_kamera'        => 'CAM-02',
                'nama_kamera'        => 'Ruang Istirahat / Nap Room',
                'lokasi'             => 'Indoor - Lantai 2',
                'ip_public'          => '103.148.12.34',
                'port'               => 555,
                'stream_url'         => '',
                'tipe_stream'        => 'placeholder',
                'jadwal_operasional' => '11.30 - 14.30 WIB',
                'resolusi'           => '1080p FHD',
                'target_program'     => 'Fullday',
                'keterangan'         => 'Ruang tidur siang ananda program Fullday yang tenang, ber-AC, dan didampingi wali kelas.',
                'status'             => 'aktif',
                'urutan'             => 2,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'kode_kamera'        => 'CAM-03',
                'nama_kamera'        => 'Playground & Area Bermain Luar',
                'lokasi'             => 'Outdoor - Halaman Depan',
                'ip_public'          => '103.148.12.34',
                'port'               => 556,
                'stream_url'         => '',
                'tipe_stream'        => 'placeholder',
                'jadwal_operasional' => '08.00 - 16.00 WIB',
                'resolusi'           => '1080p FHD',
                'target_program'     => 'Fullday',
                'keterangan'         => 'Area perosotan, ayunan, dan stimulasi motorik kasar di udara terbuka terlindungi rumput sintetis.',
                'status'             => 'aktif',
                'urutan'             => 3,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'kode_kamera'        => 'CAM-04',
                'nama_kamera'        => 'Dining Hall / Ruang Makan Bersama',
                'lokasi'             => 'Indoor - Lantai 1',
                'ip_public'          => '103.148.12.34',
                'port'               => 557,
                'stream_url'         => '',
                'tipe_stream'        => 'placeholder',
                'jadwal_operasional' => '10.30 - 15.30 WIB',
                'resolusi'           => '1080p FHD',
                'target_program'     => 'Fullday',
                'keterangan'         => 'Tempat makan siang bergizi dan snack sore santai ananda.',
                'status'             => 'aktif',
                'urutan'             => 4,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];

        $this->db->table('cctv_kamera')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('cctv_kamera', true);
    }
}

