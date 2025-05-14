<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DB::table('sales')
            ->select([
                'id', 'siparis_id', 'status', 'musteri_adi', 'telefon',
                'kamp_adi', 'tarih', 'konaklama', 'paket',
                'katilimci1_ad_soyad', 'katilimci1_tc', 'katilimci1_dt',
                'katilimci2_ad_soyad', 'katilimci2_tc', 'katilimci2_dt',
                'katilimci3_ad_soyad', 'katilimci3_tc', 'katilimci3_dt',
                'katilimci4_ad_soyad', 'katilimci4_tc', 'katilimci4_dt',
                'katilimci5_ad_soyad', 'katilimci5_tc', 'katilimci5_dt',
                'para_birimi', 'odeme_yontemi', 'urun_fiyati', 'toplam_masraf',
                'musteri_notu'
            ]);

        // Filtreler
        if ($this->request->has('camp') && $this->request->camp) {
            $query->where('kamp_adi', $this->request->camp);
        }

        if ($this->request->has('status') && $this->request->status) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->has('date_from') && $this->request->date_from) {
            $query->where('tarih', '>=', $this->request->date_from);
        }

        if ($this->request->has('date_to') && $this->request->date_to) {
            $query->where('tarih', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Sipariş ID',
            'Durum',
            'Müşteri',
            'Telefon',
            'Kamp',
            'Tarih',
            'Konaklama',
            'Paket',
            'Katılımcı 1',
            'TC No',
            'Doğum Tarihi',
            'Katılımcı 2',
            'TC No',
            'Doğum Tarihi',
            'Katılımcı 3',
            'TC No',
            'Doğum Tarihi',
            'Katılımcı 4',
            'TC No',
            'Doğum Tarihi',
            'Katılımcı 5',
            'TC No',
            'Doğum Tarihi',
            'Para Birimi',
            'Ödeme Yöntemi',
            'Satış Fiyatı',
            'Toplam Gider',
            'Kâr',
            'Müşteri Notu'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $profit = $row->urun_fiyati - $row->toplam_masraf;

        return [
            $row->siparis_id,
            $row->status,
            $row->musteri_adi,
            $row->telefon,
            $row->kamp_adi,
            $row->tarih,
            $row->konaklama,
            $row->paket,
            $row->katilimci1_ad_soyad,
            $row->katilimci1_tc,
            $row->katilimci1_dt,
            $row->katilimci2_ad_soyad,
            $row->katilimci2_tc,
            $row->katilimci2_dt,
            $row->katilimci3_ad_soyad,
            $row->katilimci3_tc,
            $row->katilimci3_dt,
            $row->katilimci4_ad_soyad,
            $row->katilimci4_tc,
            $row->katilimci4_dt,
            $row->katilimci5_ad_soyad,
            $row->katilimci5_tc,
            $row->katilimci5_dt,
            $row->para_birimi,
            $row->odeme_yontemi,
            $row->urun_fiyati,
            $row->toplam_masraf,
            $profit,
            $row->musteri_notu
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // İlk satırı kalın yapma
            1 => ['font' => ['bold' => true]],
        ];
    }
}
