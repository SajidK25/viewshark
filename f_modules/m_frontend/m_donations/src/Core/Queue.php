<?php
namespace Donations\Core;

class Queue {
    private static $instance = null;
    private $config;
    private $connection;
    private $channel;
    private $queue;

    private function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $this->connection = new \AMQPConnection([
                'host' => $this->config['queue']['host'],
                'port' => $this->config['queue']['port'],
                'username' => $this->config['queue']['user'],
                'password' => $this->config['queue']['pass'],
                'vhost' => $this->config['queue']['vhost']
            ]);

            $this->channel = $this->connection->channel();
            $this->queue = $this->config['queue']['name'];
            
            $this->channel->queue_declare($this->queue, false, true, false, false);
        } catch (\Exception $e) {
            throw new \Exception("Queue connection failed: " . $e->getMessage());
        }
    }

    public function push($job, $data = [], $delay = 0) {
        $message = [
            'job' => $job,
            'data' => $data,
            'attempts' => 0,
            'created_at' => time()
        ];

        $msg = new \AMQPMessage(
            json_encode($message),
            ['delivery_mode' => \AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        if ($delay > 0) {
            $this->channel->basic_publish($msg, '', $this->queue . '_delayed');
        } else {
            $this->channel->basic_publish($msg, '', $this->queue);
        }

        return true;
    }

    public function later($delay, $job, $data = []) {
        return $this->push($job, $data, $delay);
    }

    public function pop() {
        $msg = $this->channel->basic_get($this->queue);
        
        if ($msg) {
            $message = json_decode($msg->getBody(), true);
            $message['attempts']++;
            
            $this->channel->basic_ack($msg->getDeliveryTag());
            return $message;
        }
        
        return null;
    }

    public function process() {
        $this->channel->basic_qos(null, 1, null);
        
        $callback = function($msg) {
            try {
                $message = json_decode($msg->getBody(), true);
                $job = $message['job'];
                $data = $message['data'];
                
                $class = "Donations\\Jobs\\{$job}";
                $instance = new $class();
                $instance->handle($data);
                
                $this->channel->basic_ack($msg->getDeliveryTag());
            } catch (\Exception $e) {
                $this->handleFailedJob($msg, $e);
            }
        };
        
        $this->channel->basic_consume($this->queue, '', false, false, false, false, $callback);
        
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    protected function handleFailedJob($msg, $exception) {
        $message = json_decode($msg->getBody(), true);
        $message['attempts']++;
        
        if ($message['attempts'] >= $this->config['queue']['max_attempts']) {
            $this->channel->basic_nack($msg->getDeliveryTag(), false, false);
            $this->logFailedJob($message, $exception);
        } else {
            $this->channel->basic_nack($msg->getDeliveryTag(), false, true);
        }
    }

    protected function logFailedJob($message, $exception) {
        $sql = "INSERT INTO failed_jobs (job, data, error, failed_at) VALUES (?, ?, ?, NOW())";
        $this->db->execute($sql, [
            $message['job'],
            json_encode($message['data']),
            $exception->getMessage()
        ]);
    }

    public function size() {
        $queueInfo = $this->channel->queue_declare($this->queue, false, true, false, false);
        return $queueInfo[1];
    }

    public function flush() {
        $this->channel->queue_purge($this->queue);
    }

    public function retry($id) {
        $sql = "SELECT * FROM failed_jobs WHERE id = ?";
        $job = $this->db->fetch($sql, [$id]);
        
        if ($job) {
            $this->push($job['job'], json_decode($job['data'], true));
            $this->db->execute("DELETE FROM failed_jobs WHERE id = ?", [$id]);
            return true;
        }
        
        return false;
    }

    public function forget($id) {
        return $this->db->execute("DELETE FROM failed_jobs WHERE id = ?", [$id]);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __destruct() {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }

    private function __clone() {}
    private function __wakeup() {}
} 