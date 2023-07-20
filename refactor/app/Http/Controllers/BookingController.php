<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{
    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * Get jobs based on the user type.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $user = $request->__authenticatedUser;
            if ($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobs($user_id);
            } elseif ($user->user_type == env('ADMIN_ROLE_ID') || $user->user_type == env('SUPERADMIN_ROLE_ID')) {
                $response = $this->repository->getAll($request);
            } else {
                // Handle unauthorized user or other cases here if necessary.
                $response = [];
            }

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }

    }

    /**
     * Get a specific job by ID.
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try{
            $job = $this->repository->with('translatorJobRel.user')->find($id);
            return response($job);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a new job.
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->store($request->__authenticatedUser, $data);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing job.
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try{
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store job email for immediate job.
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->storeJobEmail($data);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get job history for a specific user.
     * @param Request $request
     * @return mixed|null
     */
    public function getHistory(Request $request)
    {
        try {
            if ($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response($response);
            }

            return null;
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Accept a job by the translator.
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try{
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJob($data, $user);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Accept a job by its ID.
     * @param Request $request
     * @return mixed
     */
    public function acceptJobWithId(Request $request)
    {
        try{
            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJobWithId($data, $user);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel a job by the user.
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try{
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->cancelJobAjax($data, $user);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * End a job and update related information.
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->endJob($data);
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Mark customer as not calling for a job.
     * @param Request $request
     * @return mixed
     */
    public function customerNotCall(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->customerNotCall($data);
            return response($response);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

    /**
     * Get potential jobs for a translator.
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try{
            $user = $request->__authenticatedUser;
            $response = $this->repository->getPotentialJobs($user);
            return response($response);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }

    }

    /**
     * Update distance and job-related data.
     * @param Request $request
     * @return mixed
     */
    public function distanceFeed(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->updateDistance($data);
            return response($response);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }

    }

    /**
     * Reopen a job and update related information.
     * @param Request $request
     * @return mixed
     */
    public function reopen(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->reopen($data);
            return response($response);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

    /**
     * Resend push notifications to translators.
     * @param Request $request
     * @return mixed
     */
    public function resendNotifications(Request $request)
    {
        try{
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            return response(['success' => 'Push sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

    /**
     * Resend SMS notifications to translators.
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }




}
