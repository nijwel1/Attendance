<?php

namespace Addons\Attendance\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class AttendanceAddonUpdateController extends Controller {

    public function downloadAndUpdate() {
        // Get current version from version.json
        $versionPath = base_path( 'Addons/Attendance/version.json' );

        // Safely read the current version
        $currentVersion = json_decode( File::get( $versionPath ), true )['version'] ?? '0.0.0';
        $client         = new Client();
        $response       = $client->get( 'https://api.github.com/repos/git-futurein/HRM-Emplyee-Addon/releases/latest', [
            'verify' => false,
        ] );

        // Check if the response is successful
        if ( $response->getStatusCode() === 200 ) {
            // Decode the JSON response
            $data = json_decode( $response->getBody(), true );

            // Get the version and zipball_url from the response
            $latestVersion = $data['tag_name'] ?? '';
            $zipballUrl    = $data['zipball_url'] ?? '';

            // Compare versions and update if needed
            if ( version_compare( $latestVersion, $currentVersion, '>' ) ) {
                // Make a request to download the ZIP file
                $zipResponse = Http::withHeaders( ['git-futurein' => 'HRM-Emplyee-Addon'] )
                    ->get( $zipballUrl, ['access_token' => env( 'GITHUB_TOKEN' )] );

                if ( $latestVersion ) {
                    $zipContent  = $zipResponse->body();
                    $zipFileName = "{$data['name']}.zip"; // or use $data['tag_name'] for versioned name
                    $filePath    = public_path( $zipFileName );

                    // Save the ZIP file to the public directory
                    file_put_contents( $filePath, $zipContent );

                    $zip = new ZipArchive;

                    if ( $zip->open( $filePath ) === TRUE ) {
                        // Extract the zip file to a temporary location
                        $extractPath = storage_path( 'app/temp' );
                        $zip->extractTo( $extractPath );
                        $zip->close();

                        // Move files to the appropriate directories
                        $this->moveFiles( $extractPath );

                        // Clean up the temporary directory
                        $this->deleteDirectory( $extractPath );

                        $this->runAddonMigrations();
                        Artisan::call( 'config:cache' );

                        return back()->with( 'success', 'Files imported successfully.' );
                    } else {
                        return back()->with( 'error', 'Failed to open zip file.' );
                    }
                }

                return response()->json( ['error' => 'Unable to download ZIP file'], 500 );
            }

            return response()->json( ['message' => 'No update needed, current version is up to date.'] );
        }

        // Handle error response
        return response()->json( ['error' => 'Unable to fetch release data'], 500 );
    }

    private function moveFiles( $extractPath ) {
        // Assuming the dynamic folder name is the only folder in the extract path
        $dynamicFolder = glob( $extractPath . '/*', GLOB_ONLYDIR );

        // If a dynamic folder is found, get its path
        if ( !empty( $dynamicFolder ) ) {
            $dynamicFolderPath = $dynamicFolder[0];

            // File mappings for known folders
            $fileMappings = [
                'controllers' => base_path( 'Addons/Attendance/Controllers' ),
                'helpers'     => base_path( 'Addons/Attendance/Helpers' ),
                'models'      => base_path( 'Addons/Attendance/Models' ),
                'routes'      => base_path( 'Addons/Attendance/routes' ),
                'views'       => base_path( 'Addons/Attendance/resources/views' ),
                'vendor'      => base_path( 'Addons/Attendance/vendor' ),
                'database'    => base_path( 'Addons/Attendance/database' ),
            ];

            // Move files from known subfolders
            foreach ( $fileMappings as $folder => $destination ) {
                $sourcePath = $dynamicFolderPath . '/' . $folder; // Use dynamic folder path
                if ( is_dir( $sourcePath ) ) {
                    $this->copyFiles( $sourcePath, $destination );
                }
            }

            // Move AttendanceServiceProvider.php
            $providerSource = $dynamicFolderPath . '/AttendanceServiceProvider.php';
            if ( file_exists( $providerSource ) ) {
                $providerDestination = base_path( 'Addons/Attendance/AttendanceServiceProvider.php' );
                copy( $providerSource, $providerDestination );
            }

            // Move version.json
            $versionSource = $dynamicFolderPath . '/version.json';
            if ( file_exists( $versionSource ) ) {
                $versionDestination = base_path( 'Addons/Attendance/version.json' );
                copy( $versionSource, $versionDestination );
            }
        }
    }

    private function copyFiles( $sourcePath, $destination ) {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $files as $file ) {
            $relativePath    = $files->getSubPathName();
            $destinationPath = $destination . '/' . $relativePath;

            if ( $file->isDir() ) {
                if ( !file_exists( $destinationPath ) ) {
                    mkdir( $destinationPath, 0755, true );
                }
            } else if ( $file->isFile() ) {
                if ( !file_exists( dirname( $destinationPath ) ) ) {
                    mkdir( dirname( $destinationPath ), 0755, true );
                }
                copy( $file->getRealPath(), $destinationPath );
            }
        }
    }

    private function deleteDirectory( $dir ) {
        if ( !file_exists( $dir ) ) {
            return true;
        }

        if ( !is_dir( $dir ) ) {
            return unlink( $dir );
        }

        foreach ( scandir( $dir ) as $item ) {
            if ( $item == '.' || $item == '..' ) {
                continue;
            }

            if ( !$this->deleteDirectory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
                return false;
            }
        }

        return rmdir( $dir );
    }

    private function runAddonMigrations() {
        $migrationPath = base_path( 'Addons/Attendance/migrations' );
        if ( File::exists( $migrationPath ) ) {
            Artisan::call( 'migrate', ['--path' => $migrationPath, '--force' => true] );
        }
    }
}
