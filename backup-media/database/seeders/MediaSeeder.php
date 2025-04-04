<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Debugbar;

class MediaSeeder extends Seeder
{
    protected array $images = [
        'logo' => 'https://picsum.photos/200/200', // Square logo placeholder
        'hero' => 'https://picsum.photos/1920/600', // Wide hero image placeholder
        'gala' => 'https://picsum.photos/800/600', // Event image placeholder
    ];

    public function run(): void
    {
        // Disable debugbar during seeding
        if (class_exists('Debugbar')) {
            Debugbar::disable();
        }

        // Ensure media directory exists
        if (!Storage::disk('public')->exists('media')) {
            Storage::disk('public')->makeDirectory('media');
        }

        foreach ($this->images as $name => $url) {
            try {
                echo "Downloading {$name} from {$url}\n";
                
                // Use streaming for large files
                $tempFile = tempnam(sys_get_temp_dir(), 'media_');
                $fp = fopen($tempFile, 'w+');
                
                $response = Http::withOptions([
                    'sink' => $fp,
                    'stream' => true
                ])->get($url);
                
                fclose($fp);
                
                if ($response->successful()) {
                    $mimeType = mime_content_type($tempFile);
                    $extension = $this->getExtensionFromMimeType($mimeType);
                    $fileName = 'media/' . Str::slug($name) . '-' . time() . '.' . $extension;
                    
                    echo "Storing file as {$fileName}\n";
                    
                    // Stream the file to storage
                    $stream = fopen($tempFile, 'r');
                    $success = Storage::disk('public')->writeStream($fileName, $stream);
                    fclose($stream);
                    
                    if (!$success) {
                        throw new \Exception("Failed to write file to storage");
                    }
                    
                    if (!Storage::disk('public')->exists($fileName)) {
                        throw new \Exception("File was not created in storage");
                    }
                    
                    echo "File size: " . filesize($tempFile) . " bytes\n";
                    
                    // Create media record
                    Media::create([
                        'name' => Str::title($name),
                        'file_name' => $fileName,
                        'mime_type' => $mimeType,
                        'size' => filesize($tempFile),
                        'collection' => 'website',
                        'disk' => 'public',
                    ]);
                    
                    echo "Created media record for {$name}\n";
                }
                
                // Clean up temp file
                @unlink($tempFile);
            } catch (\Exception $e) {
                // Log error but continue with other images
                echo "Error processing {$name}: " . $e->getMessage() . "\n";
                \Log::error("Failed to seed image {$name}: " . $e->getMessage());
            }
        }

        // Re-enable debugbar
        if (class_exists('Debugbar')) {
            Debugbar::enable();
        }
    }

    protected function getExtensionFromMimeType($mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
