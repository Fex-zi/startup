<?php

namespace Models;

use Core\Security;

class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = [
        'email', 'password_hash', 'user_type', 'first_name', 'last_name', 
        'phone', 'location', 'email_verification_token', 'profile_completed'
    ];
    protected $hidden = ['password_hash', 'email_verification_token'];

    public function createUser($data)
    {
        $security = Security::getInstance();
        
        // Hash password
        if (isset($data['password'])) {
            $data['password_hash'] = $security->hashPassword($data['password']);
            unset($data['password']);
        }
        
        // Generate email verification token
        $data['email_verification_token'] = $security->generateRandomString();
        
        return $this->create($data);
    }

    public function verifyPassword($email, $password)
    {
        $user = $this->findBy('email', $email);
        
        if (!$user) {
            return false;
        }

        $security = Security::getInstance();
        return $security->verifyPassword($password, $user['password_hash']);
    }

    public function authenticate($email, $password)
    {
        $user = $this->findBy('email', $email);
        
        if (!$user || !$user['is_active']) {
            return false;
        }

        $security = Security::getInstance();
        
        // Check brute force protection
        if (!$security->preventBruteForce($email)) {
            return false;
        }

        if ($security->verifyPassword($password, $user['password_hash'])) {
            // Clear failed attempts on successful login
            $security->clearFailedAttempts($email);
            
            // Hide sensitive fields
            return $this->hideFields($user);
        } else {
            // Record failed attempt
            $security->recordFailedAttempt($email);
            return false;
        }
    }

    public function verifyEmail($token)
    {
        $user = $this->findBy('email_verification_token', $token);
        
        if (!$user) {
            return false;
        }

        $this->update($user['id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email_verification_token' => null
        ]);

        return true;
    }

    public function updatePassword($userId, $newPassword)
    {
        $security = Security::getInstance();
        $hashedPassword = $security->hashPassword($newPassword);
        
        return $this->update($userId, ['password_hash' => $hashedPassword]);
    }

    public function markProfileCompleted($userId)
    {
        return $this->update($userId, ['profile_completed' => true]);
    }

    public function getStartupProfile($userId)
    {
        $sql = "
            SELECT u.*, s.* 
            FROM users u 
            LEFT JOIN startups s ON u.id = s.user_id 
            WHERE u.id = ? AND u.user_type = 'startup'
        ";
        
        return $this->db->fetch($sql, [$userId]);
    }

    public function getInvestorProfile($userId)
    {
        $sql = "
            SELECT u.*, i.* 
            FROM users u 
            LEFT JOIN investors i ON u.id = i.user_id 
            WHERE u.id = ? AND u.user_type = 'investor'
        ";
        
        return $this->db->fetch($sql, [$userId]);
    }

    public function getUserWithProfile($userId)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return null;
        }

        if ($user['user_type'] === 'startup') {
            return $this->getStartupProfile($userId);
        } elseif ($user['user_type'] === 'investor') {
            return $this->getInvestorProfile($userId);
        }

        return $user;
    }

    public function isEmailTaken($email, $excludeUserId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeUserId) {
            $sql .= " AND id != ?";
            $params[] = $excludeUserId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    public function getActiveUsers($userType = null)
    {
        $conditions = ['is_active' => true];
        
        if ($userType) {
            $conditions['user_type'] = $userType;
        }
        
        return $this->where($conditions, null, null, 'created_at DESC');
    }
}
