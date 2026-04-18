<?php

namespace App\Domain\Companies\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use App\Domain\Companies\Models\CompanyReview;

class CompanyReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $request;
    protected $review;

    public function __construct(Request $request, CompanyReview $review = null)
    {
        $this->request = $request;
        $this->review = $review;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $review = $this->review ?? new CompanyReview;
        $review->company = $this->request->input('company', '');
        $review->director = $this->request->input('director', '');
        $review->director_role = $this->request->input('director_role', '');
        if($this->request->hasFile('logo')) $review->logo = $review->uploadImage($this->request->logo);
        $review->review = $this->request->input('review', '');
        $review->published = $this->request->input('published', 0);
        $review->save();
    }
}
