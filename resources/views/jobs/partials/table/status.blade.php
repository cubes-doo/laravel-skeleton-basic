@if($entity->status == \App\Models\JobStatus::STATUS_FINISHED &&
    $entity->getOutputMessage() != "OK") {{-- when finished but with errors ("OK" is set with a trackingCleanup() call --}}
<span class="text-danger" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-exclamation"></i>
    {{ $entity->getJobStatusTrans() }}
</span>
@elseif($entity->status == \App\Models\JobStatus::STATUS_FINISHED)
<span class="text-success" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-check"></i>
    {{ $entity->getJobStatusTrans() }}
</span>
@elseif($entity->status == \App\Models\JobStatus::STATUS_FAILED)
<span class="text-danger" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-remove"></i>
    {{ $entity->getJobStatusTrans() }}
</span>
@elseif($entity->status == \App\Models\JobStatus::STATUS_EXECUTING)
<span class="text-info" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-spinner"></i>
    {{ $entity->getJobStatusTrans() }}...
</span>
@elseif($entity->status == \App\Models\JobStatus::STATUS_RETRYING)
<span class="text-secondary" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-refresh"></i>
    {{ $entity->getJobStatusTrans() }}...
</span>
@else 
<span class="text-warning" title="@lang('{{ $entity->getJobStatusTrans() }}')">
    <i class="fa fa-hourglass-start"></i>
    {{ $entity->getJobStatusTrans() }}
</span>
@endif
