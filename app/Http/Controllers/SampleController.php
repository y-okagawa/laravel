<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepositoryInterface;

class SampleController extends Controller
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->userName();
    }
}