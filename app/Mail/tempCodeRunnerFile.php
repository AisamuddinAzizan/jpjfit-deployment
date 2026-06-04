<?php
public function __construct(
        private readonly string $subjectLine,
        private readonly string $messageBody,
        private readonly ?string $recipientName = null,