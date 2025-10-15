<?php
namespace Donations\Core;

class Event {
    private static $instance = null;
    private $listeners = [];
    private $queue;

    private function __construct() {
        $this->queue = Queue::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function listen($event, $listener) {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $listener;
    }

    public function fire($event, $data = []) {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            if (is_string($listener)) {
                $listener = new $listener();
            }

            if (method_exists($listener, 'handle')) {
                $listener->handle($data);
            }
        }
    }

    public function dispatch($event, $data = []) {
        $this->fire($event, $data);
    }

    public function dispatchAsync($event, $data = []) {
        $this->queue->push('EventJob', [
            'event' => $event,
            'data' => $data
        ]);
    }

    public function dispatchDelayed($delay, $event, $data = []) {
        $this->queue->later($delay, 'EventJob', [
            'event' => $event,
            'data' => $data
        ]);
    }

    public function forget($event) {
        unset($this->listeners[$event]);
    }

    public function flush() {
        $this->listeners = [];
    }

    public function hasListeners($event) {
        return isset($this->listeners[$event]) && !empty($this->listeners[$event]);
    }

    public function getListeners($event) {
        return $this->listeners[$event] ?? [];
    }

    public function getEvents() {
        return array_keys($this->listeners);
    }

    public function subscribe($subscriber) {
        if (method_exists($subscriber, 'subscribe')) {
            $subscriber->subscribe($this);
        }
    }

    public function unsubscribe($subscriber) {
        foreach ($this->listeners as $event => $listeners) {
            $this->listeners[$event] = array_filter($listeners, function($listener) use ($subscriber) {
                return $listener !== $subscriber;
            });
        }
    }
}

class EventJob {
    private $event;

    public function handle($data) {
        $this->event = Event::getInstance();
        $this->event->fire($data['event'], $data['data']);
    }
}

class EventSubscriber {
    public function subscribe($events) {
        // Override this method to subscribe to events
    }
}

class EventListener {
    public function handle($data) {
        // Override this method to handle events
    }
} 