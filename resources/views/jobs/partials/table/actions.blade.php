
<div class="button-list">
    @if($entity->status == \App\Models\JobStatus::STATUS_RETRYING)
        <button class="btn btn-icon waves-effect btn-info rerun-job-btn" title="@lang('Rerun this job')" data-job-id="{{ $entity->job_id }}"><i class="mdi mdi mdi-repeat"></i></button>
    @endif 
    <a 
        href="{{route('jobs.job_status', [
            'jobStatus' => $entity->id,
        ])}}" 
        class="btn btn-icon waves-effect btn-warning"
        title="@lang('Go to the status page')"
    >
        <i class="mdi mdi-view-list"></i>
    </a>
</div>