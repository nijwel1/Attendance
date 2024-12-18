<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

if ( !function_exists( 'employee_addon' ) ) {
    function attendance_addon() {

        return DB::table( 'addon_settings' )
            ->where( 'name', 'attendance' )
            ->value( 'enabled' );
    }
}

if ( !function_exists( 'yearsBefore' ) ) {
    function yearsBefore( $n ) {
        $currentYear = date( "Y" );
        $years       = [];

        // Years before the current year
        for ( $i = 0; $i < $n; $i++ ) {
            $years[] = $currentYear - $i;
        }
        return $years;
    }

}

if ( !function_exists( 'yearsAfter' ) ) {
    function yearsAfter( $n ) {
        $currentYear = date( "Y" );
        $years       = [];

        // Years before the current year
        for ( $i = 0; $i < $n; $i++ ) {
            $years[] = $currentYear + $i;
        }
        return $years;
    }

}

if ( !function_exists( 'employeePresent' ) ) {
    function employeePresent() {

        if ( !function_exists( 'authEmployeeId' ) ) {
            return false;
        }

        $employeeId = authEmployeeId();

        if ( !$employeeId ) {
            return false;
        }

        $present = DB::table( 'employee_attendances' )
            ->where( 'employee_id', $employeeId )
            ->where( 'date', date( 'Y-m-d' ) )
            ->exists();

        return $present;
    }
}

if ( !function_exists( 'attendanceAddonVersion' ) ) {
    function attendanceAddonVersion() {

        // Get current version from version.json
        $versionPath = base_path( 'Addons/Attendance/version.json' );

        // Safely read the current version
        $currentVersion = json_decode( File::get( $versionPath ), true )['version'] ?? '0.0.0';

        // Prepare to check GitHub for the latest release version
        $token  = env( 'GITHUB_TOKEN_NIJWEL1' ); // Use an environment variable for the token
        $client = new Client();

        try {
            $response = $client->get( 'https://api.github.com/repos/nijwel1/Attendance/releases/latest', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept'        => 'application/vnd.github.v3+json',
                ],
            ] );

            // dd( json_decode( $response->getBody(), true ) );
            // Check if the request was successful
            if ( $response->getStatusCode() === 200 ) {
                // Decode the response
                $latestRelease = json_decode( $response->getBody(), true );

                // Extract the latest version and download URL
                $latestVersion = $latestRelease['tag_name'] ?? '';

                // Compare versions and update if needed
                if ( version_compare( $latestVersion, $currentVersion, '>' ) ) {
                    return "
                        <div style='padding: 15px; border: 1px solid #f0ad4e; background-color: #fcf8e3; border-radius: 5px; margin-bottom: 15px; font-size: 12px;'>
                            <h5 style='color: #f0ad4e;'>Update Available!</h5>
                            A new version <strong>'{$latestVersion}'</strong> of the Attendance Addon is available.
                            Install it from <a href='" . route( 'attendance.addon.update' ) . "' style='color: #d9534f; text-decoration: underline;'>here</a>.
                        </div>
                    ";
                }
            } else {
                Log::warning( 'Failed to retrieve the latest release: ' . $response->getReasonPhrase() );
            }
        } catch ( \Exception $e ) {
            // Handle exceptions (e.g., network issues, API errors)
            Log::error( 'Error checking for updates: ' . $e->getMessage() );
        }
    }
}
