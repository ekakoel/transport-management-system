<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                email_verified_at TIMESTAMP NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(30) NOT NULL DEFAULT 'reservation',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_users_role_active (role, is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS password_reset_tokens (
                email VARCHAR(190) PRIMARY KEY,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(255) PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,
                INDEX sessions_user_id_index (user_id),
                INDEX sessions_last_activity_index (last_activity)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS drivers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                phone VARCHAR(40) NULL,
                license_number VARCHAR(80) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                notes TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_drivers_active_name (is_active, name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS vehicles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                plate_number VARCHAR(30) NOT NULL UNIQUE,
                capacity SMALLINT UNSIGNED NOT NULL DEFAULT 4,
                status VARCHAR(30) NOT NULL DEFAULT 'Available',
                notes TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_vehicles_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS service_types (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL UNIQUE,
                description VARCHAR(500) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS emergency_contacts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                phone VARCHAR(40) NOT NULL DEFAULT '',
                whatsapp VARCHAR(40) NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_contacts_default (is_default, is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS transport_services (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                service_code VARCHAR(40) NOT NULL UNIQUE,
                customer_name VARCHAR(150) NOT NULL,
                customer_phone VARCHAR(40) NULL,
                guest_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
                service_type_id BIGINT UNSIGNED NOT NULL,
                service_date DATE NOT NULL,
                service_end_date DATE NOT NULL,
                start_time TIME NULL,
                end_time TIME NULL,
                pickup_location VARCHAR(255) NOT NULL,
                notes TEXT NULL,
                driver_id BIGINT UNSIGNED NOT NULL,
                vehicle_id BIGINT UNSIGNED NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'Draft',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_service_dates (service_date, service_end_date),
                INDEX idx_service_status (status),
                INDEX idx_service_driver_dates (driver_id, service_date, service_end_date),
                INDEX idx_service_vehicle_dates (vehicle_id, service_date, service_end_date),
                CONSTRAINT fk_services_type
                    FOREIGN KEY (service_type_id) REFERENCES service_types(id),
                CONSTRAINT fk_services_driver
                    FOREIGN KEY (driver_id) REFERENCES drivers(id),
                CONSTRAINT fk_services_vehicle
                    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
                CONSTRAINT fk_services_creator
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS service_itinerary_stops (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transport_service_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(150) NOT NULL,
                address VARCHAR(500) NULL,
                latitude DECIMAL(10,7) NULL,
                longitude DECIMAL(10,7) NULL,
                sort_order INT NOT NULL DEFAULT 0,
                status VARCHAR(30) NOT NULL DEFAULT 'Planned',
                added_by_driver TINYINT(1) NOT NULL DEFAULT 0,
                note VARCHAR(1000) NULL,
                cancelled_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_stops_service_order (transport_service_id, sort_order),
                CONSTRAINT fk_stops_service
                    FOREIGN KEY (transport_service_id)
                    REFERENCES transport_services(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS spks (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transport_service_id BIGINT UNSIGNED NOT NULL UNIQUE,
                spk_number VARCHAR(50) NOT NULL UNIQUE,
                token_hash CHAR(64) NOT NULL UNIQUE,
                published_at TIMESTAMP NULL,
                revoked_at TIMESTAMP NULL,
                emergency_name VARCHAR(150) NULL,
                emergency_phone VARCHAR(40) NULL,
                emergency_whatsapp VARCHAR(40) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_spks_revoked (revoked_at),
                CONSTRAINT fk_spks_service
                    FOREIGN KEY (transport_service_id)
                    REFERENCES transport_services(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS spk_events (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                spk_id BIGINT UNSIGNED NOT NULL,
                transport_service_id BIGINT UNSIGNED NOT NULL,
                itinerary_stop_id BIGINT UNSIGNED NULL,
                event_type VARCHAR(40) NOT NULL,
                latitude DECIMAL(10,7) NULL,
                longitude DECIMAL(10,7) NULL,
                accuracy_meters DECIMAL(10,2) NULL,
                note VARCHAR(1000) NULL,
                actor_type VARCHAR(40) NOT NULL DEFAULT 'driver_public',
                actor_id BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                metadata JSON NULL,
                INDEX idx_events_spk_time (spk_id, created_at),
                INDEX idx_events_service_time (transport_service_id, created_at),
                INDEX idx_events_type (event_type),
                INDEX idx_events_stop (itinerary_stop_id),
                CONSTRAINT fk_events_spk
                    FOREIGN KEY (spk_id) REFERENCES spks(id) ON DELETE CASCADE,
                CONSTRAINT fk_events_service
                    FOREIGN KEY (transport_service_id)
                    REFERENCES transport_services(id) ON DELETE CASCADE,
                CONSTRAINT fk_events_stop
                    FOREIGN KEY (itinerary_stop_id)
                    REFERENCES service_itinerary_stops(id) ON DELETE SET NULL,
                CONSTRAINT fk_events_actor
                    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS vehicle_maintenance (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                vehicle_id BIGINT UNSIGNED NOT NULL,
                maintenance_date DATE NOT NULL,
                maintenance_type VARCHAR(120) NOT NULL,
                cost DECIMAL(12,2) NULL,
                odometer_km INT UNSIGNED NULL,
                notes TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_maintenance_vehicle_date (vehicle_id, maintenance_date),
                CONSTRAINT fk_maintenance_vehicle
                    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS vehicle_usage_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                vehicle_id BIGINT UNSIGNED NOT NULL,
                transport_service_id BIGINT UNSIGNED NOT NULL,
                started_at DATETIME NULL,
                ended_at DATETIME NULL,
                start_odometer_km INT UNSIGNED NULL,
                end_odometer_km INT UNSIGNED NULL,
                notes TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_usage_vehicle (vehicle_id, started_at),
                CONSTRAINT fk_usage_vehicle
                    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
                CONSTRAINT fk_usage_service
                    FOREIGN KEY (transport_service_id)
                    REFERENCES transport_services(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                action VARCHAR(100) NOT NULL,
                subject_type VARCHAR(100) NULL,
                subject_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(500) NULL,
                details JSON NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_audit_user_time (user_id, created_at),
                INDEX idx_audit_subject (subject_type, subject_id),
                CONSTRAINT fk_audit_user
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `cache` (
                `key` VARCHAR(255) PRIMARY KEY,
                `value` MEDIUMTEXT NOT NULL,
                `expiration` INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS cache_locks (
                `key` VARCHAR(255) PRIMARY KEY,
                owner VARCHAR(255) NOT NULL,
                expiration INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(255) NOT NULL,
                payload LONGTEXT NOT NULL,
                attempts TINYINT UNSIGNED NOT NULL,
                reserved_at INT UNSIGNED NULL,
                available_at INT UNSIGNED NOT NULL,
                created_at INT UNSIGNED NOT NULL,
                INDEX jobs_queue_index (queue)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_events');
        Schema::dropIfExists('vehicle_usage_logs');
        Schema::dropIfExists('vehicle_maintenance');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('spks');
        Schema::dropIfExists('service_itinerary_stops');
        Schema::dropIfExists('transport_services');
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};