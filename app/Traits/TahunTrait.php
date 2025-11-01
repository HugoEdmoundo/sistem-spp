<?php
namespace App\Traits;

trait TahunTrait
{
    /**
     * Get default years range (2024-2030)
     */
    public function getTahunDefault(): array
    {
        return range(2024, 2030);
    }
    
    /**
     * Get available years from database merged with default years
     */
    public function getTahunTersedia($userId = null): array
    {
        $tahunFromData = [];
        
        if ($userId) {
            // Ambil dari pembayaran
            $tahunFromData = \App\Models\Pembayaran::where('user_id', $userId)
                ->whereNotNull('tahun')
                ->distinct()
                ->pluck('tahun')
                ->toArray();
                
            // Ambil dari tagihan  
            $tahunFromTagihan = \App\Models\Tagihan::where('user_id', $userId)
                ->whereNotNull('tahun')
                ->distinct()
                ->pluck('tahun')
                ->toArray();
                
            $tahunFromData = array_merge($tahunFromData, $tahunFromTagihan);
        }
        
        // Gabung dengan tahun default, buat unique, urutkan descending
        $allYears = array_unique(array_merge($tahunFromData, $this->getTahunDefault()));
        rsort($allYears);
        
        return $allYears;
    }
    
    /**
     * Get years for dropdown/select options
     */
    public function getTahunUntukSelect($startYear = 2024, $endYear = 2030): array
    {
        return range($startYear, $endYear);
    }
    
    /**
     * Get current year with fallback
     */
    public function getTahunSekarang(): int
    {
        return date('Y');
    }
}