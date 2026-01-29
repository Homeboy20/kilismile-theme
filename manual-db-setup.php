<?php
/**
 * Manual Database Table Creation Script
 * Use this to create the AzamPay tables manually
 */

echo "🗃️ AzamPay Database Table Creation\n";
echo "==================================\n\n";

// Direct MySQL connection (adjust these values for your Local by Flywheel setup)
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'local';  // Default Local by Flywheel database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database: $database\n\n";
    
    // Table prefix (usually 'wp_' for WordPress)
    $prefix = 'wp_';
    
    // Create transactions table
    $transactions_sql = "CREATE TABLE IF NOT EXISTS {$prefix}azampay_transactions (
        id int(11) NOT NULL AUTO_INCREMENT,
        reference varchar(100) NOT NULL UNIQUE,
        amount decimal(10,2) NOT NULL,
        currency varchar(3) NOT NULL DEFAULT 'TZS',
        donor_name varchar(255) NOT NULL,
        donor_email varchar(255) NOT NULL,
        phone varchar(20) NOT NULL,
        purpose varchar(255) DEFAULT 'healthcare',
        payment_method varchar(50) DEFAULT 'stkpush',
        anonymous tinyint(1) DEFAULT 0,
        notes text,
        status varchar(20) DEFAULT 'pending',
        gateway_transaction_id varchar(255),
        gateway_response longtext,
        callback_data longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY reference (reference),
        KEY status (status),
        KEY created_at (created_at),
        KEY donor_email (donor_email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    // Create logs table
    $logs_sql = "CREATE TABLE IF NOT EXISTS {$prefix}azampay_logs (
        id int(11) NOT NULL AUTO_INCREMENT,
        level varchar(20) NOT NULL DEFAULT 'info',
        message text NOT NULL,
        context longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY level (level),
        KEY created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    // Execute table creation
    echo "Creating transactions table...\n";
    $pdo->exec($transactions_sql);
    echo "✅ {$prefix}azampay_transactions table created\n";
    
    echo "Creating logs table...\n";
    $pdo->exec($logs_sql);
    echo "✅ {$prefix}azampay_logs table created\n";
    
    // Verify tables exist
    echo "\nVerifying table creation:\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}azampay_%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "✅ Table exists: $table\n";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "   Columns: " . count($columns) . "\n";
    }
    
    echo "\n🎉 Database tables created successfully!\n";
    echo "You can now use the AzamPay plugin admin interface.\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nTry updating the connection details at the top of this script:\n";
    echo "- Host: $host\n";
    echo "- Username: $username\n";
    echo "- Password: $password\n";
    echo "- Database: $database\n";
}
?>

