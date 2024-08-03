<?php

namespace App\Console\Commands;

use App\Models\ManageTask;
use App\Models\ManageTaskProcess;
use App\Notifications\AndroidNotification;
use Illuminate\Console\Command;

class OverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overdue:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer the scheduled task to the overdue tasks';

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
        $today = now()->startOfDay();
         $scheduletasksmanage = ManageTaskProcess::get()->unique('task_id')->pluck('task_id')->toArray();
                 
        $scheduletasks = ManageTask::whereDate('startdate', $today)->where('status', 'to-do')->whereNotIn('id', $scheduletasksmanage)->get()->pluck('id')->toArray();
        
         $scheduletasksendd = ManageTask::whereDate('enddate', $today)->where('status', '!=', 'completed')->get()->pluck('id')->toArray();
        ////dd($scheduletasks);
        
         $Task = ManageTask::whereIn('id',$scheduletasks)->orWhereIn('id',$scheduletasksendd);

            // Update material request data
            $Task->update([
                'status' => "overdue"
            ]);
            
            
     

        $this->info('Tasks updated successfully.');
    }
}
