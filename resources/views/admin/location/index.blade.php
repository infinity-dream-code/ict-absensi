@extends('admin.layouts.app')

@section('title', 'Set Lokasi')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .page-header {
        margin-bottom: 32px;
    }
    
    .page-title {
        font-size: 32px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .page-subtitle {
        font-size: 16px;
        color: #6b7280;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        outline: none;
    }
    
    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }
    
    .error-message {
        color: #dc2626;
        font-size: 14px;
        margin-top: 6px;
    }
    
    .form-actions {
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }
    
    .btn-submit {
        padding: 14px 32px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    
    .info-box {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .info-box p {
        color: #1e40af;
        font-size: 14px;
        margin: 0;
    }
    
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        margin-bottom: 24px;
    }
    
    .map-container {
        position: relative;
    }
    
    .btn-locate {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        padding: 10px 16px;
        background: white;
        border: 2px solid #667eea;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    
    .btn-locate:hover {
        background: #667eea;
        color: white;
    }
    
    input[type="range"] {
        -webkit-appearance: none;
        appearance: none;
    }
    
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #667eea;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.2s;
    }
    
    input[type="range"]::-webkit-slider-thumb:hover {
        background: #4f46e5;
        transform: scale(1.1);
    }
    
    input[type="range"]::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #667eea;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.2s;
    }
    
    input[type="range"]::-moz-range-thumb:hover {
        background: #4f46e5;
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Set Lokasi</h1>
    <p class="page-subtitle">Atur lokasi kantor untuk absensi</p>
</div>

<div class="settings-card">
    <div class="info-box">
        <p><i class="fas fa-info-circle"></i> Set lokasi kantor untuk validasi absensi berdasarkan GPS</p>
    </div>
    
    <form action="{{ route('admin.location.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="location_name" class="form-label">Nama Lokasi</label>
            <input type="text" 
                   id="location_name" 
                   name="location_name" 
                   value="{{ old('location_name', $settings->location_name) }}"
                   placeholder="Contoh: Kantor Pusat Jakarta"
                   class="form-input">
            @error('location_name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="form-group">
            <label class="form-label">Pilih Lokasi di Peta</label>
            <div class="map-container">
                <div id="map"></div>
                <button type="button" class="btn-locate" onclick="getCurrentLocation()">
                    <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
                </button>
            </div>
            <p class="form-help">
                <i class="fas fa-info-circle"></i> 
                Klik pada peta, drag pin (marker), atau gunakan tombol untuk mendapatkan lokasi saat ini
            </p>
        </div>
        
        <div class="form-group">
            <label for="latitude" class="form-label">Latitude</label>
            <input type="number" 
                   id="latitude" 
                   name="latitude" 
                   step="0.00000001"
                   value="{{ old('latitude', $settings->latitude) }}"
                   placeholder="Contoh: -6.2088"
                   class="form-input"
                   readonly>
            @error('latitude')
                <p class="error-message">{{ $message }}</p>
            @enderror
            <p class="form-help">Koordinat latitude lokasi kantor (otomatis terisi dari peta)</p>
        </div>
        
        <div class="form-group">
            <label for="longitude" class="form-label">Longitude</label>
            <input type="number" 
                   id="longitude" 
                   name="longitude" 
                   step="0.00000001"
                   value="{{ old('longitude', $settings->longitude) }}"
                   placeholder="Contoh: 106.8456"
                   class="form-input"
                   readonly>
            @error('longitude')
                <p class="error-message">{{ $message }}</p>
            @enderror
            <p class="form-help">Koordinat longitude lokasi kantor (otomatis terisi dari peta)</p>
        </div>
        
        <div class="form-group">
            <label for="radius" class="form-label">Radius (meter)</label>
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="range" 
                       id="radius-slider" 
                       min="10"
                       max="10000"
                       step="10"
                       value="{{ old('radius', $settings->radius ?? 100) }}"
                       style="flex: 1; height: 8px; border-radius: 4px; background: #e5e7eb; outline: none; cursor: pointer;">
                <input type="number" 
                       id="radius" 
                       name="radius" 
                       min="10"
                       max="10000"
                       value="{{ old('radius', $settings->radius ?? 100) }}"
                       placeholder="100"
                       class="form-input"
                       style="width: 120px; flex-shrink: 0;">
            </div>
            @error('radius')
                <p class="error-message">{{ $message }}</p>
            @enderror
            <p class="form-help">Geser slider atau ketik langsung untuk mengatur jarak maksimal dari lokasi kantor (dalam meter)</p>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i>
                <span>Simpan Pengaturan</span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map, marker, circle;
    const defaultLat = {{ $settings->latitude ?? -6.2088 }};
    const defaultLng = {{ $settings->longitude ?? 106.8456 }};
    const radius = {{ $settings->radius ?? 100 }};

    // Initialize map
    map = L.map('map').setView([defaultLat, defaultLng], 15);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Add marker if location exists (make it draggable)
    if (defaultLat && defaultLng) {
        marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);
        marker.bindPopup('Lokasi Kantor<br><small>Drag untuk memindahkan</small>').openPopup();
        
        // Add circle for radius
        if (radius) {
            circle = L.circle([defaultLat, defaultLng], {
                radius: radius,
                color: '#667eea',
                fillColor: '#667eea',
                fillOpacity: 0.2,
                weight: 2
            }).addTo(map);
        }
        
        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            const lat = position.lat;
            const lng = position.lng;
            
            // Update input fields
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
            
            // Update circle position
            if (circle) {
                const currentRadius = document.getElementById('radius').value || 100;
                circle.setLatLng([lat, lng]);
                circle.setRadius(parseInt(currentRadius));
            }
            
            // Update popup
            marker.setPopupContent('Lokasi Kantor<br><small>Drag untuk memindahkan</small>').openPopup();
        });
    } else {
        // Create a default draggable marker if no location exists
        marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);
        marker.bindPopup('Lokasi Kantor<br><small>Drag untuk memindahkan</small>');
        
        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            const lat = position.lat;
            const lng = position.lng;
            
            // Update input fields
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
            
            // Update circle position
            if (circle) {
                const currentRadius = document.getElementById('radius').value || 100;
                circle.setLatLng([lat, lng]);
                circle.setRadius(parseInt(currentRadius));
            } else {
                const currentRadius = document.getElementById('radius').value || 100;
                circle = L.circle([lat, lng], {
                    radius: parseInt(currentRadius),
                    color: '#667eea',
                    fillColor: '#667eea',
                    fillOpacity: 0.2,
                    weight: 2
                }).addTo(map);
            }
            
            // Update popup
            marker.setPopupContent('Lokasi Kantor<br><small>Drag untuk memindahkan</small>').openPopup();
        });
    }

    // Handle map click
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Update input fields
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        
        // Update or create marker (make it draggable)
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            
            // Add drag event listener for new marker
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                const newLat = position.lat;
                const newLng = position.lng;
                
                document.getElementById('latitude').value = newLat.toFixed(8);
                document.getElementById('longitude').value = newLng.toFixed(8);
                
                if (circle) {
                    const currentRadius = document.getElementById('radius').value || 100;
                    circle.setLatLng([newLat, newLng]);
                    circle.setRadius(parseInt(currentRadius));
                }
            });
        }
        marker.bindPopup('Lokasi Kantor<br><small>Drag untuk memindahkan</small>').openPopup();
        
        // Update circle
        const currentRadius = document.getElementById('radius').value || 100;
        if (circle) {
            circle.setLatLng([lat, lng]);
            circle.setRadius(parseInt(currentRadius));
        } else {
            circle = L.circle([lat, lng], {
                radius: parseInt(currentRadius),
                color: '#667eea',
                fillColor: '#667eea',
                fillOpacity: 0.2,
                weight: 2
            }).addTo(map);
        }
    });

    // Sync radius slider and input
    const radiusSlider = document.getElementById('radius-slider');
    const radiusInput = document.getElementById('radius');
    
    // Update circle when radius slider changes
    radiusSlider.addEventListener('input', function() {
        const newRadius = parseInt(this.value);
        radiusInput.value = newRadius;
        updateCircleRadius(newRadius);
    });
    
    // Update circle when radius input changes
    radiusInput.addEventListener('input', function() {
        let newRadius = parseInt(this.value) || 100;
        if (newRadius < 10) newRadius = 10;
        if (newRadius > 10000) newRadius = 10000;
        this.value = newRadius;
        radiusSlider.value = newRadius;
        updateCircleRadius(newRadius);
    });
    
    // Function to update circle radius
    function updateCircleRadius(newRadius) {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (lat && lng) {
            if (circle) {
                circle.setRadius(newRadius);
            } else if (marker) {
                const markerPos = marker.getLatLng();
                circle = L.circle([markerPos.lat, markerPos.lng], {
                    radius: newRadius,
                    color: '#667eea',
                    fillColor: '#667eea',
                    fillOpacity: 0.2,
                    weight: 2
                }).addTo(map);
            }
        }
    }

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Update input fields
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                
                // Update map
                map.setView([lat, lng], 17);
                
                // Update or create marker (make it draggable)
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);
                    
                    // Add drag event listener for new marker
                    marker.on('dragend', function(e) {
                        const position = marker.getLatLng();
                        const newLat = position.lat;
                        const newLng = position.lng;
                        
                        document.getElementById('latitude').value = newLat.toFixed(8);
                        document.getElementById('longitude').value = newLng.toFixed(8);
                        
                        if (circle) {
                            const currentRadius = document.getElementById('radius').value || 100;
                            circle.setLatLng([newLat, newLng]);
                            circle.setRadius(parseInt(currentRadius));
                        }
                    });
                }
                marker.bindPopup('Lokasi Kantor<br><small>Drag untuk memindahkan</small>').openPopup();
                
                // Update circle
                const currentRadius = document.getElementById('radius').value || 100;
                if (circle) {
                    circle.setLatLng([lat, lng]);
                    circle.setRadius(parseInt(currentRadius));
                } else {
                    circle = L.circle([lat, lng], {
                        radius: parseInt(currentRadius),
                        color: '#667eea',
                        fillColor: '#667eea',
                        fillOpacity: 0.2,
                        weight: 2
                    }).addTo(map);
                }
            }, function(error) {
                alert('Tidak dapat mendapatkan lokasi saat ini. Pastikan izin lokasi sudah diberikan.');
            });
        } else {
            alert('Browser Anda tidak mendukung geolocation.');
        }
    }
</script>
@endsection

