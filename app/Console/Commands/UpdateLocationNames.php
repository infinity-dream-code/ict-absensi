<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

class UpdateLocationNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:update-location-names {--force : Force update even if location_name exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update location names for existing attendance records using reverse geocoding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        // Get attendances that need location name update
        $query = Attendance::whereNotNull('latitude')
            ->whereNotNull('longitude');
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('location_name')
                  ->orWhere('location_name', '')
                  ->orWhere('location_name', 'like', 'RW%')
                  ->orWhere('location_name', 'like', 'RT%')
                  ->orWhere('location_name', 'like', '%Kota Semarang%'); // Update yang hanya kota saja
            });
        }
        
        $attendances = $query->get();
        
        if ($attendances->isEmpty()) {
            $this->info('No attendances found that need location name updates.');
            return 0;
        }
        
        $this->info("Found {$attendances->count()} attendance(s) to update.");
        
        $bar = $this->output->createProgressBar($attendances->count());
        $bar->start();
        
        $updated = 0;
        $failed = 0;
        
        foreach ($attendances as $attendance) {
            try {
                $locationName = $this->getLocationName($attendance->latitude, $attendance->longitude);
                
                if ($locationName) {
                    $attendance->update(['location_name' => $locationName]);
                    $updated++;
                } else {
                    $this->newLine();
                    $this->warn("Could not get location name for attendance ID: {$attendance->id}");
                    $failed++;
                }
                
                // Rate limiting untuk Nominatim API (1 request per second)
                usleep(1000000); // 1 second delay
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error updating attendance ID {$attendance->id}: " . $e->getMessage());
                $failed++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Update completed!");
        $this->info("Updated: {$updated}");
        $this->info("Failed: {$failed}");
        
        return 0;
    }
    
    /**
     * Get location name from coordinates using reverse geocoding (OpenStreetMap Nominatim)
     * Returns location name in format: "Village/Kelurahan, City, Province" (e.g., "Lempongsari, Kota Semarang, Jawa Tengah")
     */
    private function getLocationName($latitude, $longitude)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Absensi ICT App');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept-Language: id,en'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['address'])) {
            return null;
        }
        
        $address = $data['address'];
        $locationParts = [];
        
        // Helper function to check if string is RW/RT or just a number
        $isRWRTOrNumber = function($str) {
            $str = trim($str);
            // Check if it's RW, RT, or just numbers
            if (preg_match('/^(RW|RT)[\s\-]?\d+/i', $str)) {
                return true;
            }
            // Check if it's just a number or very short number-like string
            if (preg_match('/^\d+$/', $str) && strlen($str) <= 3) {
                return true;
            }
            return false;
        };
        
        // 1. Village/Kelurahan (prioritas utama untuk area)
        // Skip quarter/residential karena biasanya RW/RT
        if (isset($address['village']) && !$isRWRTOrNumber($address['village'])) {
            $locationParts[] = $address['village'];
        } elseif (isset($address['neighbourhood']) && !$isRWRTOrNumber($address['neighbourhood'])) {
            $locationParts[] = $address['neighbourhood'];
        } elseif (isset($address['suburb']) && !$isRWRTOrNumber($address['suburb'])) {
            // Suburb bisa jadi kelurahan, tapi skip jika RW/RT
            $locationParts[] = $address['suburb'];
        } elseif (isset($address['quarter']) && !$isRWRTOrNumber($address['quarter'])) {
            // Quarter hanya jika bukan RW/RT
            $locationParts[] = $address['quarter'];
        }
        
        // 2. City/Kota atau Town
        if (isset($address['city'])) {
            $locationParts[] = $address['city'];
        } elseif (isset($address['town'])) {
            $locationParts[] = $address['town'];
        } elseif (isset($address['municipality'])) {
            $locationParts[] = $address['municipality'];
        }
        
        // 3. Province/Provinsi
        if (isset($address['state'])) {
            $locationParts[] = $address['state'];
        } elseif (isset($address['region'])) {
            $locationParts[] = $address['region'];
        }
        
        // Jika belum dapat village/kelurahan, coba parse dari display_name
        // Biasanya format: "Road, Village/Kelurahan, Kecamatan, City, Province"
        if (empty($locationParts) && isset($data['display_name'])) {
            $displayName = $data['display_name'];
            $parts = explode(',', $displayName);
            $cleanedParts = [];
            
            foreach ($parts as $part) {
                $part = trim($part);
                // Skip jika kosong, RW/RT, atau angka saja
                if (empty($part) || $isRWRTOrNumber($part)) {
                    continue;
                }
                // Skip bagian yang hanya koordinat
                if (preg_match('/^[\d\.°\'"SNWE\s]+$/', $part)) {
                    continue;
                }
                // Skip jika mengandung kode pos (5 digit angka)
                if (preg_match('/^\d{5}$/', $part)) {
                    continue;
                }
                $cleanedParts[] = $part;
            }
            
            // Cari village/kelurahan (biasanya bagian ke-2 atau ke-3 setelah road)
            // Format umum: "Jl. X, Lempongsari, Kecamatan Y, Kota Semarang, Jawa Tengah"
            $village = null;
            $city = null;
            $province = null;
            
            foreach ($cleanedParts as $index => $part) {
                // Cari village (biasanya tidak mengandung "Kota", "Kecamatan", "Jl", "Jalan")
                if (!$village && !preg_match('/\b(kota|kabupaten|kecamatan|kab|kec|jl|jalan|street|road|kota|semarang)\b/i', $part)) {
                    if (strlen($part) > 3 && $index < 3) {
                        $village = $part;
                    }
                }
                
                // Cari city (biasanya mengandung "Kota" atau nama kota besar)
                if (!$city && preg_match('/\b(kota|semarang|surabaya|jakarta|bandung|yogyakarta|malang)\b/i', $part)) {
                    $city = $part;
                }
                
                // Cari province (biasanya "Jawa Tengah", "Jawa Barat", dll)
                if (!$province && preg_match('/\b(jawa|sumatera|kalimantan|sulawesi|bali|ntb|ntt|papua)\b/i', $part)) {
                    $province = $part;
                }
            }
            
            // Rebuild dengan format yang benar
            if ($village || $city || $province) {
                $locationParts = [];
                if ($village) $locationParts[] = $village;
                if ($city) $locationParts[] = $city;
                if ($province) $locationParts[] = $province;
                
                if (!empty($locationParts)) {
                    return implode(', ', $locationParts);
                }
            }
            
            // Fallback: ambil 2-3 bagian tengah (biasanya area, city, province)
            if (count($cleanedParts) >= 2) {
                $startIdx = min(1, count($cleanedParts) - 2); // Mulai dari index 1 (skip road jika ada)
                $endIdx = min($startIdx + 3, count($cleanedParts));
                $relevantParts = array_slice($cleanedParts, $startIdx, $endIdx - $startIdx);
                
                if (!empty($relevantParts)) {
                    return implode(', ', $relevantParts);
                }
            }
        }
        
        // Return locationParts jika sudah ada
        if (!empty($locationParts)) {
            return implode(', ', $locationParts);
        }
        
        return null;
    }
}
