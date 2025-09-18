<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Car;

class WorkReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $car;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Car $car, string $pdfPath)
    {
        $this->car = $car;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Werkzaamheden Rapport - ' . $this->car->license_plate,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.work-report',
            with: [
                'car' => $this->car,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('werkzaamheden_' . str_replace('-', '_', $this->car->license_plate) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
