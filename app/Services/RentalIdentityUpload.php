<?php

namespace App\Services;

use App\Models\RentalIdentity;
use App\Models\RentalReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RentalIdentityUpload
{
    public function prepare(Request $request): ?array
    {
        if ($request->input('mode') !== 'daily') {
            return null;
        }
        $data = $request->validate([
            'document_type' => ['required', Rule::in(['ktp', 'sim', 'student', 'other'])],
            'identity_photo' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:2048|dimensions:min_width=200,min_height=100,max_width=4096,max_height=4096',
            'identity_consent' => 'accepted', 'bring_guarantee' => 'accepted',
        ], [
            'identity_photo.required' => __('Unggah foto identitas untuk sewa bawa pulang.'),
            'identity_consent.accepted' => __('Setujui penggunaan foto untuk pemeriksaan sewa.'),
            'bring_guarantee.accepted' => __('Konfirmasi bahwa kamu akan membawa identitas asli dan jaminan ke tempat.'),
        ]);
        $file = $request->file('identity_photo');
        [$width,$height] = getimagesize($file->getRealPath());
        if ($width * $height > 12000000) {
            throw ValidationException::withMessages(['identity_photo' => __('Ukuran foto terlalu besar. Gunakan foto yang lebih kecil.')]);
        }
        $source = imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $source) {
            throw ValidationException::withMessages(['identity_photo' => __('Foto tidak dapat dibaca. Gunakan JPG, PNG, atau WebP.')]);
        }
        $ratio = min(1, 1600 / max($width, $height));
        $image = imagecreatetruecolor(max(1, (int) round($width * $ratio)), max(1, (int) round($height * $ratio)));
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        imagecopyresampled($image, $source, 0, 0, 0, 0, imagesx($image), imagesy($image), $width, $height);
        ob_start();
        imagejpeg($image, null, 85);
        $photo = ob_get_clean();
        imagedestroy($source);
        imagedestroy($image);

        // Re-encoding removes embedded metadata. Only ciphertext goes into persistent storage.
        return ['document_type' => $data['document_type'], 'encrypted_photo' => Crypt::encryptString(base64_encode($photo))];
    }

    public function attach(RentalReservation $reservation, ?array $data): void
    {
        if (! $data || $reservation->identity()->exists()) {
            return;
        }
        $reservation->update(['requires_identity_check' => true]);
        $reservation->identity()->create($data + ['expires_at' => $reservation->ends_at->addDays(3)]);
        RentalIdentity::purgeExpired();
    }
}
