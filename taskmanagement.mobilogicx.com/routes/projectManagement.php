<?php

use App\Http\Controllers\project_management\masters\JobAndSubTaskController;
use App\Http\Controllers\project_management\MaterialRequestController;
use App\Http\Controllers\project_management\ProjectController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/project-management', 'as' => 'project-management.', 'middleware' => ['auth', 'admin']], function () {
    // Route::group(['prefix' => '/masters'], function () {
    Route::get('/jobs', [JobAndSubTaskController::class, 'jobs'])->name('jobs');
    Route::get('/sub-tasks', [JobAndSubTaskController::class, 'subtasks'])->name('sub-tasks');

    Route::post('/checkUniqueJobName', [JobAndSubTaskController::class, 'checkUniqueJobName'])->name('checkUniqueJobName');
    Route::post('/checkUniqueSubTaskName', [JobAndSubTaskController::class, 'checkUniqueSubTaskName'])->name('checkUniqueSubTaskName');

    Route::post('/jobs/store', [JobAndSubTaskController::class, 'store'])->name('jobs.store');
    Route::post('/jobs/sub-tasks/store', [JobAndSubTaskController::class, 'subtaskStore'])->name('sub-tasks.store');

    Route::post('/jobs/status', [JobAndSubTaskController::class, 'status'])->name('jobAndSubTaskStatus');
    Route::post('/sub-task/status', [JobAndSubTaskController::class, 'subTaskstatus'])->name('subTaskStatus');

    Route::get('/jobs/edit/job/{id}', [JobAndSubTaskController::class, 'edit'])->name('editJobAndTasks');
    Route::get('/sub-task/edit/job-subtasks/{id}', [JobAndSubTaskController::class, 'editSubTask'])->name('editSubTask');

    Route::post('/jobs/update', [JobAndSubTaskController::class, 'update'])->name('jobs.update');
    Route::post('/jobs/sub-tasks/update', [JobAndSubTaskController::class, 'subtaskupdate'])->name('sub-tasks.update');


    // manage subtasks and inputs

    Route::get('jobs/manage-subtasks/{id}', [JobAndSubTaskController::class, 'manageSubTasks'])->name('manageSubTasks');
    Route::post('jobs/manage-subtasks/status', [JobAndSubTaskController::class, 'manageSubTaskStatus'])->name('manageSubTaskStatus');
    Route::post('jobs/manage-subtasks/store', [JobAndSubTaskController::class, 'manageSubTasksStore'])->name('manageSubTasksStore');

    Route::get('jobs/manage-inputs/{id}', [JobAndSubTaskController::class, 'manageInputs'])->name('manageInputs');
    Route::post('jobs/manage-inputs/status', [JobAndSubTaskController::class, 'manageInputStatus'])->name('manageInputsStatus');
    Route::post('jobs/manage-inputs/store', [JobAndSubTaskController::class, 'manageInputsStore'])->name('manageInputsStore');
    // });


    Route::get('/inputs', [JobAndSubTaskController::class, 'inputs'])->name('inputs');
    Route::post('/checkUniqueInputName', [JobAndSubTaskController::class, 'checkUniqueInputName'])->name('checkUniqueInputName');
    Route::post('/inputs/store', [JobAndSubTaskController::class, 'inputsStore'])->name('inputs.store');
    Route::get('/edit/inputs/{id}', [JobAndSubTaskController::class, 'editInput'])->name('editInput');
    Route::post('/inputs/update', [JobAndSubTaskController::class, 'inputUpdate'])->name('input.update');
    Route::post('/inputs/status', [JobAndSubTaskController::class, 'inputStatus'])->name('inputStatus');








    Route::resource('/projects', ProjectController::class);
    Route::post('/projects/checkContractNumber', [ProjectController::class, 'checkContractNumber'])->name('checkContractNumber');

    Route::get('/projects/add-jobs/{id}', [ProjectController::class, 'addJobs'])->name('addJobs');

    Route::get('/projects/get-inputs/{id}', [ProjectController::class, 'getInputs'])->name('getInputs');
    Route::get('/projects/get-subDivisions/{id}', [ProjectController::class, 'getSubDivisions'])->name('getSubDivisions');
    Route::get('/projects/get-sites/{id}', [ProjectController::class, 'getSites'])->name('getSites');
    Route::get('/projects/get-site-user/{id}', [ProjectController::class, 'getSiteUser'])->name('getSiteUser');

    Route::post('/projects/store-jobs', [ProjectController::class, 'storeJob'])->name('storeJob');
    Route::get('/projects/job/{id}', [ProjectController::class, 'viewJob'])->name('viewJob');

    Route::get('/projects/daily-report/{id}', [ProjectController::class, 'viewDailyReport'])->name('dailyReport');

    Route::resource('/material-requests', MaterialRequestController::class);
});
