<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WishlistNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?User $customer;
    public ?Product $product;
    public string $subjectText;
    public string $customMessage;
    public string $actionUrl;

    public function __construct(?User $customer, ?Product $product, string $subjectText, string $customMessage, string $actionUrl = '')
    {
        $this->customer = $customer;
        $this->product = $product;
        $this->subjectText = $subjectText;
        $this->customMessage = $customMessage;
        $this->actionUrl = $actionUrl;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.wishlist-notification');
    }
}
