<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $today = Carbon::today('Asia/Jakarta');
        $attendance = Attendance::with('logs')->where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        $settings = \App\Models\Setting::getSettings();

        return view('attendance.index', compact('attendance', 'settings'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'work_type' => 'required|in:WFA,WFO,WFH',
            'notes' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Log request
        Log::info('Check-in request', [
            'user_id' => Auth::id(),
            'work_type' => $request->work_type,
            'has_location' => $request->has('latitude') && $request->has('longitude'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => now('Asia/Jakarta')->toDateTimeString()
        ]);

        $today = Carbon::today('Asia/Jakarta');
        $now = Carbon::now('Asia/Jakarta');

        // Get or create attendance record for today
        $existing = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        // Prevent check-in if already checked out today
        if ($existing && $existing->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check-out hari ini. Tidak dapat check-in lagi setelah check-out.'
            ], 400);
        }

        $locationName = null;

        // Get location name via reverse geocoding if latitude and longitude are available
        if ($request->latitude && $request->longitude) {
            try {
                $locationName = $this->getLocationName($request->latitude, $request->longitude);
            } catch (\Exception $e) {
                Log::warning('Failed to get location name', [
                    'user_id' => Auth::id(),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $data = [
            'user_id' => Auth::id(),
            'attendance_date' => $today,
            'work_type' => $request->work_type,
            'notes' => $request->notes,
            'check_in' => Carbon::now('Asia/Jakarta'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_valid' => true,
            'location_name' => $locationName,
        ];

        // Validate location for WFO (jika lokasi tersedia)
        if ($request->work_type === 'WFO') {
            if ($request->latitude && $request->longitude) {
                $settings = \App\Models\Setting::getSettings();

                Log::info('WFO Location validation', [
                    'user_id' => Auth::id(),
                    'user_location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
                    'office_location' => ['lat' => $settings->latitude, 'lng' => $settings->longitude],
                    'radius' => $settings->radius
                ]);

                if ($settings->latitude && $settings->longitude && $settings->radius) {
                    $distance = $this->calculateDistance(
                        $settings->latitude,
                        $settings->longitude,
                        $request->latitude,
                        $request->longitude
                    );

                    // Convert radius from meters to kilometers
                    $radiusKm = $settings->radius / 1000;

                    Log::info('Location distance calculation', [
                        'user_id' => Auth::id(),
                        'distance_km' => $distance,
                        'radius_km' => $radiusKm,
                        'is_valid' => $distance <= $radiusKm
                    ]);

                    if ($distance > $radiusKm) {
                        $data['location_valid'] = false;
                    }
                } else {
                    Log::warning('Office location not configured', [
                        'user_id' => Auth::id()
                    ]);
                }
            } else {
                // Jika WFO tapi tidak ada lokasi, set location_valid = false
                Log::warning('WFO check-in without location', [
                    'user_id' => Auth::id()
                ]);
                $data['location_valid'] = false;
            }
        }

        // Handle image upload to Cloudinary
        $imageUrl = null;
        if ($request->hasFile('image')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                    'folder' => 'attendance_images',
                    'resource_type' => 'image'
                ]);
                $imageUrl = $uploadedFile->getSecurePath();
                $data['image'] = $imageUrl;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
                ], 500);
            }
        }

        // Create or update attendance record
        if ($existing) {
            if (!$existing->check_in) {
                // First check-in of the day (record existed but no check_in yet)
                $data['check_in'] = $now;
                $existing->update($data);
                $attendance = $existing;
            } else {
                // Absen kedua dan seterusnya: hanya update work_type di record utama ke yang terakhir (WFO)
                // check_in / check_out TIDAK diubah (tetap jam pertama). Log tetap ada (WFA lalu WFO)
                $existing->update(['work_type' => $request->work_type]);
                $attendance = $existing;
            }
        } else {
            // First check-in of the day
            $data['check_in'] = $now;
            $attendance = Attendance::create($data);
        }

        // Simpan log untuk setiap check-in (termasuk absen kedua di hari yang sama)
        $logData = [
            'attendance_id' => $attendance->id,
            'check_in_time' => $now,
            'status' => $request->work_type,
            'notes' => $request->notes,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $locationName,
            'image' => $imageUrl,
        ];

        AttendanceLog::create($logData);

        $message = 'Check-in berhasil!';
        if ($request->work_type === 'WFO' && isset($data['location_valid']) && !$data['location_valid']) {
            $message = 'Check-in berhasil! Namun lokasi Anda berada di luar jangkauan kantor. Hubungi admin jika Anda merasa salah.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'location_valid' => $data['location_valid'] ?? true,
            'attendance' => $attendance
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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
        $isRWRTOrNumber = function ($str) {
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

    public function checkOut(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum check-in hari ini!'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check-out hari ini!'
            ], 400);
        }

        $attendance->update([
            'check_out' => Carbon::now('Asia/Jakarta')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance
        ]);
    }
}
