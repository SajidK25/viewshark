<?php
namespace Donations\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
    private static $instance = null;
    private $config;
    private $mailer;
    private $queue;

    private function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->queue = Queue::getInstance();
        $this->setupMailer();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function setupMailer() {
        $this->mailer = new PHPMailer(true);

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['mail']['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['mail']['username'];
            $this->mailer->Password = $this->config['mail']['password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['mail']['port'];
            $this->mailer->CharSet = 'UTF-8';

            $this->mailer->setFrom(
                $this->config['mail']['from']['address'],
                $this->config['mail']['from']['name']
            );
        } catch (Exception $e) {
            throw new \Exception("Mail setup failed: " . $e->getMessage());
        }
    }

    public function to($address, $name = '') {
        $this->mailer->addAddress($address, $name);
        return $this;
    }

    public function cc($address, $name = '') {
        $this->mailer->addCC($address, $name);
        return $this;
    }

    public function bcc($address, $name = '') {
        $this->mailer->addBCC($address, $name);
        return $this;
    }

    public function subject($subject) {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function body($body) {
        $this->mailer->Body = $body;
        return $this;
    }

    public function html($html) {
        $this->mailer->isHTML(true);
        $this->mailer->Body = $html;
        return $this;
    }

    public function text($text) {
        $this->mailer->AltBody = $text;
        return $this;
    }

    public function attach($path, $name = '') {
        $this->mailer->addAttachment($path, $name);
        return $this;
    }

    public function embed($path, $cid) {
        $this->mailer->addEmbeddedImage($path, $cid);
        return $this;
    }

    public function send() {
        try {
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Mail send failed: " . $e->getMessage());
        }
    }

    public function queue($delay = 0) {
        $data = [
            'to' => $this->mailer->getAllRecipientAddresses(),
            'subject' => $this->mailer->Subject,
            'body' => $this->mailer->Body,
            'html' => $this->mailer->isHTML(),
            'text' => $this->mailer->AltBody,
            'attachments' => $this->getAttachments(),
            'embedded' => $this->getEmbedded()
        ];

        if ($delay > 0) {
            return $this->queue->later($delay, 'MailJob', $data);
        }

        return $this->queue->push('MailJob', $data);
    }

    protected function getAttachments() {
        $attachments = [];
        foreach ($this->mailer->getAttachments() as $attachment) {
            $attachments[] = [
                'path' => $attachment[0],
                'name' => $attachment[1]
            ];
        }
        return $attachments;
    }

    protected function getEmbedded() {
        $embedded = [];
        foreach ($this->mailer->getEmbeddedImages() as $image) {
            $embedded[] = [
                'path' => $image[0],
                'cid' => $image[1]
            ];
        }
        return $embedded;
    }

    public function reset() {
        $this->mailer->clearAllRecipients();
        $this->mailer->clearAttachments();
        $this->mailer->clearCustomHeaders();
        $this->mailer->clearReplyTos();
        $this->mailer->Subject = '';
        $this->mailer->Body = '';
        $this->mailer->AltBody = '';
        $this->mailer->isHTML(false);
        return $this;
    }

    public function getMailer() {
        return $this->mailer;
    }
}

class MailJob {
    private $mail;

    public function handle($data) {
        $this->mail = Mail::getInstance();
        
        foreach ($data['to'] as $address => $name) {
            $this->mail->to($address, $name);
        }

        $this->mail->subject($data['subject'])
                  ->body($data['body']);

        if ($data['html']) {
            $this->mail->html($data['body']);
        }

        if ($data['text']) {
            $this->mail->text($data['text']);
        }

        foreach ($data['attachments'] as $attachment) {
            $this->mail->attach($attachment['path'], $attachment['name']);
        }

        foreach ($data['embedded'] as $image) {
            $this->mail->embed($image['path'], $image['cid']);
        }

        $this->mail->send();
    }
} 