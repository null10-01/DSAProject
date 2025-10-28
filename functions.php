<?php
require_once 'config.php';

/**
 * Priority Queue Implementation for Seat Allocation
 * This is my implementation of a max heap for the flight booking system
 * Learned about heaps in DSA class and wanted to apply it here
 * 
 * References:
 * - GeeksforGeeks heap tutorial
 * - My DSA textbook chapter 6
 */
class FlightBookingQueue {
    private $bookings = [];
    private $size = 0;
    
    /**
     * Insert a booking into the priority queue
     * Priority is based on user priority level and booking time
     * This took me a while to get right - heap insertion is tricky!
     */
    public function insert($booking) {
        $this->bookings[++$this->size] = $booking;
        $this->heapifyUp($this->size);
    }
    
    /**
     * Extract the highest priority booking
     * Returns null if queue is empty
     */
    public function extractMax() {
        if ($this->isEmpty()) return null;
        
        $max = $this->bookings[1];
        $this->bookings[1] = $this->bookings[$this->size];
        unset($this->bookings[$this->size]);
        $this->size--;
        
        if ($this->size > 0) {
            $this->heapifyDown(1);
        }
        
        return $max;
    }
    
    /**
     * Check if queue is empty
     */
    public function isEmpty() {
        return $this->size === 0;
    }
    
    /**
     * Get current queue size
     */
    public function getSize() {
        return $this->size;
    }
    
    /**
     * Heapify up operation to maintain max-heap property
     */
    private function heapifyUp($index) {
        while ($index > 1) {
            $parent = intval($index / 2);
            if ($this->compare($this->bookings[$index], $this->bookings[$parent]) <= 0) {
                break;
            }
            $this->swap($index, $parent);
            $index = $parent;
        }
    }
    
    /**
     * Heapify down operation to maintain max-heap property
     */
    private function heapifyDown($index) {
        while (true) {
            $left = 2 * $index;
            $right = 2 * $index + 1;
            $largest = $index;
            
            if ($left <= $this->size && $this->compare($this->bookings[$left], $this->bookings[$largest]) > 0) {
                $largest = $left;
            }
            
            if ($right <= $this->size && $this->compare($this->bookings[$right], $this->bookings[$largest]) > 0) {
                $largest = $right;
            }
            
            if ($largest === $index) break;
            
            $this->swap($index, $largest);
            $index = $largest;
        }
    }
    
    /**
     * Compare two bookings for priority
     * Returns positive if booking1 has higher priority
     * TODO: Maybe optimize this later
     */
    private function compare($booking1, $booking2) {
        // First compare by priority level (higher is better)
        if ($booking1['priority'] != $booking2['priority']) { // using != instead of !==
            return $booking1['priority'] - $booking2['priority'];
        }
        
        // If same priority, compare by booking time (earlier is better)
        // Note: strtotime might be slow for large datasets
        return strtotime($booking2['created_at']) - strtotime($booking1['created_at']);
    }
    
    /**
     * Swap two elements in the array
     */
    private function swap($i, $j) {
        $temp = $this->bookings[$i];
        $this->bookings[$i] = $this->bookings[$j];
        $this->bookings[$j] = $temp;
    }
}

/**
 * Enhanced seat allocation using custom priority queue
 * Demonstrates efficient algorithm design and data structure usage
 */
function allocate_seats($flight_id) {
    global $mysqli;

    try {
        // Fetch flight details with proper error handling
        $stmt = $mysqli->prepare("SELECT seats_total, seats_booked FROM flights WHERE id = ?");
        $stmt->bind_param('i', $flight_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result || $result->num_rows === 0) {
            error_log("Flight not found: $flight_id");
            return false;
        }
        
        $flight = $result->fetch_assoc();
        $available = (int)$flight['seats_total'] - (int)$flight['seats_booked'];

        if ($available <= 0) {
            return true; // No seats available
        }

        // Fetch WAITLISTED bookings using prepared statement
        $stmt = $mysqli->prepare("
            SELECT id, user_id, priority, created_at 
            FROM bookings 
            WHERE flight_id = ? AND status = 'WAITLISTED' 
            ORDER BY created_at ASC
        ");
        $stmt->bind_param('i', $flight_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Use custom priority queue for efficient seat allocation
        $bookingQueue = new FlightBookingQueue();
        
        while ($row = $result->fetch_assoc()) {
            $bookingQueue->insert($row);
        }

        $confirmedCount = 0;
        $confirmedBookings = [];

        // Allocate seats using priority queue
        while (!$bookingQueue->isEmpty() && $confirmedCount < $available) {
            $booking = $bookingQueue->extractMax();
            $confirmedBookings[] = $booking['id'];
            $confirmedCount++;
        }

        // Update confirmed bookings in a single transaction
        if ($confirmedCount > 0) {
            $mysqli->begin_transaction();
            
            try {
                // Update booking statuses
                $placeholders = str_repeat('?,', count($confirmedBookings) - 1) . '?';
                $stmt = $mysqli->prepare("UPDATE bookings SET status='CONFIRMED' WHERE id IN ($placeholders)");
                $stmt->bind_param(str_repeat('i', count($confirmedBookings)), ...$confirmedBookings);
                $stmt->execute();
                
                // Update flight seat count
                $stmt = $mysqli->prepare("UPDATE flights SET seats_booked = seats_booked + ? WHERE id = ?");
                $stmt->bind_param('ii', $confirmedCount, $flight_id);
                $stmt->execute();
                
                $mysqli->commit();
                
                // Log successful allocation
                error_log("Allocated $confirmedCount seats for flight $flight_id");
                
            } catch (Exception $e) {
                $mysqli->rollback();
                error_log("Error allocating seats: " . $e->getMessage());
                return false;
            }
        }

        return true;
        
    } catch (Exception $e) {
        error_log("Error in allocate_seats: " . $e->getMessage());
        return false;
    }
}

/**
 * Input validation function with comprehensive checks
 */
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[$field] = ucfirst($field) . ' is required';
            continue;
        }
        
        if (!empty($value)) {
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Invalid email format';
                        }
                        break;
                    case 'int':
                        if (!is_numeric($value) || (int)$value != $value) {
                            $errors[$field] = ucfirst($field) . ' must be a valid integer';
                        }
                        break;
                    case 'date':
                        if (!strtotime($value)) {
                            $errors[$field] = 'Invalid date format';
                        }
                        break;
                }
            }
            
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = ucfirst($field) . ' must be at least ' . $rule['min_length'] . ' characters';
            }
            
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = ucfirst($field) . ' must not exceed ' . $rule['max_length'] . ' characters';
            }
        }
    }
    
    return $errors;
}

/**
 * Generate CSRF token for form protection
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}