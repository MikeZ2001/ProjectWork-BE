<?php
    
    namespace Modules\OAuth\Events;
    
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Http\Response;
    use Illuminate\Queue\SerializesModels;
    use Symfony\Component\HttpFoundation\Response as ResponseAlias;

    class LoginSuccess
    {
        use Dispatchable;
        use InteractsWithSockets;
        use SerializesModels;
        
        public bool $isSuccess = false;
        
        /**
         * Create a new event instance.
         */
        public function __construct(public readonly object $response)
        {
            $this->isSuccess = ($response instanceof Response) && ($response->status() === ResponseAlias::HTTP_OK);
        }
        
        /**
         * Get the channels the event should be broadcast on.
         *
         * @return array<Channel>
         */
        public function broadcastOn(): array
        {
            return [
                new PrivateChannel('channel-name'),
            ];
        }
    }
