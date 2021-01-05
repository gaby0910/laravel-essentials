<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Facades\App\Libraries\Notifications;

class EmailReservationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:notify 
    {count : The number of bookings to retrieve}
    {--dry-run= : To have this command do no actual work.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify reservations holders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $answer = $this->choice('What service should we use?',['sms','email'],'email');
        var_dump($answer);
        $count= $this->argument('count');
        if(!is_numeric($count)) {
            $this->alert('The count must be a number');
            return 1;
        }
        $bookings = \App\Models\Booking::with(['room.roomType','users'])->limit($count)->get();
        $this->info(sprintf('The number of bookings to alert for is: %d' , $bookings->count()));

        $bar = $this->output->createProgressBar($bookings->count());

        $bar->start();

        foreach($bookings as $booking)
        {
            if($this->option('dry-run')){
                $this->info('would process booking');
            }else{
                // $this->notify->send();
                Notifications::send();
            }
           
            $bar->advance();
            $bar->display();
        }
            $bar->finish();
            $this->comment("Command complete");
            return 0;
    }
}
