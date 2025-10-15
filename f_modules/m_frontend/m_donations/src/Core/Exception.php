<?php
namespace Donations\Core;

class Exception extends \Exception {
    protected $data;

    public function __construct($message = "", $code = 0, $data = null) {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function toArray() {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'data' => $this->getData()
        ];
    }

    public function toJson() {
        return json_encode($this->toArray());
    }
}

class ValidationException extends Exception {
    public function __construct($errors) {
        parent::__construct('Validation failed', 422, $errors);
    }
}

class AuthenticationException extends Exception {
    public function __construct($message = 'Authentication required') {
        parent::__construct($message, 401);
    }
}

class AuthorizationException extends Exception {
    public function __construct($message = 'Unauthorized access') {
        parent::__construct($message, 403);
    }
}

class NotFoundException extends Exception {
    public function __construct($message = 'Resource not found') {
        parent::__construct($message, 404);
    }
}

class RateLimitException extends Exception {
    public function __construct($message = 'Rate limit exceeded') {
        parent::__construct($message, 429);
    }
}

class PaymentException extends Exception {
    public function __construct($message, $data = null) {
        parent::__construct($message, 402, $data);
    }
}

class DatabaseException extends Exception {
    public function __construct($message = 'Database error occurred') {
        parent::__construct($message, 500);
    }
}

class ConfigurationException extends Exception {
    public function __construct($message = 'Configuration error occurred') {
        parent::__construct($message, 500);
    }
}

class WebhookException extends Exception {
    public function __construct($message = 'Webhook error occurred') {
        parent::__construct($message, 500);
    }
} 