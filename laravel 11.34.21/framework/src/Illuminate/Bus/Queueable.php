<?php

namespace Illuminate\Bus;

use Closure;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Support\Arr;
use RuntimeException;

trait Queueable
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * The name of the connection the chain should be sent to.
     *
     * @var string|null
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var string|null
     */
    public $chainQueue;

    /**
     * The callbacks to be executed on chain failure.
     *
     * @var array|null
     */
    public $chainCatchCallbacks;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    public $delay;

    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var bool|null
     */
    public $afterCommit;

    /**
     * The middleware the job should be dispatched through.
     *
     * @var array
     */
    public $middleware = [];

    /**
     * The jobs that should run if this job is successful.
     *
     * @var array
     */
    public $chained = [];

    /**
     * Set the desired connection for the job.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired connection for the chain.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function allOnConnection($connection)
    {
        $this->chainConnection = $connection;
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the chain.
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function allOnQueue($queue)
    {
        $this->chainQueue = $queue;
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired delay for the job.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Indicate that the job should be dispatched after all database transactions have committed.
     *
     * @return $this
     */
    public function afterCommit()
    {
        $this->afterCommit = true;

        return $this;
    }

    /**
     * Indicate that the job should not wait until database transactions have been committed before dispatching.
     *
     * @return $this
     */
    public function beforeCommit()
    {
        $this->afterCommit = false;

        return $this;
    }

    /**
     * Specify the middleware the job should be dispatched through.
     *
     * @param  array|object  $middleware
     * @return $this
     */
    public function through($middleware)
    {
        $this->middleware = Arr::wrap($middleware);

        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return $this
     */
    public function chain($chain)
    {
        $this->chained = collect($chain)->map(function ($job) {
            return $this->serializeJob($job);
        })->all();

        return $this;
    }

    /**
     * Serialize a job for queuing.
     *
     * @param  mixed  $job
     * @return string
     */
    protected function serializeJob($job)
    {
        if ($job instanceof Closure) {
            if (!                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     