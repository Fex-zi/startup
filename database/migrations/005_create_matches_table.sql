CREATE TABLE IF NOT EXISTS matches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    startup_id INT UNSIGNED NOT NULL,
    investor_id INT UNSIGNED NOT NULL,
    match_score INT NOT NULL,
    match_reasons JSON,
    startup_interested BOOLEAN DEFAULT NULL,
    investor_interested BOOLEAN DEFAULT NULL,
    status ENUM('pending', 'mutual_interest', 'startup_declined', 'investor_declined', 'expired') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (startup_id) REFERENCES startups(id) ON DELETE CASCADE,
    FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_match (startup_id, investor_id),
    INDEX idx_startup (startup_id),
    INDEX idx_investor (investor_id),
    INDEX idx_status (status),
    INDEX idx_score (match_score)
);
